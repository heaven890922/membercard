<?php

namespace app\index\controller;
use app\index\model\CardOrder;
use app\index\model\PayMethod as Method;
use think\Db;

class PayMethod extends Common
{
    //设置前置操作
    protected $beforeActionList = [
        'checkKey' =>['except' => 'wxPay,cashPay,aliPay'],
    ];
    /*
     * 获取支付方式列表
     */
    public function getPayMethods(){
        $payMethods = Db::table("mc_pay_method")->where("state",1)->order('sort')->select();
        return $payMethods;
    }

    /**
     * 获取支付方式列表并返回结果
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function get()
    {
        return $this->getPayMethods();
    }

    /**
     * 获取支付链接
     * @param $dataInfo
     * @return array
     */
    public function getPayUrl($dataInfo)
    {
        $ret = ['state' => false,'code' => 402, 'msg' =>'支付方式错误' ];
        $payMethods = $this->getPayMethods();
        $cardOrder = new CardOrder();
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
            $ret = ['state' => true, 'code' => 200, 'url' => $url];
        }
        return $ret;
    }


    public function wxPayUrl($dataInfo)
    {
        $time = time() + VALID_TIME;
        $url = "http://xcsh.card.com/pay/wxpay/money/".$dataInfo['money']."/remarks/".$dataInfo['remarks']."/orderNum/".$dataInfo['orderNum']."/expireTime/".$time;
        return $url;
    }


    public function aliPayUrl($dataInfo)
    {
        return "http://www.baidu.com";
    }


    public function cashPayUrl($dataInfo)
    {
        $time = time() + VALID_TIME;
        $url = "http://xcsh.card.com/pay/cashpay/money/".$dataInfo['money']."/remarks/".$dataInfo['remarks']."/orderNum/".$dataInfo['orderNum']."/expireTime/".$time;
        return $url;
    }

    public function wxPay()
    {
        $data = $this->request->param();
        if ($data['expireTime'] < time()) {
            $this->error('超出二维码有效时间！');
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function cashPay()
    {
        return $this->fetch();
    }

    public function aliPay()
    {

    }
}
