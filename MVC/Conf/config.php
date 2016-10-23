<?php
    define('DB_TYPE','mysql');          // 数据库类型
    define('DB_HOST','localhost');   // 数据库服务器地址
    define('DB_NAME','red');            // 数据库名称
    define('DB_USER','root');           // 数据库用户名
    define('DB_PWD','7777777y');        // 数据库密码
    define('DB_PREFIX','we');             // 数据表前缀
    define('DB_CHARSET','utf8');        // 网站编码
    define('DB_PORT','3306');           // 数据库端口
    define("DB_DSN","mysql:host=localhost;dbname=weChat");     //NotORM初始化
    define('','');



    define("CACHE_IP","127.0.0.1");
    define("CACHE_PORT","11211");       //memcache默认端口


    define('CURRENT_VIEWPATH','v1');    //当前视图目录
    define('CURRENT_VIEWPATH_ADMIN','');    //当前管理视图目录
    define('CACHE_PATH','cache/');      //缓存地址



    define('BACKGROUND_ENCRYPTKEY','LogWeChatRuiYunDuBackground@026!');   //cookie加密解密用的秘钥
    define('BACKGROUND_LOGINKEY','weChatBackgroundLogin');      //后台cookie登录名称
    define('USER_PASSWORD_CRYPT_KEY','AccountBelongsToRuiYunDu@163.com'); //用户密码秘钥