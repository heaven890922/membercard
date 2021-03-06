<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Common extends Controller
{
    //设置前置操作
    protected $beforeActionList = [
        'checkKey',
    ];

    protected $apiToken;
    /**
     * @param $apiToken
     * @param $className
     * @param string $secretKey
     * 校验apiToken口令
     */
    public function checkApiToken($apiToken, $className, $secretKey = API_SECRET_KEY)
    {
        if(isset($apiToken))
        {
            $ret = $this->createToken($className,$secretKey);
            if($ret)
            {
                //校验apiToken口令是否正确
                if($apiToken == $this->apiToken)
                {
                    return  ['state' => true ,'code' => 200];
                }
                else
                {
                    //apiToken检验失败
                    return  ['state' => false ,'code' => 301];
                }
            }
            else
            {
                //系统创建口令失败
                return  ['state' => false ,'code' => 501];
            }
        }
        else
        {
            //302 口令缺失
            return  ['state' => false ,'code' => 302];
        }
    }

    /**
     * 生成Api口令  生成规则  md5（__CLASS__.日期.$secretKey）
     * @param $className            //控制器名称 __CLASS__
     * @param string $secretKey     //生成apiToken的密令
     * @return bool
     */
    public function createToken($className, $secretKey = API_SECRET_KEY)
    {
        $date = date("Ymd", time());
        if(!empty($className)&&!empty($secretKey)) {
            $this->apiToken = md5($className.$date.$secretKey);
            //p($this->apiToken);
            return true;
        } else {
            return false;
        }

    }

    /**
     * 检查apiKey是否正确
     */
    public function checkKey()
    {
        $apiKey = $this->request->param('apiKey');
        $key = Db::table('mc_api_key')->where('api_key', $apiKey)->where('status', 1)->field('api_key')->find();
        if(!isset($key)){
            echo json_encode(['code' => 311, 'msg' => 'api_key错误']) ; exit;
        }

    }

    public function errorInfo($msg, $info)
    {
        $this->redirect(WEB_SITE."/index.php/error/msg/$msg/info/$info");
    }
}
