<?php

namespace app\index\controller;



class Test extends Common
{
    protected $beforeActionList = [
        'checkKey',
        'second' =>  ['except'=>'hello'],
        'three'  =>  ['only'=>'hello,data'],
    ];



    protected function second()
    {
        echo 'second<br/>';
    }

    protected function three()
    {
        echo 'three<br/>';
    }

    public function hello()
    {
        return 'hello';
    }

    public function data()
    {
        return 'data';
    }
}
