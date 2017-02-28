<?php
namespace app\index\controller;

use app\index\model\Msg;
use think\Validate;

class Index extends Common
{
    protected $beforeActionList = [
        'checkKey' => ['except'=>'index,login'],
    ];
    public function index()
    {
        /*$userT = new UserToken(23);
        $userToken = $userT->getToken();
        p($userToken);*/
        /*$shop = new Shop();
        $ret = $shop->checkKey("e0684fed76aaa6611f436a381491687e");
        print_r($ret);*/
        /* $user = new User(188);
         $user->checkChargeLimit(300);
         p( $user->chargeMsgCode);*/
        /*$buy = new  Buy();
        $ret = $buy->finishBuy('3001201701201639075755127608', 10000);
        p($ret);*/
        /*$data = $this->request->param();
        $cardOrder = new CardOrder();
        $ret =$cardOrder->createOrder($data);
        p($ret);*/
        //p(build_order_no());
        //pJson(make_qrcode('http://www.baidu.com', 'testQR4', $size = 10, $level = "L"));
        //$this->errorInfo('我是错误测试','错误测试是我');
    }

    public function login()
    {
        //接收传入参数
        $data = $this->request->param();
        if (!empty($data)) {
            //校验口令
            $ret = $this->checkApiToken($data['apiToken'], __CLASS__);
            if ($ret['state'] === true) {
                $validate = new Validate([
                    'name' => 'require|max:25',
                    'email' => 'email'
                ]);
                if (!$validate->check($data)) {
                    dump($validate->getError());
                } else {
                    print_r($data);
                }
            } else {
                pJson(Msg::getMsg($ret));
            }
        } else {
            return $this->fetch('login');
        }

    }

}
