<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/17/16
 * Time: 10:38 AM
 */
//测试账号的信息
define('APPID','wx9a7e00ceaf1818fc');
define('APPSECRET','be27d8bf1bcdde5065454e943341268c');

//第一步 获取用户code
if(isset($_GET['code']))
{
    $userCode=$_GET['code'];
    echo 'User Code: '.$userCode.PHP_EOL;
    //第二步，通过usercode获取access_token
    $access_token_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&code=".$userCode."&grant_type=authorization_code";
    $gainToken=tokenHandler($access_token_url);
    //第三步，非必要，根据refresh_token刷新access_token
    $refresh_token_url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".APPID."&grant_type=refresh_token&refresh_token=".$gainToken['refresh_token'];
    $gainToken=tokenHandler($refresh_token_url);
    //第四步：使用access_token获取用户信息
    $user_info_url="https://api.weixin.qq.com/sns/userinfo?access_token=".$gainToken['access_token']."&openid=".$gainToken['openid'];
    $userInfo=tokenHandler($user_info_url);
    echo '您的唯一标识：'.$userInfo['openid'].'<br />';
    echo '昵称：'.$userInfo['nickname'].'<br />';
    echo $userInfo['sex']==1?'性别：男':'性别：女'."<br />";
    echo '省份：'.$userInfo['province'].'<br />';
    echo '城市：'.$userInfo['city'].'<br />';
    echo '国家：'.$userInfo['country'].'<br />';
    echo '头像：'."<img src='".$userInfo['headimgurl']."' alt='头像' />".'<br />';
    echo '特殊信息： '.var_export($userInfo['privilege']).'<br />';
    access_token_check($gainToken['access_token'],$gainToken['openid']);
}
else
{
    echo 'no code';
}
/**
 * @param $url
 * @return mixed
 * curl get请求
 */
function getData($url)
{
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_TIMEOUT,15);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}
function tokenHandler($url)
{
    $output=getData($url);
    $json=json_decode($output);
    $gainToken=get_object_vars($json);  //将返回的json转换成数组
    return $gainToken;
}
function access_token_check($access_token,$openid)
{
    $url="https://api.weixin.qq.com/sns/auth?access_token=".$access_token."&openid=".$openid;
    $check=getData($url);
    var_export($check);
}
