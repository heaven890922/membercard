<?php

namespace app\index\controller;

use  \app\index\model\CardOrder as Order;
use app\index\model\Msg;
use app\index\model\OrderToken;
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
                'token' => 'require|length:32'
            ],
            [
                'money.require' => '请输入金额',
                'money./^[1-9]\d*0000$/' => '金额必须为10000的倍数(单位分)',
                'orderNum.require' => '订单号是必须的',
                'token.require' => '验证口令是必须的',
                'token.length' => '口令验证错误'
            ]
        );
        if ($validate->check($data)) {
            //检验Token口令是否正确
            $orderToken = OrderToken::get(['orderNum' => $data['orderNum']]);
            if (isset($orderToken)) {
                if ($data['token'] == $orderToken['token']) {
                    $buy = new Buy();
                    $ret = $buy->finishBuy($data['orderNum'], $data['money']);
                    $ret = Msg::getMsg($ret);
                } else {
                    $ret = Msg::getMsg(705);
                }
            } else {
                $ret = Msg::getMsg(702);
            }

        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    public function checkOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'orderNum' => 'require'
            ],
            [
                'orderNum.require' => "订单号是必须的！"
            ]
        );
        if ($validate->check($data)) {
            $order = new Order();
            $retData = $order->getOrder($data['orderNum']);
            if (isset($retData)) {
                $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
            } else {
                $ret = Msg::getMsg(702);
            }
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }
}
