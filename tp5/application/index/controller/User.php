<?php

namespace app\index\controller;

use app\index\model\Car;
use app\index\model\CardOrder;
use app\index\model\ConsumeOrder;
use app\index\model\Customer;
use app\index\model\Msg;
use app\index\model\Shop;
use app\index\model\User as U;
use think\Db;
use think\Validate;

class User extends Common
{
    /**
     * 客户会员卡支付
     */
    public function pay()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'money' => 'require|number',
                'payPwd' => 'require',
                'shopID' => 'require|integer',
                'customerID' => 'require|integer'
            ],
            [
                'money.require' => '请输入金额',
                'money.number' => '金额必须是数字',
                'payPwd.require' => '用户密码缺失',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'customerID.require' => "客户信息缺失",
                'customerID.integer' => '客户信息参数错误'

            ]
        );
        if ($validate->check($data)) {
            $user = new U($data['customerID']);
            $ret = $user->pay($data['money'], $data['payPwd'], $data['shopID']);
            $ret = Msg::getMsg($ret);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 给客户设置支付密码
     */
    public function setPwd()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'payPwd' => 'require|number|length:6'
            ],
            [
                'payPwd.length' => '密码长度必须为6位',
                'payPwd.number' => '密码必须是数组',
                'customerID.require' => "客户信息缺失",
                'customerID.integer' => '客户信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $customer = Customer::get($data['customerID']);
            $ret = $customer->setPwd($data['payPwd']);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 检查是否设置密码
     */
    public function checkPwdSet()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer'
            ],
            [
                'customerID.require' => "客户信息缺失",
                'customerID.integer' => '客户信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $customer = Customer::get(['coustomid' => $data['customerID']]);
            if ($customer->isSetPwd()) {
                $ret['isSet'] = 1;
            } else {
                $ret['isSet'] = 0;
            }
            $ret['code'] = 200;
            $ret = Msg::getMsg($ret);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 客户绑定车牌号码
     */
    public function bindCarNum()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'carNum' => 'require',
                'payPwd' => 'require'
            ],
            [
                'carNum.require' => "必须输入车牌信息！",
                'payPwd.require' => "请输入支付密码！",
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $customer = Customer::get(['coustomid' => $data['customerID']]);
            if ($data['payPwd'] == $customer['pwd']) {
                //该车牌是否已经绑定
                $data['carNum'] = strtoupper($data['carNum']);
                $carCheck = Car::get(['carNum' => $data['carNum']]);
                if (isset($carCheck)) {
                    $ret = Msg::getMsg(804);
                } else {
                    $userCheck = Car::get(['coustomID' => $data['customerID']]);
                    if (isset($userCheck)) {
                        $ret = Msg::getMsg(805);
                    } else {
                        $saveData['coustomID'] = $data['customerID'];
                        $saveData['carNum'] = $data['carNum'];
                        $saveData['createTime'] = getTime();
                        $car = new Car();
                        $car->data($saveData);
                        $car->save();
                        $ret = Msg::getMsg(['code' => 200]);
                    }
                }
            } else {
                $ret = Msg::getMsg(801);
            }
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 检查是否已经绑定车牌
     */
    public function checkCarNum()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $car = Car::get(['coustomID' => $data['customerID'], 'state' => 1]);
            if (isset($car)) {
                $isCar['isCar'] = 1;
            } else {
                $isCar['isCar'] = 0;
            }
            $isCar['code'] = 200;
            $ret = Msg::getMsg($isCar);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取用户信息
     */
    public function getInfo()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误'
            ]
        );
        if ($validate->check($data)) {
            $dataRet = [];
            $customer = Customer::get(['coustomid' => $data['customerID']])->getAttr('tel');
            $car = Car::get(['coustomID' => $data['customerID']])->getAttr('carNum');
            $user = new U($data['customerID']);
            $balance = $user->getBalance();
            $dataRet['tel'] = $customer;
            $dataRet['carNum'] = $car;
            $dataRet['balance'] = $balance;
            $ret = Msg::getMsg(['code' => 200, 'info' => $dataRet]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取支付订单记录列表
     */
    public function getPayOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'page' => 'require|integer'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误',
                'page.require' => '页数参数缺失',
                'page.integer' => '页数参数错误'
            ]
        );
        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            $orderData = Db::query('select o.consumeOrderNum,o.createTime,o.consumeQuota,s.shopname from mc_consume_order o JOIN ' . JFYCARLIFE . '.sh_shop s ON o.shopID = s.shopid where o.customerID=:id ORDER BY o.createTime DESC limit ' . $start . ',' . PER_NUM, ['id' => $data['customerID']]);
            $ret = Msg::getMsg(['code' => 200, 'data' => $orderData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取充值订单列表
     */
    public function getBuyOrder()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'page' => 'require|integer'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误',
                'page.require' => '页数参数缺失',
                'page.integer' => '页数参数错误'
            ]
        );
        if ($validate->check($data)) {
            $start = $data['page'] * PER_NUM;
            $orderData = Db::query('SELECT i.`name`,o.cardOrderNum,o.createTime,o.quota,m.payMethodName,o.paid,s.shopname,m.port FROM mc_card_info i JOIN mc_card_order o ON i.orderID = o.cardOrderID AND i.customerID=:id JOIN ' . JFYCARLIFE . '.sh_shop s ON i.shopID = s.shopid JOIN mc_pay_method m ON o.payType = m.id ORDER BY o.createTime DESC  limit ' . $start . ',' . PER_NUM, ['id' => $data['customerID']]);
            $ret = Msg::getMsg(['code' => 200, 'data' => $orderData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取支付订单信息（单个）支付完成之后的订单那信息
     */
    public function getPayOrderInfo()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'orderNum' => 'require'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误',
                'orderNum.require' => '订单号是必须的'
            ]
        );
        if ($validate->check($data)) {
            $consumeOrder = new ConsumeOrder();
            $retData = $consumeOrder->getOrder($data['orderNum']);
            if (!empty($retData)) {
                $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
            } else {
                $ret = Msg::getMsg(702);
            }
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取充值订单信息（单个）支付完成之后的订单那信息
     */
    public function getChargeOrderInfo()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'orderNum' => 'require'
            ],
            [
                'customerID.require' => '客户信息参数缺失',
                'customerID.integer' => '客户信息参数错误',
                'orderNum.require' => '订单号是必须的'
            ]
        );
        if ($validate->check($data)) {
            $carOrder = new CardOrder();
            $retData = $carOrder->getOrder($data['orderNum']);
            if (!empty($retData)) {
                $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
            } else {
                $ret = Msg::getMsg(702);
            }
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取商户列表（用户选择商户的列表,根据距离排序）
     */
    public function getShopListByDistance()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'longitude' => 'require|float',
                'latitude' => 'require|float',
                'page' => 'require|integer',
                'num' => 'require|integer'
            ],
            [
                'longitude.require' => '坐标点经度是必须的',
                'latitude.require' => '坐标纬度是必须的',
                'page.require' => "页数参数缺失",
                'page.integer' => "页数参数错误",
                'num.require' => '数量参数缺失',
                'num.integer' => '数量参数错误'
            ]
        );
        if ($validate->check($data)) {
            $shop = new Shop();
            if (empty($data['num'])) {
                $data['num'] = 10;
            }
            $retData = $shop->getListByDistance($data['longitude'], $data['latitude'], $data['page'], $data['num']);
            $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 按区域查询商户列表
     */
    public function getShopListByArea()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'longitude' => 'require|float',
                'latitude' => 'require|float',
                'areaID' => 'require|integer',
                'page' => 'require|integer',
                'num' => 'require|integer'
            ],
            [
                'longitude.require' => '坐标点经度是必须的',
                'latitude.require' => '坐标纬度是必须的',
                'areaID.require' => '区域参数是必须的',
                'areaID.integer' => '区域参数必须为整数',
                'page.require' => "页数参数缺失",
                'page.integer' => "页数参数错误",
                'num.require' => '数量参数缺失',
                'num.integer' => '数量参数错误'
            ]
        );
        if ($validate->check($data)) {
            $shop = new Shop();
            if (empty($data['num'])) {
                $data['num'] = 10;
            }
            $retData = $shop->getListByArea($data['longitude'], $data['latitude'], $data['areaID'], $data['page'], $data['num']);
            $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    public function getShopListBySearch()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'longitude' => 'require|float',
                'latitude' => 'require|float',
                'search' => 'require',
                'page' => 'require|integer',
                'num' => 'require|integer'
            ],
            [
                'longitude.require' => '坐标点经度是必须的',
                'latitude.require' => '坐标纬度是必须的',
                'search.require' => '搜索条件是必须的',
                'page.require' => "页数参数缺失",
                'page.integer' => "页数参数错误",
                'num.require' => '数量参数缺失',
                'num.integer' => '数量参数错误',
                'longitude.float' => "坐标点经度错误",
                'latitude.float' => "坐标点纬度错误"
            ]
        );
        if ($validate->check($data)) {
            $shop = new Shop();
            if (empty($data['num'])) {
                $data['num'] = 10;
            }
            $retData = $shop->getListBySearch($data['longitude'], $data['latitude'], $data['search'], $data['page'], $data['num']);
            $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 获取区域列表
     */
    public function getAreaList()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'areaID' => 'require|integer',
            ],
            [
                'areaID.require' => '区域参数是必须的',
                'areaID.integer' => '区域参数错误'
            ]
        );
        if ($validate->check($data)) {
            $retData = Db::query("SELECT areaID,areaName FROM " . JFYCARLIFE . ".sh_area WHERE fid =" . $data['areaID']);
            $ret = Msg::getMsg(['code' => 200, 'data' => $retData]);
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

}
