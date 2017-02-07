<?php

namespace app\index\controller;


use app\index\model\Msg;
use app\index\model\ShopAccount;
use think\Validate;

class Shop extends Common
{
    public function checkPwd()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'shopID' => 'require|integer',
                'payPwd' => 'require'
            ],
            [
                'payPwd.require' => '商户支付密码缺失'
            ]
        );
        if ($validate->check($data)) {
            $shop = new ShopAccount();
            $shopData = $shop->where('shopid', $data['shopID'])->find();
            if ($data['payPwd'] == $shopData->paypwd) {
                $ret = Msg::getMsg(['code' => 200]);
            } else {
                $ret = Msg::getMsg(['code' => 631]);
            }
        } else {
            $ret = Msg::getMsg(['code' => 401, 'msg' => $validate->getError()]);
        }
        pJson($ret);
    }
}
