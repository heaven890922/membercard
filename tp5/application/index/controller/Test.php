<?php

namespace app\index\controller;





use think\Controller;

class Test extends Controller
{
    public function index()
    {
        $this->redirect("ErrorInfo/index",['cate_id' => 2]);
    }
}
