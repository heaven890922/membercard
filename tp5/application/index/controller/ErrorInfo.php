<?php

namespace app\index\controller;

use think\Controller;

class ErrorInfo extends Controller
{
    public function index()
    {
        $data = $this->request->param();
        //$data['msg'] = iconv("GB2312//IGNORE","UTF-8" , $data['msg']);
        //$data['info'] = iconv("GB2312//IGNORE","UTF-8", $data['info']);
        $this->assign('data',$data);
        return $this->fetch();
    }
}
