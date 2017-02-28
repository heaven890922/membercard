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
Route::rule('error','index/ErrorInfo/index','GET|POST');
Route::rule('index','index/index/index','GET|POST');
Route::rule('order/create','index/CardOrder/createOrder','GET|POST');
Route::rule('pay/methods','index/PayMethod/get','GET|POST');
Route::rule('pay/wxpay','index/PayMethod/wxPay','GET|POST');
Route::rule('pay/cashpay','index/PayMethod/cashPay','GET|POST');
Route::rule('order/finish','index/CardOrder/finish','GET|POST');
Route::rule('order/check','index/CardOrder/checkOrder','GET|POST');
//商户模块的路由
Route::rule('shop/checkPwd','index/Shop/checkPwd','GET|POST');
Route::rule('shop/info','index/Shop/getInfo','GET|POST');
Route::rule('shop/count','index/Shop/getPayInfo','GET|POST');
Route::rule('shop/order','index/Shop/getChargeOrder','GET|POST');
Route::rule('shop/search','index/Shop/searchOrder','GET|POST'); //订单查询
Route::rule('shop/consume','index/Shop/getConsumeOrder','GET|POST'); //消费订单列表
Route::rule('shop/searchcharge','index/Shop/searchChargeOrder','GET|POST');
Route::rule('shop/searchconsume','index/Shop/searchConsumeOrder','GET|POST');
//二維碼模塊路由
Route::rule('qrcode/create','index/QrCode/create','GET|POST');
Route::rule('qrcode/check','index/QrCode/check','GET|POST');
Route::rule('qrcode/test','index/QrCode/test','GET|POST');
Route::rule('qrcode/moneycheck','index/QrCode/moneyCheck','GET|POST'); //检查金额是否能创建二维码

//用户模块的路由
Route::rule('user/setpwd','index/User/setPwd','GET|POST'); //用户设置密码路由
Route::rule('user/ispwd','index/User/checkPwdSet','GET|POST'); // 检查用户是否设置密码
Route::rule('user/bindcar','index/User/bindCarNum','GET|POST');//用户车牌绑定
Route::rule('user/iscar','index/User/checkCarNum','GET|POST');//检查用户是否绑定车牌
Route::rule('user/info','index/User/getInfo','GET|POST');//获取用户基本信息
Route::rule('user/pay','index/User/pay','GET|POST');//获取用户基本信息
Route::rule('user/payorder','index/User/getPayOrder','GET|POST');//获取支付订单记录
Route::rule('user/chargeorder','index/User/getBuyOrder','GET|POST');//获取充值订单记录
Route::rule('user/payorderinfo','index/User/getPayOrderInfo','GET|POST');//获取支付订单信息（单个）
Route::rule('user/buyorderinfo','index/User/getChargeOrderInfo','GET|POST');//获取充值订单信息（单个）
Route::rule('user/shoplistbyd','index/User/getShopListByDistance','GET|POST');//获取商户列表（附近）
Route::rule('user/shoplistbyarea','index/User/getShopListByArea','GET|POST');//获取商户列表（区域）
Route::rule('user/shoplistbysearch','index/User/getShopListBySearch','GET|POST');//获取商户列表（区域）
Route::rule('user/getarea','index/User/getAreaList','GET|POST');//获取商户列表（区域）


/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/


