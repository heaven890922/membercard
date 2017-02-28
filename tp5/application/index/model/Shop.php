<?php

namespace app\index\model;

use think\Db;
use think\Log;
use think\Model;

class Shop extends Model
{

    protected $table = 'mc_shop_config';

    /**
     * 减少商户可发卡余额
     * @param $money
     */
    public function reduceRemainQuota($money)
    {
        $this->remainQuota = $this->remainQuota - $money;
        $this->totalQuota = $this->totalQuota + $money;
        $this->save();
    }

    /**
     * 增加商户可发卡余额
     * @param $money
     */
    public function increaseRemainQuota($money){
        $this->remainQuota = $this->remainQuota + $money;
        $this->usedQuota = $this->usedQuota + $money;
        $this->save();
    }
    /**
     * 获取商户列表，距离排序
     * @param $longitude
     * @param $latitude
     * @param $page
     * @param int $num
     * @return mixed
     */
    public function getListByDistance($longitude, $latitude, $page, $num = 10)
    {
        $start = $page * $num;
        $data = Db::query("SELECT s.shopname,s.shopaddress,s.shopid,s.locationlon,s.locationlat,s.pictureurl,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude *PI()/180-locationlat*PI()/180)/2),2)+COS($latitude *PI()/180)*COS(locationlat*PI()/180)*POW(SIN(($longitude*PI()/180-locationlon*PI()/180)/2),2)))*1000) AS juli FROM mc_shop_config c JOIN ".JFYCARLIFE.".sh_shop s ON c.shopID = s.shopid AND c.state = 'ON' having juli<100000 ORDER BY juli ASC LIMIT $start,$num ");
        return $data;
    }

    /**
     * 根据区域获取商户列表
     * @param $longitude
     * @param $latitude
     * @param $areaID
     * @param $page
     * @param int $num
     * @return mixed
     */
    public function getListByArea($longitude, $latitude, $areaID, $page, $num = 10)
    {
        $start = $page * $num;
        $data = Db::query("SELECT s.shopname,s.shopaddress,s.shopid,s.locationlon,s.locationlat,s.pictureurl,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($longitude *PI()/180-locationlat*PI()/180)/2),2)+COS($latitude *PI()/180)*COS(locationlat*PI()/180)*POW(SIN(($latitude*PI()/180-locationlon*PI()/180)/2),2)))*1000) AS juli FROM mc_shop_config c JOIN ".JFYCARLIFE.".sh_shop s ON c.shopID = s.shopid AND c.state = 'ON' AND s.areaID = $areaID  ORDER BY juli ASC LIMIT $start,$num ");
        return $data;
    }

    /**
     * 搜索商户名称
     * @param $longitude
     * @param $latitude
     * @param $search
     * @param $page
     * @param int $num
     * @return mixed
     */
    public function getListBySearch($longitude, $latitude, $search, $page, $num = 10)
    {
        $start = $page * $num;
        $data = Db::query("SELECT s.shopname,s.shopaddress,s.shopid,s.locationlon,s.locationlat,s.pictureurl,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($longitude *PI()/180-locationlat*PI()/180)/2),2)+COS($latitude *PI()/180)*COS(locationlat*PI()/180)*POW(SIN(($latitude*PI()/180-locationlon*PI()/180)/2),2)))*1000) AS juli FROM mc_shop_config c JOIN ".JFYCARLIFE.".sh_shop s ON c.shopID = s.shopid AND c.state = 'ON' AND s.shopname like '%".$search."%'  ORDER BY juli ASC LIMIT $start,$num ");
        return $data;
    }


}
