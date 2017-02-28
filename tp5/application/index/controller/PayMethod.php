<?php

namespace app\index\controller;

use app\index\model\Buy;
use app\index\model\CardOrder;
use app\index\model\Customer;
use app\index\model\Msg;
use app\index\model\ShopInfo;
use think\Db;
use think\Validate;

class PayMethod extends Common
{
    //设置前置操作
    protected $beforeActionList = [
        'checkKey' => ['except' => 'wxPay,cashPay,aliPay'],
    ];

    /*
     * 获取支付方式列表
     */
    public function getPayMethods()
    {
        $payMethods = Db::table("mc_pay_method")->where("state", 1)->order('sort')->select();
        return $payMethods;
    }

    /**
     * 获取支付方式列表并返回结果
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function get()
    {
        $payMethods = Db::table("mc_pay_method")->where("state", 1)->order('sort')->field('id,payMethodName,sort')->select();
        $ret = Msg::getMsg(['code' => 200, 'methods' => $payMethods]);
        pJson($ret);
    }

    /**
     * 获取支付链接
     * @param $dataInfo
     * @return array
     */
    public function getPayUrl($dataInfo)
    {
        $ret = ['state' => false, 'code' => 402, 'msg' => '支付方式错误'];
        $payMethods = $this->getPayMethods();
        $cardOrder = new CardOrder();
        $dataInfo = array_merge(['remarks' => '暂无备注'],$dataInfo);
        //p($dataInfo);
        $orderRet = $cardOrder->createOrder($dataInfo);
        if (!$orderRet['state']) {
            return $orderRet;
        } else {
            unset($orderRet['state']);
            unset($orderRet['code']);
            $dataInfo = array_merge($dataInfo, $orderRet);
        }
        foreach ($payMethods as $items) {
            if ($items['id'] == $dataInfo['methodID']) {
                $url = $this->$items['port']($dataInfo);
            }
        }
        if (isset($url)) {
            $ret = ['state' => true, 'code' => 200, 'url' => $url, 'orderNum' => $orderRet['orderNum']];
        }
        return $ret;
    }


    public function wxPayUrl($dataInfo)
    {
        $time = time() + VALID_TIME;
        $url = QR_WEB_SITE."/pay/wxpay/money/" . $dataInfo['money'] . "/remarks/" . $dataInfo['remarks'] . "/orderNum/" . $dataInfo['orderNum'] . "/expireTime/" . $time;
        return $url;
    }

    /**
     * 获取支付宝支付链接
     * @param $dataInfo
     * @return string
     */
    public function aliPayUrl($dataInfo)
    {
        return "http://www.baidu.com";
    }

    /**获取现金支付链接
     * @param $dataInfo
     * @return string
     */
    public function cashPayUrl($dataInfo)
    {

        $time = time() + VALID_TIME;
        $url = QR_WEB_SITE."/pay/cashpay/money/" . $dataInfo['money'] . "/remarks/" . $dataInfo['remarks']  . "/orderNum/" . $dataInfo['orderNum'] . "/expireTime/" . $time;
        return $url;
    }

    /**
     * 微信支付页面
     * @return mixed
     */
    public function wxPay()
    {
        $data = $this->request->param();
        $data = array_merge(['remarks' => '暂无备注'], $data);
        $validate = new Validate(
            [
                'money' => 'require|/^[1-9]\d*00$/',
                'orderNum' => 'require',
                'expireTime' => 'require'
            ],
            [
                'money.require' => '请输入金额',
                'money./^[1-9]\d*0000$/' => '金额必须为100的倍数(单位元)',
                'orderNum.require' => '订单号缺失',
                'expireTime' => '信息缺失'
            ]
        );
        if ($validate->check($data)) {
            if ($data['expireTime'] < time()) {
                $this->errorInfo('超出二维码有效时间', '请让商户重新生成二维码！');
            }
            $cardOrder = CardOrder::get(['cardOrderNum' => $data['orderNum']]);
            if (isset($cardOrder)) {
                $shopName = ShopInfo::get(['shopid' => $cardOrder['shopID']])->getAttr('shopname');
                $data['shopName'] = $shopName;
                $data['orderTime'] = $cardOrder['createTime'];
                $this->assign('data', $data);
                return $this->fetch();
            } else {
                $this->errorInfo('订单号错误', '订单号错误或者订单号不存在，请重新操作或者咨询客服！');
            }
        } else {
            $this->errorInfo('参数错误', $validate->getError());
        }
        return $this->fetch();
    }

    /**
     * 现金支付
     * @return mixed
     */
    public function cashPay()
    {
        $data = $this->request->param();
        if ($data['expireTime'] < time()) {
            $this->errorInfo('超出二维码有效时间', '请让商户重新生成二维码！');
        }
        if (isset($data['customerID'])) {
            $cardOrder = CardOrder::get(['cardOrderNum' => $data['orderNum']])->getAttr('customerID');
            if (isset($cardOrder) && $cardOrder == $data['customerID']) {
                //检验apiToken口令是否正确
                //p($data['money']);
                $validate = new Validate(
                    [
                        'money' => 'require|/^[1-9]\d*00$/',
                        'orderNum' => 'require',
                    ],
                    [
                        'money.require' => '请输入金额',
                        'money./^[1-9]\d*0000$/' => '金额必须为100的倍数(单位元)',
                    ]
                );
                if ($validate->check($data)) {
                    $buy = new Buy();
                    $tokenRet = $this->checkApiToken($data['apiToken'], __CLASS__);
                    if ($tokenRet['state'] === true) {
                        $ret = $buy->finishBuy($data['orderNum'], ($data['money'] * 100));
                        $ret = Msg::getMsg($ret);
                        if ($ret['code'] == 200) {
                            //p($ret);
                            //成功之后跳转
                            $this->redirect("http://www.baidu.com");
                        } else {
                            $this->errorInfo('支付失败', $ret['msg']);
                        }
                    } else {
                        $ret = Msg::getMsg($tokenRet);
                        $this->errorInfo('密钥错误', $ret['msg']);
                    }

                } else {
                    $this->errorInfo('参数错误', $validate->getError());
                }
            } else {
                //错误跳转页面，待修改
                $this->errorInfo('错误微信号', '请用正确的微信号扫描该二维码！');
            }
        } else {
            $data['customerID'] = 0;
            $cardOrder = CardOrder::get(['cardOrderNum' => $data['orderNum']]);
            $shopName = ShopInfo::get(['shopid' => $cardOrder['shopID']])->getAttr('shopname');
            $data['shopName'] = $shopName;
            $data['orderTime'] = $cardOrder['createTime'];
            if (isset($data['oid'])) {
                $customer = Customer::get(['wechatid' => $data['oid']])->getAttr('coustomid');
                if (isset($cardOrder) && $cardOrder['customerID'] == $customer) {
                    $data['customerID'] = $customer;
                } else {
                    //错误跳转页面，待修改
                    $this->errorInfo('错误微信号', '请用正确的微信号扫描该二维码！');
                }
            }
            $this->assign('data', $data);
            return $this->fetch();
        }
        return $this->fetch();
    }

    /**
     * 支付宝支付
     */
    public function aliPay()
    {

    }


}
