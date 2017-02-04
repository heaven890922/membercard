<?php

namespace app\index\model;

use think\Model;

class ConsumeOrder extends Model
{
    protected $table = 'mc_consume_order';

    public function createOrder($orderNum, $customerID, $shopID, $money, $remark ='', $payType = '1')
    {
        $time = getTime();
        $this->data([
            'consumeOrderNum' => $orderNum,
            'customerID' => $customerID,
            'shopID' => $shopID,
            'consumeQuota' => $money,
            'orderState' => 'NEW',
            'payState' => 'N',
            'payType' => $payType,
            'remark' => $remark,
            'createTime' => $time
        ]);
        $this->isUpdate(false)->save();
        return $this->consumeOrderID;
    }

    public function finishOrder($orderID)
    {
        $time = getTime();
        $data = [
            'consumeOrderID' => $orderID,
            'orderState' => 'FINISH',
            'payState' => 'N',
            'payTime' => $time
        ];
        $this->data($data, true)->isUpdate(true)->save();
    }
}
