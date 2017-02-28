<?php

namespace app\index\model;

use think\Cache;
use think\Db;

class UserToken
{

    private static $secretKey; //用于加密的密钥
    private $userToken; //用户口令
    /**
     * 获取密钥
     * @return mixed
     */
    private static function getKey()
    {
        //从数据库中读取加密key
       /* if (isset(self::$secretKey)) {
            return self::$secretKey;
        } else {
            $secretData = Db::table("card_token_key")->where('id',1)->find();
          self::$secretKey = $secretData['tokenKey'];
        }*/
       //定义常量key值
       self::$secretKey = USER_SECRET_KEY;
        return true;
    }

    /**
     * 构造函数
     * @param $userId           //用户id
     * @param bool $isCache     //是否生成缓存
     * @param int $expire       //缓存过期时间
     * @param bool $refresh     //是否刷新缓存
     */
    public function __construct($userId, $expire = 0, $isCache = true, $refresh = false)
    {

        if (!isset(self::$secretKey)) {
            self::getKey();
        }
        $secretKey = self::$secretKey;

        if ($refresh) {
            $userToken = md5($userId.$secretKey);
            $this->userToken = $userToken;
            if ($isCache) {
                Cache::set("token".$userId,$userToken,$expire);
            }
        } else {
            if ($isCache) {
                $cacheToken = Cache::get("token".$userId);
                if (empty($cacheToken)) {
                    $userToken = md5($userId.$secretKey);
                    $this->userToken = $userToken;
                    Cache::set("token".$userId,$userToken,$expire);
                } else {
                    $this->userToken = $cacheToken;
                }
            }

        }
    }

    /**
     * 获取用户的userToken
     * @return mixed
     */
    public function getToken()
    {
        return $this->userToken;
    }

    /**
     * @param $userToken    //用户userToken
     * @return bool         //校验结果
     */
    public function checkToken($userToken)
    {
        if ($this->userToken == $userToken) {
            return true;
        } else {
            return false;
        }
    }
}
