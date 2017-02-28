<?php

namespace app\index\controller;

use  \app\index\model\CardOrder as Order;
use app\index\model\Msg;
use think\Validate;
use app\index\model\Buy;

class CardOrder extends Common
{
    /**
     * 创建订单
     */
    public function createOrder()
    {
        $data = $this->request->param();
        $cardOrder = new Order();
        //创建订单
        $ret = $cardOrder->createOrder($data);
        $msg = new Msg();
        $message = $msg->getMsg($ret);
        p($message);
    }

    /**
     * 完成充值订单
     */
    public function finish()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'money' => 'require|/^[1-9]\d*0000$/',
                'orderNum' => 'require',
            ],
            [
                'money.require' => '请输入金额',
                'money./^[1-9]\d*0000$/' => '金额必须为10000的倍数(单位分)',
            ]
        );
        if ($validate->check($data)) {
            //检验apiToken口令是否正确
            $tokenRet = $this->checkApiToken($data['apiToken'], __CLASS__);
            if ($tokenRet['state'] === true) {
                $buy = new Buy();
                $ret = $buy->finishBuy($data['orderNum'], $data['money']);
                $ret = Msg::getMsg($ret);
            } else {
                $ret = Msg::getMsg($tokenRet);
            }
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }
}
