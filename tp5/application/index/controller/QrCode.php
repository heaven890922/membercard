<?php

namespace app\index\controller;


use app\index\model\Car;
use app\index\model\Customer;
use app\index\model\Msg;
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
                'money'  => 'require|/^[1-9]\d*00$/',
                'methodID' => 'require|in:'.$payMethod[0]['methods'],
                'customerID' => 'require|integer',
                'shopID' => 'require|integer'
            ],
            [
                'money.require' => '请输入金额',
                'money./^[1-9]\d*00$/' => '金额必须为100的倍数',
                'methodID.in' => '支付方式选择错误',
                'methodID.require' => '请选择支付方式'
            ]
        );
        if ($validate->check($data)) {
            $arr = ['remarks' => ''];
            $data = array_merge($arr, $data);
            $payUrl = new PayMethod();
            $url = $payUrl->getPayUrl($data);
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

        $validate = new Validate([
            'tel'  => 'require|length:11',
            'carNum' => 'require'
        ]);
        if ($validate->check($data)) {
            $customer = Customer::get(['tel' => $data['tel']]);
            if (isset($customer)){
                $customerID = $customer->getAttr('coustomid');
                $car = Car::get(['coustomID' => $customerID, 'carNum' => $data['carNum']]);
                if (isset($car)) {
                    pJson(Msg::getMsg(['code' => 200, 'customerID' => $customerID]));
                } else {
                    pJson(Msg::getMsg(803));
                }
            } else {
                pJson(Msg::getMsg(802));
            }
        } else {
            pJson( Msg::getMsg(['code' => 402, 'msg' => $validate->getError()]));
        }

    }

}
