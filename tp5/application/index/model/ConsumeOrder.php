<?php

namespace app\index\model;

use think\Db;
use think\Model;

class ConsumeOrder extends Model
{
    protected $table = 'mc_consume_order';
    /**
     * 创建会员卡支付订单
     * @param $orderNum
     * @param $customerID
     * @param $shopID
     * @param $money
     * @param string $remark
     * @param string $payType
     * @return mixed
     */
    
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

    /**
     * 完成订单
     * @param $orderID
     */
    public function finishOrder($orderID)
    {
        $time = getTime();
        $data = [
            'consumeOrderID' => $orderID,
            'orderState' => 'FINISH',
            'payState' => 'Y',
            'payTime' => $time
        ];
        $this->data($data, true)->isUpdate(true)->save();
    }

    public function getOrder($orderNum)
    {
        $data = Db::query("SELECT o.consumeQuota,o.createTime,s.shopname,o.remark FROM mc_consume_order o JOIN ".JFYCARLIFE.".sh_shop s ON o.shopID = s.shopid AND o.consumeOrderNum =:orderNum",['orderNum' => $orderNum]);
        return $data;
    }
}
