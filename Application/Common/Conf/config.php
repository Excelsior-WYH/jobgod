<?php
return array(
	'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'wyh_jobgod', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'mysql', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_FIELDS_CACHE' => false,
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    'DB_PARAMS' =>  array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),



    'URL_CASE_INSENSITIVE' => true,  
    'URL_MODEL' => 3,
    'LOG_RECORD' => true, // 开启日志记录
    'SHOW_PAGE_TRACE' =>true, 
    
    
    'pageSize' => 10,

    // 上传路径
    'uploadConfig' => array(
        'maxSize' => 0,
        'rootPath' => './Public/',
        'exts' => '',
        'autoSub' => false
    ),
    'completeDomain' => 'http://www.noyours.com/jobGod/',

    /**
     * mob 短信验证 SDK
     */
    'mobAppKey' => '587c0a5919c6',
    'mobApi' => 'https://api.sms.mob.com/sms/verify',
    
    /**
     * RC 融云 SDK
     */
    'RCappKey' => 'cpj2xarljqa5n', 
    'RCappSecert' => 'TTXXj7RD6BIWvd',
    'RCgetToken' => 'https://api.cn.rong.io/user/getToken.json',    // 获取Token地址,
    'Rcrefresh' => 'https://api.cn.rong.io/user/refresh.json'
);