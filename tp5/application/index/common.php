<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-01-12
 * Time: 16:37
 */

/**
 * @param $className  //控制器名称
 * @param string $secretKey //生成api 口令的密钥
 * @param string $apiToken  //传入过来的 apiToken 值
 */
function check_api_token( $className , $apiToken , $secretKey = API_SECRET_KEY )
{
    $date =date("Y-m-d",time());
    //校验apiToken值是否正确
    $str = md5($className.$date.$secretKey);
    if($str == $apiToken)
    {
        return true;
    }else{
        return false;
    }
}