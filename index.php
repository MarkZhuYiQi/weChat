<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);
define("APP_NAME","WeChat");
// 定义应用目录
define('APP_PATH','./weChat/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单

/*
define("APPID","wx26ab595b1d049a06");
define("APPSECRET","c0e84fcfcecbbe3cec8e80189870d0b8");

function checkSignature()
{
    $signature=$_GET['signature'];
    $timestamp=$_GET['timestamp'];
    $nonce=$_GET['nonce'];
    $echoStr=$_GET['echostr'];
    $token='markzhu';
    $tempArr=array($token,$timestamp,$nonce);
    sort($tempArr);
    $tempStr=sha1(implode('',$tempArr));
    file_put_contents(getcwd().'/weixin',date('Y-m-d H:i:s').'signature: '.$signature
        ."|timestamp: ".$timestamp."|nonce: ".$nonce."|echoStr: ".$echoStr.PHP_EOL);
    if($tempStr==$signature && $echoStr)
    {
        return true;
        exit;
    }
    return false;
}
function vaild()
{
    if(checkSignature())
    {
        $echoStr=$_GET['echostr'];
        echo $echoStr;
    }
}
function responseMsg()
{
//    if(checkSignature() && $GLOBALS['HTTP_RAW_POST_DATA'])
//    {
        $postStr=$GLOBALS['HTTP_RAW_POST_DATA'];
        file_put_contents(getcwd().'/msg',$postStr);
//    }
}

function get_access_token()
{
    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
    $return=file_get_contents($url);
//    return json_decode($return)->access_token;
    file_put_contents(getcwd().'/access_token',$return);
    responseMsg();
    echo '';


}
//vaild();
get_access_token();
*/