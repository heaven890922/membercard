<?php

namespace app\index\model;

class ApiToken
{
    private $apiToken;

    /**
     * @param $apiToken
     * @param $className
     * @param string $secretKey
     * 校验apiToken口令
     */
    public function checkApiToken( $apiToken , $className , $secretKey = API_SECRET_KEY)
    {
        $msg = new  Msg();
        if(isset($apiToken))
        {
            $ret = $this->create($className,$secretKey);
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
    public function create($className, $secretKey = API_SECRET_KEY)
    {
        $date = date("Y-m-d", time());
        if(!empty($className)&&!empty($secretKey)) {
            $this->apiToken = md5($className.$date.$secretKey);
            //p($this->apiToken);exit;
            return true;
        } else {
            return false;
        }

    }


}
