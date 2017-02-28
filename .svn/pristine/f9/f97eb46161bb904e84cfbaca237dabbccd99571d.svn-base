<?php

namespace app\index\model;

use think\Model;

class CardOrder extends Model
{
    //
    protected $table = 'mc_card_order';

    /**
     * 创建订单
     * @param $data //订单信息数组包含以下字段
     * @param $customerID //用户ID
     * @param $shopID //商户ID
     * @param $money //订单金额
     * @param $methodID //支付方式
     * @param int $discount //优惠减免金额 非必须
     * @param string $remarks //备注    非必须
     * @return array
     */
    public function createOrder($data)
    {
        //组合默认字段
        $arr = ['discount' => '0', 'remarks' => ''];
        $data = array_merge($arr, $data);
        //检测必要字段是否齐全
        if (!isset($data['customerID']) || !isset($data['shopID']) || !isset($data['money']) || !isset($data['methodID'])) {
            return ['state' => false, 'code' => 401];
        }
        $customerID = (int)$data['customerID'];
        $shopID = (int)$data['shopID'];
        $money = (int)$data['money'];
        $payType = (int)$data['methodID'];
        $discount = $data['discount'];
        $remarks = $data['remarks'];
        unset($data);
        //判断是否100倍数
        if (($money % 100) != 0) {
            return ['state' => false, 'code' => 701];
        }
        //创建客户实体对象
        $user = new User($customerID);
        //检查客户充值是否受限制
        $chargeState = $user->checkChargeLimit($money);
        if (!$chargeState) {
            return ['state' => false, 'code' => $user->chargeMsgCode];
        }
        $shop = Shop::get(['shopID' => $shopID]);
        //检查商户额度是否充足
        if ($shop['remainQuota'] < $money) {
            return ['state' => false, 'code' => 621];
        }
        //获取订单号
        $orderNum = build_order_no($customerID, '3001');
        //获取当前时间
        $time = getTime();
        $this->data([
            'cardOrderNum' => $orderNum,
            'customerID' => $customerID,
            'shopID' => $shopID,
            'quota' => $money,
            'realNeedPay' => $money - $discount,
            'orderState' => 'NEW',
            'payState' => 'N',
            'payType' => $payType,
            'discount' => $discount,
            'remark' => $remarks,
            'createTime' => $time
        ]);
        $this->save();
        return ['state' => true, 'code' => 200, 'orderNum' => $orderNum];
    }
}
