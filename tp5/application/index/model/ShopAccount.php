<?php

namespace app\index\model;

use think\Model;

class ShopAccount extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sh_shop_account';

    // 设置当前模型的数据库连接
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => HOSTNAME,
        // 数据库名
        'database'    => JFYCARLIFE,
        // 数据库用户名
        'username'    => 'huangks',
        // 数据库密码
        'password'    => '000000!@#',
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
        // 数据库调试模式
        'debug'       => false,
    ];

    /**
     * 充值补给商户可提现余额并写入detail记录表
     * @param $money        //补给金额
     * @param $orderID      //充值订单id
     */
    public function increaseBalance($money, $orderID, $orderTable = 'mc_card_order', $source = 'CARD_CHARGE', $remark = '会员卡充值')
    {
        if(is_numeric($money)){
            $time = getTime();
            $afterMoney = $this->balance + $money;
            $log = new ShopAccountDetail();
            $log->data([
                'createTime' => $time,
                'typeID' => 1,
                'beforeMoney' => $this->balance,
                'afterMoney' => $afterMoney,
                'remark' => $remark,
                'shopid' => $this->shopID,
                'busTable' => $orderTable,
                'busid' => $orderID,
                'source' => $source,
                'changeType' => 'IN',
                'money' => $money,
                'state' => 'FINISH',
                'processTime' => $time
            ]);
            $log->save();
            $this->balance = $afterMoney;
            $this->save();
        }
    }

    /**
     * 扣除小车商户账户可提现余额并记录detail
     * @param $money
     * @param $orderID
     */
    public function decreaseBalance($money, $orderID)
    {
        if (is_numeric($money)) {
            $time = getTime();
            $afterMoney = $this->balance - $money;
            $log = new ShopAccountDetail();
            $log->data([
                'createTime' => $time,
                'typeID' => 1,
                'beforeMoney' => $this->balance,
                'afterMoney' => $afterMoney,
                'remark' => '会员卡消费支出',
                'shopid' => $this->shopID,
                'busTable' => 'mc_consume_order',
                'busid' => $orderID,
                'source' => 'CARD_PAY',
                'changeType' => 'OUT',
                'money' => $money,
                'state' => 'FINISH',
                'processTime' => $time
            ]);
            $log->isUpdate(false)->save();
            $this->balance = $afterMoney;
            $this->save();
        }
    }
}
