<?php

namespace app\index\controller;


use app\index\model\Car;
use app\index\model\CardOrder;
use app\index\model\Customer;
use app\index\model\Msg;
use app\index\model\Shop;
use app\index\model\User;
use think\Db;
use think\Validate;

class QrCode extends Common
{
    /**
     * 创建充值二维码
     */
    public function create()
    {
        $data = $this->request->param();
        $payMethod = Db::query('SELECT GROUP_CONCAT(id) AS methods FROM mc_pay_method WHERE state =1');
        $validate = new Validate(
            [
                'money' => 'require|/^[1-9]\d*00$/',
                'methodID' => 'require|in:' . $payMethod[0]['methods'],
                'customerID' => 'require|integer',
                'shopID' => 'require|integer',
                'carNum' => 'require'
            ],
            [
                'money.require' => '请输入金额',
                'money./^[1-9]\d*00$/' => '金额必须为100的倍数',
                'methodID.in' => '支付方式选择错误',
                'methodID.require' => '请选择支付方式',
                'carNum.require' => '车牌号码是必须的',
                'customerID.require' => "客户信息缺失",
                'customerID.integer' => "客户信息错误",
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误"
            ]
        );
        if ($validate->check($data)) {
            $data['carNum'] = strtoupper($data['carNum']);
            $payUrl = new PayMethod();
            $url = $payUrl->getPayUrl($data);
            if ($url['state']) {
                $pngName = $data['shopID'] . '-' . $data['customerID'] . '-' . date('YmdHis', time());
                //p($url);
                $png = make_qrcode($url['url'], $pngName, $size = 10, $level = "L");
                unset($url['url']);
                $url['png'] = $png;
                $cardOrder = CardOrder::get(['cardOrderNum' => $url['orderNum']]);
                $saveData = [
                    'cardOrderNum' => $url['orderNum'],
                    'qrCodePng' => $url['png']
                ];
                $cardOrder->data($saveData,true)->isUpdate(true)->save();
            }
            $ret = Msg::getMsg($url);
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }

    /**
     * 检查是否是小车生活用户以及是否绑定车牌
     * @param string $dataInfo
     */
    public function check($dataInfo = '')
    {
        if ($dataInfo == '') {
            $data = $this->request->param();
        } else {
            $data = $dataInfo;
        }

        $validate = new Validate(
            [
                'tel' => 'require|length:11',
                'carNum' => 'require'
            ],
            [
                'tel.require' => '手机号码未输入',
                'tel.length' => '请输入正确的手机号码',
                'carNum.require' => '车牌号码未输入'
            ]
        );
        if ($validate->check($data)) {
            $customer = Customer::get(['tel' => $data['tel']]);
            if (isset($customer)) {
                $customerID = $customer->getAttr('coustomid');
                $car = Car::get(['coustomID' => $customerID, 'carNum' => $data['carNum']]);
                //p($data['carNum']);
                if (isset($car)) {
                    pJson(Msg::getMsg(['code' => 200, 'customerID' => $customerID]));
                } else {
                    pJson(Msg::getMsg(803));
                }
            } else {
                pJson(Msg::getMsg(802));
            }
        } else {
            pJson(Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]));
        }

    }

    /**
     * 检查是否能创建二维码金额
     */
    public function moneyCheck()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'customerID' => 'require|integer',
                'shopID' => 'require|integer',
                'money' => 'require|/^[1-9]\d*00$/'
            ],
            [
                'money./^[1-9]\d*00$/' => '金额必须为100的倍数',
                'shopID.require' => "商户信息缺失",
                'shopID.integer' => "商户信息错误",
                'money.require' => '请输入金额'
            ]
        );
        if ($validate->check($data)) {
            $shopBalance = Shop::get(['shopID' => $data['shopID']])->getAttr('remainQuota');
            $user = new User($data['customerID']);
            if ($shopBalance >= $data['money']) {
                if ($user->checkChargeLimit($data['money'])) {
                    $ret = Msg::getMsg(['code' => 200, 'isCanCharge' => 1]);
                } else {
                    $ret = Msg::getMsg(['code' => $user->chargeMsgCode, 'isCanCharge' => 0]);
                }
            } else {
                $ret = Msg::getMsg(['code' => 621, 'isCanCharge' => 0]);
            }
        } else {
            $ret = Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }
}
