<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/14/16
 * Time: 9:26 PM
 */
//define("APPID","wx26ab595b1d049a06");
//define("APPSECRET","c0e84fcfcecbbe3cec8e80189870d0b8");
define("APPID","wx9a7e00ceaf1818fc");
define("APPSECRET","be27d8bf1bcdde5065454e943341268c");
/**
 * 获取微信access_token
 * 返回2个参数
 * access_token:全局接口调用唯一凭据，至少512字符空间，有效期为2小时
 * expire_in:返回一个0-7200之间的数字，超过7200及2小时token失效，需要更新
 */
get_access_token();
//get_weixin_ip();

function get_access_token()
{
    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
    $return=file_get_contents($url);
//    return json_decode($return)->access_token;
    foreach(json_decode($return) as $key=>$value)
    {
        echo $key .'='. $value.PHP_EOL;
    }
}
function get_weixin_ip()
{
    $access_token=get_access_token();
    echo $access_token;
    $url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
    $ip=json_decode(file_get_contents($url));
    foreach($ip as $key =>$value)
    {
        echo var_export($value)."<br />";
    }
}