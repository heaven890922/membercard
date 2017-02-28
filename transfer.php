<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-02-08
 * Time: 14:06
 */
$str = $_REQUEST['str'];
$oid = $_REQUEST['oid'];
$data = json_decode($str);

$url = "http://192.168.1.200/membercard/tp5/public/index.php/pay/cashpay/expireTime/".$data->expireTime."/orderNum/".$data->orderNum."/money/".$data->money."/oid/".$oid;
Header("Location:$url");