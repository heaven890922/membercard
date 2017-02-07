<?php

namespace app\index\model;

class Msg
{
    public static $msg = array();

    /**
     * 101 系统错误，未生成返回信息
     * @param $code -返回参数状态码
     * 200  -处理成功
     *
     * ---3开头的都是口令校验
     * 301  -apiToken 校验错误
     * 302  -apiToken 缺失
     * -31 key错误
     * 311  -api_key 错误
     * ---4开头的都是参数问题
     * 401  -参数缺失
     * 402  -参数错误
     *
     * --5开头的是系统错误
     * 501 --生成apiToken错误
     *
     * --6开头业务限制
     *  -60客户充值限制
     * 601  -余额超过3000
     * 602  -单日充值1000限制
     * -61客户支付限制
     * 611  -单日支付限制
     * 612  -月支付限制
     * 613  -余额不足
     * -62商户发放限制
     * 621  -商户发放余额不足
     * -63 商户验证问题
     * 631 商户支付密码验证错误
     * --7订单问题
     * 701  -金额错误或者已支付
     * 702  -查无订单，订单号错误
     * 703  -已完成，不能重复结算或者金额不对
     * 704  -系统错误，订单支付失败
     * --8用户错误
     * 801  -用户密码错误
     * 802  -用户不存在
     * 803  -未绑定车牌号码
     */
    public static function createMsg($code)
    {

        switch ($code) {
            case 200:
                self::$msg = ['code' => $code, 'msg' => '成功'];
                break;
            case 301:
                self::$msg = ['code' => $code, 'msg' => '口令错误'];
                break;
            case 302:
                self::$msg = ['code' => $code, 'msg' => '口令缺失'];
                break;
            case 311:
                self::$msg = ['code' => $code, 'msg' => 'api_key错误'];
                break;
            case 401:
                self::$msg = ['code' => $code, 'msg' => '参数缺失'];
                break;
            case 402:
                self::$msg = ['code' => $code, 'msg' => '参数错误'];
                break;
            case 501:
                self::$msg = ['code' => $code, 'msg' => '口令生成错误'];
                break;
            case 601:
                self::$msg = ['code' => $code, 'msg' => '余额受限'];
                break;
            case 602:
                self::$msg = ['code' => $code, 'msg' => '单日充值受限'];
                break;
            case 611:
                self::$msg = ['code' => $code, 'msg' => '单日支付受限'];
                break;
            case 612:
                self::$msg = ['code' => $code, 'msg' => '月支付受限'];
                break;
            case 613:
                self::$msg = ['code' => $code, 'msg' => '余额不足'];
                break;
            case 621:
                self::$msg = ['code' => $code, 'msg' => '商户发卡余额不足'];
                break;
            case 631:
                self::$msg = ['code' => $code, 'msg' => '商户支付密码验证失败'];
                break;
            case 701:
                self::$msg = ['code' => $code, 'msg' => '金额错误或者已支付'];
                break;
            case 702:
                self::$msg = ['code' => $code, 'msg' => '查无订单'];
                break;
            case 703:
                self::$msg = ['code' => $code, 'msg' => '订单已支付'];
                break;
            case 704:
                self::$msg = ['code' => $code, 'msg' => "系统错误，订单支付失败"];
                break;
            case 801:
                self::$msg = ['code' => $code, 'msg' => '密码错误'];
                break;
            case 802:
                self::$msg = ['code' => $code, 'msg' => '未注册小车生活账号'];
                break;
            case 803:
                self::$msg = ['code' => $code, 'msg' => '未绑定车牌号码'];
                break;
            default:
                self::$msg = ['code' => 101, 'msg' => '系统错误'];
                break;
        }
    }

    /**
     * @param $msg --自定义返回参数 --数组
     * @return array --返回参数
     */
    public static function getMsg($msg = [])
    {
        if (is_array($msg)) {
            unset($msg['state']);
            self::createMsg($msg['code']);
            $retMsg = array_merge(self::$msg, $msg);
            return $retMsg;
        } else if (is_numeric($msg)) {
            self::createMsg($msg);
            return self::$msg;
        } else {
            return self::$msg;
        }
    }
}
