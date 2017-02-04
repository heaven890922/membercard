<?php
namespace app\index\observer;
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-01-19
 * Time: 11:48
 */

interface CardBuyObserver{
    public function finishBuyOrder();
    public function backBuyOrder();
}