<?php
use \think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::rule('login','index/index/login','GET|POST');
Route::rule('index','index/index/index','GET|POST');
Route::rule('order/create','index/CardOrder/createOrder','GET|POST');
Route::rule('pay/methods','index/PayMethod/get','GET|POST');
Route::rule('pay/wxpay','index/PayMethod/wxPay','GET|POST');
Route::rule('pay/cashpay','index/PayMethod/cashPay','GET|POST');
Route::rule('qrcode/create','index/QrCode/create','GET|POST');
Route::rule('qrcode/check','index/QrCode/check','GET|POST');
Route::rule('qrcode/test','index/QrCode/test','GET|POST');
Route::rule('order/finish','index/CardOrder/finish','GET|POST');
Route::rule('shop/checkPwd','index/Shop/checkPwd','GET|POST');
/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/


