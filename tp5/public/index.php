<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
header('Access-Control-Allow-Origin:*');
// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define("API_SECRET_KEY",'xcshmembercard');
define("USER_SECRET_KEY",'xcsh.membercard.com');
define('JFYCARLIFE', 'jfycarlifetest');
define('HOSTNAME', '112.74.90.130');
//define('WEB_SITE', 'http://xcsh.card.com');
define('WEB_SITE', 'http://192.168.1.200/membercard/tp5/public');
define('QR_WEB_SITE', WEB_SITE.'/index.php');
define('VALID_TIME', 600);
define('PER_NUM',6);
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
