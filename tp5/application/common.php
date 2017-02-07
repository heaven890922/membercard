<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function p($var)
{
    if (is_bool($var)) {
        var_dump($var);
    } else if (is_null($var)) {
        var_dump(null);
    } else {
        echo "<pre style='position: relative; z-index: 1000; padding: 10px; border-radius: 5px; background: #F5F5F5; border: 1px solid #aaa;font-size: 18px; line-height: 24px; opacity: 0.9;'>" . print_r($var, true) . "</pre>";
    }
}

function pJson($var)
{
    if (is_bool($var)) {
        var_dump($var);
    } else if (is_null($var)) {
        var_dump(null);
    } else {
        echo json_encode($var);
    }
}


function build_order_no($userID = 100, $prefix = '')
{
    return $prefix . date('YmdHis') . sprintf("%04d", substr(microtime(true), strpos(microtime(true), ".") + 1)) . substr($userID, -3) . rand(100, 999);
}



function getTime()
{
    return date('Y-m-d H:i:s', time());
}