<?php

namespace app\index\model;

use think\Model;

class Shop extends Model
{

    protected $table = 'mc_shop_config';

    public function reduceRemainQuota($money)
    {
        $this->remainQuota = $this->remainQuota - $money;
        $this->totalQuota = $this->totalQuota + $money;
        $this->save();
    }
}
