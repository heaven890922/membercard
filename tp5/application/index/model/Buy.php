<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Buy extends Model
{
    //获取支付信息
    public function getBuyMethod()
    {
        $methodData = Db::table("mc_pay_method")->where('state', 1)->order('sort asc')->select();
        return $methodData;
    }

    /**
     * 检测是否满足支付要求
     * @param $obj
     * @return array
     */
    public function checkBuy($obj)
    {
        $userID = $obj['customerID'];
        $orderNum = $obj['cardOrderNum'];   //订单号
        //$payType = $obj['payType'];     //支付方式
        $total_fee = $obj['quota']; //支付金额
        $order = Db::table("mc_card_order")->where('cardOrderNum', $orderNum)->find();
        if (isset($order)) {
            if (($order['quota'] - $order['discount']) == $total_fee && $order['payState'] == 'N') {
                //客户支付限制
                $user = new User($userID);
                if ($user->checkChargeLimit($total_fee)) {
                    return ['state' => true, 'code' => 200];
                } else {
                    return ['state' => false, 'code' => $user->chargeMsgCode];
                }
            } else {
                return ['state' => false, 'code' => 701];
            }
        } else {
            return ['state' => false, 'code' => 702];
        }

    }

    /**
     * 订单支付完成
     * @param $obj
     * @param $money //订单金额（单位分）
     * @return array
     */
    public function finishBuy($obj, $money)
    {
        if (!is_array($obj) || !isset($obj['cardOrderID'])) {
            $order = Db::table('mc_card_order')->where('cardOrderNum', $obj)->find();
        } else {
            $order = $obj;
        }

        if ($order['payState'] == 'N' && ($money == ($order['realNeedPay'] * 100))) {

            //检查客户是否满足支付调教
            $checkRet = $this->checkBuy($order);
            if (!$checkRet['state']) {
                return $checkRet;
            }

            $time = getTime();

            //查询商户发卡人信息以及发卡余额信息
            $shop = Shop::get(['shopID' => $order['shopID']]);

            //检查商户额度是否充足
            if ($shop['remainQuota'] < $order['quota']) {
                return ['state' => false, 'code' => 621];
            }

            //获取订单对象用于更新订单信息
            $cardOrder = CardOrder::get($order['cardOrderID']);

            //创建会员卡对象用于创建新会员卡
            $card = new Card();

            //获取支付方式是否补给商户可提现状态
            $payMethod = Db::table('mc_pay_method')->where('id', $cardOrder['payType'])->field('supply')->find();
            $supplyState = $payMethod['supply'];

            //创建商户账户对象
            $shopAccount = ShopAccount::get(['shopID' => $cardOrder['shopID']]);
            //事务开始
            Db::startTrans();
            try {
                //会员卡生成
                $card->data([
                    'virtualCardNum' => '20170119175000131',
                    'customerID' => $cardOrder['customerID'],
                    'shopID' => $cardOrder['shopID'],
                    'orderID' => $cardOrder['cardOrderID'],
                    'quota' => $cardOrder['quota'],
                    'currentQuota' => $cardOrder['quota'],
                    'state' => 'ON',
                    'name' => $shop['name'],
                    'phone' => $shop['phone'],
                    'createTime' => $time
                ]);
                $card->save();
                //商户更新
                if ($supplyState) {
                    //补给商户可提现并记录detail
                    $shopAccount->increaseBalance($cardOrder['quota'], $cardOrder['cardOrderID']);
                } else {
                    //不补给商户提现
                }

                //商户发卡余额更新（减少）
                $shop->reduceRemainQuota($cardOrder['quota']);

                //订单状态更新
                $cardOrder->payState = 'Y';
                $cardOrder->orderState = 'FINISH';
                $cardOrder->payTime = $time;
                $cardOrder->paid = $cardOrder['quota'] - $cardOrder['discount'];
                $cardOrder->save();

                Db::commit();
                return ['state' => true, 'code' => 200];
            } catch (\Exception $e) {
                Db::rollback();
                return ['state' => false, 'code' => 704];
            }


        } else {
            return ['state' => false, 'code' => 703];
        }

    }
}
