<?php

namespace app\index\controller;

use  \app\index\model\CardOrder as Order;
use app\index\model\Msg;

class CardOrder extends Common
{
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
}
