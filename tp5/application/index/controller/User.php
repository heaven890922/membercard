<?php

namespace app\index\controller;

use app\index\model\Msg;
use app\index\model\User as U;
use think\Validate;

class User extends Common
{
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
                'payPwd.require' => '用户密码缺失'
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


}
