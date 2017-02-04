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

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define("API_SECRET_KEY",'xcshmembercard');
define("USER_SECRET_KEY",'xcsh.membercard.com');
define('JFYCARLIFE', 'jfycarlifetest');
define('HOSTNAME', '112.74.90.130');
define('WEB_SITE', 'http://xcsh.card.com');
define('VALID_TIME', 60000);
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
