<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Common extends Model
{
    /**
     * 验证用户key信息
     * @param $key          //用户api_key
     * @return bool         //返回验证信息
     */
    public function checkKey($key)
    {
        $check = Db::table("mc_apikey")->where('api_key', $key)->where('status',1)->find();
        if (!empty($check)) {
            return true;
        } else {
            return false;
        }

    }
}
