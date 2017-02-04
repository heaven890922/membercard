<?php

namespace app\index\model;

use think\Model;

class Card extends Model
{
    protected $table = 'mc_card_info';
    public function cardPay($money, $orderID){
        $remain = $this->currentQuota - $money;
        $data = ['currentQuota' => $remain];
        if ($remain <= 0) {
            $data = array_merge($data, ['state' => 'OFF']);
        }
        //记录卡消费记录LOG
        $cardLog = new CardLog();
        $logData =[
            'cardID' => $this->cardID,
            'money' => $money,
            'BeforeMoney' => $this->currentQuota,
            'orderID' => $orderID,
            'logType' => '1'
        ];
        $cardLog->isUpdate(false)->save($logData);
        $shopAccount = ShopAccount::get(['shopID' => $this->shopID]);
        $shopAccount->decreaseBalance($money, $orderID);
        $this->isUpdate(true)->save($data);
    }
}
