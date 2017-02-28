<?php

namespace app\index\controller;


use app\index\model\Card;
use app\index\model\CardOrder;
use app\index\model\Msg;
use app\index\model\ShopAccount;
use app\index\model\Shop as S;
use app\index\model\ShopAccountDetail;
use app\index\model\ShopInfo;
use think\Db;
use think\Log;
use think\Validate;

class Shop extends Common
{
    /**
     * 检查密码是否正确
     */
    public function checkPwd()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'payPwd' => 'require'
            ],
            [
                'payPwd.require' => '请输入商户支付密码',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误"
            ]
        );
        if ($validate->check($data)) {
            $shop = new ShopAccount();
            $shopData = $shop->where('shopid', $data['shopID'])->find();
            if ($data['payPwd'] == $shopData->paypwd) {
                $ret = Msg::getMsg(['code' => 200]);
            } else {
                $ret = Msg::getMsg(['code' => 631]);
            }
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取商户首页信息
     */
    public function getInfo()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer'
            ],
            [
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
            ]
        );
        if ($validate->check($data)) {
            $shopInfo = S::get(['shopID' => $data['shopID']])->getData();
            $shopName = ShopInfo::get(['shopid' => $data['shopID']])->getAttr('shopname');
            unset($shopInfo['configID']);
            $shopInfo['shopName'] = $shopName;
            $today = date('Y-m-d 00:00:00', time());
            $shopDetail = new ShopAccountDetail();
            //今日获取会员消费收入记录信息
            $consumeData = $shopDetail->where(['shopid' => $data['shopID'], 'busTable' => 'mc_consume_order', 'changeType' => 'IN'])->where('createTime', '> time', $today);
            $consumeMoney = $consumeData->sum('money');
            if (isset($consumeMoney)) {
                $consumeNum = $shopDetail->where(['shopid' => $data['shopID'], 'busTable' => 'mc_consume_order', 'changeType' => 'IN'])->where('createTime', '> time', $today)->count();
                $cData = ['consumMoney' => $consumeMoney, 'consumeNum' => $consumeNum];
            } else {
                $cData = ['consumMoney' => 0, 'consumeNum' => 0];
            }
            $shopInfo['consumeData'] = $cData;
            //会员卡支出记录信息统计
            $payData = $shopDetail->where(['shopid' => $data['shopID'], 'busTable' => 'mc_consume_order', 'changeType' => 'OUT'])->where('createTime', '> time', $today);
            $payMoney = $payData->sum('money');
            if (isset($payMoney)) {
                $payNum = $shopDetail->where(['shopid' => $data['shopID'], 'busTable' => 'mc_consume_order', 'changeType' => 'OUT'])->where('createTime', '> time', $today)->count();
                $pData = ['payMoney' => $payMoney, 'payNum' => $payNum];
            } else {
                $pData = ['payMoney' => 0, 'payNum' => 0];
            }
            $shopInfo['payData'] = $pData;
            //会员卡充值记录统计
            $card = new Card();
            $chargeMoney = $card->where('shopID', $data['shopID'])->where('createTime', '> time', $today)->sum('quota');
            if (isset($chargeMoney)) {
                $chargeNum = $card->where('shopID', $data['shopID'])->where('createTime', '> time', $today)->count();
                $chData = ['chargeMoney' => $chargeMoney, 'chargeNum' => $chargeNum];
            } else {
                $chData = ['chargeMoney' => 0, 'chargeNum' => 0];
            }
            $shopInfo['chargeData'] = $chData;
            $ret = Msg::getMsg(['code' => 200, 'data' => $shopInfo]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取每种支付方式的统计信息
     */
    public function getPayInfo()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'payType' => 'require'
            ],
            [
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'payType.require' => '支付方式信息缺失'
            ]

        );
        if ($validate->check($data)) {
            $arr = explode(',', $data['payType']);
            $cardOrder = new CardOrder();
            $cardData = array();
            foreach ($arr as $item) {
                $cardNum = $cardOrder->where('shopID', $data['shopID'])->where('orderState', 'FINISH')->where('payType', $item)->count();
                $cardMoney = $cardOrder->where('shopID', $data['shopID'])->where('orderState', 'FINISH')->where('payType', $item)->sum('quota');
                $cardData[$item] = ['num' => $cardNum, 'money' => $cardMoney];
            }
            $ret = Msg::getMsg(['code' => 200, 'data' => $cardData]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取商户订单信息列表接口
     */
    public function getChargeOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'page' => 'require|integer'
            ],
            [
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'page' => '页数参数缺失'
            ]
        );
        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            $cardData = Db::query("SELECT o.cardOrderNum,o.payTime,o.quota,cu.tel,o.carNum,o.`name`,o.phone,m.payMethodName,o.orderState,o.payState,o.qrCodePng,o.createTime  FROM mc_card_order o   JOIN mc_pay_method m ON o.payType = m.id AND o.shopID = " . $data['shopID'] . " JOIN " . JFYCARLIFE . ".sh_coustom cu ON o.customerID= cu.coustomid ORDER BY o.createTime DESC  LIMIT " . $start . "," . PER_NUM);
            $ret = Msg::getMsg(['code' => 200, 'data' => $cardData]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 商户订单搜索（包括充值和消费订单）
     */
    public function searchOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'search' => 'require'
            ],
            [
                'search.require' => '搜索内容是必须的',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
            ]
        );

        if ($validate->check($data)) {
            if (is_numeric($data['search'])) {
                $chargeData = Db::query("call procSearchOrderByTel(:shopID,:search,:start,:num)", ['shopID' => $data['shopID'], 'search' => $data['search'], 'start' => 0, 'num' => 3]);
                $consumeData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order'  JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid AND c.tel LIKE '%" . $data['search'] . "%' JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID ORDER BY o.createTime DESC LIMIT 6");

            } else {
                $chargeData = Db::query("call procSearchOrderByCarNum(:shopID,:search,:start,:num)", ['shopID' => $data['shopID'], 'search' => $data['search'], 'start' => 0, 'num' => 3]);
                $consumeData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_car ca JOIN " . JFYCARLIFE . ".sh_coustom c ON ca.coustomID = c.coustomid JOIN mc_consume_order o ON o.customerID = ca.coustomID JOIN " . JFYCARLIFE . ".sh_shop_account_detail d ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' WHERE ca.carNum like '%" . $data['search'] . "%' GROUP BY o.consumeOrderID,d.changeType ORDER BY o.createTime DESC LIMIT 6");
            }
            if (!isset($chargeData[0])) {
                $chargeData[0] = [];
            }
            $ret = Msg::getMsg(['code' => 200, 'charge' => $chargeData[0], 'consume' => $consumeData]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取消费订单
     */
    public function getConsumeOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'page' => 'require|integer',
                'type' => 'require|in:1,2,3'
            ],
            [
                'page.integer' => '页数必须为整数',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'page.require' => '页数信息缺失',
                'type.require' => '查询类型参数缺失',
                'type.in' => '查询类型参数错误'
            ]
        );

        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            if ($data['type'] == 1) {
                $itData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' AND changeType = 'IN' JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID  ORDER BY o.createTime DESC LIMIT  " . $start . "," . PER_NUM);
                $itData = ['code' => 200, 'income' => $itData];
            } elseif ($data['type'] == 2) {
                $itData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' AND changeType = 'OUT' JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid  JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID ORDER BY o.createTime DESC LIMIT " . $start . "," . PER_NUM);
                $itData = ['code' => 200, 'expend' => $itData];
            } elseif ($data['type'] == 3) {
                $itData1 = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' AND changeType = 'IN' JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID  ORDER BY o.createTime DESC LIMIT 6");
                $itData2 = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' AND changeType = 'OUT' JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid  JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID ORDER BY o.createTime DESC  LIMIT 6");
                $itData = ['code' => 200, 'income' => $itData1, 'expend' => $itData2];
            } else {
                $itData = ['code' => 200];
            }
            $ret = Msg::getMsg($itData);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 商户搜索充值订单
     */
    public function searchChargeOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'search' => 'require',
                'page' => 'require|integer'
            ],
            [
                'search.require' => '搜索内容是必须的',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'page.require' => '页数信息参数缺失',
                'page.integer' => '页数信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            if (is_numeric($data['search'])) {
                $retData = Db::query("call procSearchOrderByTel(:shopID,:search,:start,:num)", ['shopID' => $data['shopID'], 'search' => $data['search'], 'start' => $start, 'num' => PER_NUM]);
            } else {
                $retData = Db::query("call procSearchOrderByCarNum(:shopID,:search,:start,:num)", ['shopID' => $data['shopID'], 'search' => $data['search'], 'start' => $start, 'num' => PER_NUM]);
            }

            if (!isset($retData[0])) {
                $retData[0] = [];
            }

            $ret = Msg::getMsg(['code' => 200, 'data' => $retData[0]]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 消费订单搜索
     */
    public function searchConsumeOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'search' => 'require',
                'page' => 'require|integer'
            ],
            [
                'search.require' => '搜索内容是必须的',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'page.require' => '页数信息参数缺失',
                'page.integer' => '页数信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            if (is_numeric($data['search'])) {
                $retData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_shop_account_detail d JOIN mc_consume_order o ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order'  JOIN " . JFYCARLIFE . ".sh_coustom c ON o.customerID = c.coustomid AND c.tel LIKE '%" . $data['search'] . "%' JOIN " . JFYCARLIFE . ".sh_car ca ON c.coustomid = ca.coustomID  ORDER BY o.createTime DESC LIMIT " . $start . "," . PER_NUM);
            } else {
                $retData = Db::query("SELECT o.consumeOrderNum,o.createTime,o.consumeQuota,c.tel,d.money,d.changeType,ca.carNum FROM " . JFYCARLIFE . ".sh_car ca JOIN " . JFYCARLIFE . ".sh_coustom c ON ca.coustomID = c.coustomid JOIN mc_consume_order o ON o.customerID = ca.coustomID JOIN " . JFYCARLIFE . ".sh_shop_account_detail d ON d.busid = o.consumeOrderID AND d.shopid =" . $data['shopID'] . " AND busTable = 'mc_consume_order' WHERE ca.carNum like '%" . $data['search'] . "%' GROUP BY o.consumeOrderID,d.changeType  ORDER BY o.createTime DESC LIMIT " . $start . "," . PER_NUM);
            }
            $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }
}
