<?php

namespace app\index\model;



use think\Db;

class User
{
    private $userID;            //用户id customerID
    private $cardData;          //用户可用卡片的信息二位数组
    private $balance;           //用户余额
    public  $payMsgCode;        //支付限制返回码
    public  $chargeMsgCode;     //充值限制返回码
    private  $limitData;        //限制参数对象
    private $payPwd;
    /**
     * User constructor.
     * @param $userID //用户id
     */
    public function __construct($userID)
    {
        $this->userID = $userID;
        $this->createDate();
    }

    /**
     * 获取用户卡片信息并统计余额
     */
    private function createDate()
    {
        $this->cardData = $cardData = Db::table('mc_card_info')->where('customerID', $this->userID)->where('state', 'ON')->where('currentQuota', '>', 0)->order('createTime ASC')->select();
        $this->balance = 0;
        if (!empty($cardData)) {
            foreach ($cardData as $item) {
                $this->balance += $item['currentQuota'];
            }
        }
        $payPwd = Customer::get(['coustomid' => $this->userID]);
        $this->payPwd = $payPwd['pwd'];
    }

    /**
     * 获取限制配置参数
     */
    public function getLimitData()
    {
        if (isset($this->limitData)) {
            $limitConfig = $this->limitData;
        } else {
            $limitConfig = LimitConfig::get(1);
            $this->limitData;
        }
        return $limitConfig;
    }


    /**
     * 检测支付限制
     * 累计支付金额 + 本次支付金额 > 支付金额限制  判定能否支付
     * @param $money        //本次支付的金额
     * @return bool         //返回结果
     */
    public function checkPayLimit($money)
    {
        if (is_numeric($money)) {
            if ($this->balance < $money) {
                $this->payMsgCode = 613;
                return false;
            }

            $dayStart = date('Y-m-d 00:00:00', time());
            $monthStart = date('Y-m-01', time());
            //日支付金额统计
            $dayPay = Db::table('mc_consume_order')->where('customerID', $this->userID)->where('payState', 'Y')->where('createTime', '>= time', $dayStart)->sum('consumeQuota');
            //月支付金额统计
            $monthPay = Db::table('mc_consume_order')->where('customerID', $this->userID)->where('payState', 'Y')->where('createTime', '>= time', $monthStart)->sum('consumeQuota');

            //获取限制参数
            $limitConfig = $this->getLimitData();
            if (($dayPay + $money > $limitConfig->dayPayLimit) || ( ($a = 1) && ($monthPay +$money > $limitConfig->monthPayLimit)) ) {

                if (isset($a)) {
                    $this->payMsgCode = 612;    //月流水超过限制
                } else {
                    $this->payMsgCode = 611;    //日支付超过限制
                }

                return false;
            } else {
                return true;
            }

        } else {
            $this->payMsgCode = 402;
            return false;
        }

    }

    /**
     * 检查用户充值是否限制
     * @param $money        //充值金额
     * @return bool
     */
    public function checkChargeLimit($money)
    {
        if (is_numeric($money)) {

            //获取限制参数
            $limitConfig = $this->getLimitData();

            if ($this->balance + $money > $limitConfig->balanceLimit) {
                $this->chargeMsgCode = 601; //余额超过限制
                return false;
            }
            $dayStart = date('Y-m-d 00:00:00', time());

            //日充值金额统计
            $dayCharge = Db::table('mc_card_order')->where('customerID', $this->userID)->where('payState', 'Y')->where('payTime', '>= time', $dayStart)->sum('quota');
            //p($dayCharge);
            //p($limitConfig->dayChargeLimit);
            if ($dayCharge + $money > $limitConfig->dayChargeLimit) {
                $this->chargeMsgCode = 602; //单日充值金额超过限制
                return false;
            } else {
                return true;
            }

        } else {
            $this->chargeMsgCode = 402;
            return false;
        }

    }
    /**
     * 获取用户余额
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * 用户会员卡支付
     * @param $money            //支付金额
     * @param $payPwd           //支付密码
     * @param $shopID           //支付商户
     * @return array
     */
    public function pay($money , $payPwd, $shopID)
    {
        if(!is_numeric($money) || !isset($payPwd) || !is_numeric($shopID)){
            return ['state' => false, 'code' => 402];
        }
        //检查密码是否正确
        $pwdRet = $this->checkPwd($payPwd);
        if (!$pwdRet['state']) {
            return $pwdRet;
        }

        //检查金额是否受限制
        $payCheckRet = $this->checkPayLimit($money);
        if ($payCheckRet) {
            $orderNum = build_order_no($this->userID);

            // 创建消费订单对象
            $consumeOrder = new ConsumeOrder();

            Db::startTrans();
            try {
                $orderID = $consumeOrder->createOrder($orderNum, $this->userID, $shopID, $money);
                //= $consumeOrder->consumeOrderID;
                $remainPay = $money;
                foreach ($this->cardData as $items) {
                    if ($remainPay > 0) {
                        $card = Card::get($items['cardID']);
                        if ($remainPay >= $card['currentQuota']) {
                            $remainPay = $remainPay - $card['currentQuota'];
                            $thisToPay = $card['currentQuota'];
                        } else {
                            $thisToPay = $remainPay;
                            $remainPay = 0;
                        }
                        //卡片支付金额并记录log，发卡商户扣除可提现并记录detail
                        $card->cardPay($thisToPay, $orderID);
                        //恢复发卡商户可发卡余额
                        $shop = Shop::get(['shopID' => $items['shopID']]);
                        $shop->increaseRemainQuota($money);
                    } else {
                        break;
                    }
                }
                //更新订单状态
                $consumeOrder->finishOrder($orderID);
                //创建商户对象
                $shopAccount = ShopAccount::get(['shopID' => $shopID]);
                //给提供服务商户增加可提现余额
                $shopAccount->increaseBalance($money, $orderID, 'mc_consume_order', 'CARD_PAY', '会员卡消费收入');
                DB::commit();
                return ['state' => true, 'code' => 200, 'orderNum' => $orderNum];
            } catch (\Exception $e) {
                p($e);
                Db::rollback();
                return ['state' => false, 'code' => 704];
            }
        } else {
            return ['state' => false, 'code' => $this->payMsgCode];
        }

    }

    /**
     * 校验用户支付密码
     * @param $payPwd           //支付密码
     * @return array            //返回结果
     */
    public function checkPwd($payPwd)
    {
        if ($this->payPwd == $payPwd) {
            return ['state' => true];
        } else {
            return ['state' => false, 'code' => 801];
        }
    }

}
