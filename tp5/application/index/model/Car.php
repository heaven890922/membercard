<?php

namespace app\index\model;

use think\Model;

class Car extends Model
{
    protected $table = 'sh_car';

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
}
