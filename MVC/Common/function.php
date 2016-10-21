<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/17/16
 * Time: 8:45 PM
 */
use MVC\Model\model;


function load_model($mName,$dsn=DB_DSN)
{
    return new model($mName,$dsn);
}

function load_Lib($lib,$libName)
{
    require("MVC/Lib/".$lib.'/'.$libName.'.php');
}

function httpGET($url)
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $output = curl_exec($ch);
    if (curl_errno($ch)) var_export(curl_error($ch));
    curl_close($ch);
    return $output;
}
function httpPOST($url,$postParam=false,$header=false)
{
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    
    if(version_compare(phpversion(),'5.6')>=0 && version_compare(phpversion(),'7.0')<0)
    {
        //php5.5以上要加这句话
        curl_setopt($ch,CURLOPT_SAFE_UPLOAD,false);
    }
    //PHP7.0禁止了CURLOPT_SAFE_UPLOAD, 只能是true，所以只能用CURLFile代替方案
    if(version_compare(phpversion(),'7.0')>=0)
    {
        foreach($postParam as $key=>$value)
        {
            if(strpos($value,'@')===0)
            {
                $value=ltrim($value,'@');
                $postParam[$key]=new CURLFile($value);
            }
        }
    }

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5000);
    if($postParam){
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postParam);
    }
    if($header){
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    $res=curl_exec($ch);
    if(curl_errno($ch))echo 'Error!'.curl_error($ch);
    curl_close($ch);
    return $res;
}
function getWeChatToken()
{
    $data = json_decode(file_get_contents(getcwd() . '/access_token'));
    if ($data->expire_time < time()) {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID . '&secret=' . APPSECRET;
        $output=httpGET($url);
        $output = json_decode($output);
        $data->access_token = $output->access_token;
        $data->expire_time = time() + 7000;
        file_put_contents(getcwd() . '/access_token', json_encode($data));
    }
    return $data->access_token;
}
function get_cache($key)
{
    $m=new Memcache();
    $m->connect(CACHE_IP,CACHE_PORT);
    return $m->get($key);
}
function set_cache($key,$value,$expire)
{
    $m=new Memcache();
    $m->connect(CACHE_IP,CACHE_PORT);
    return $m->set($key,$value,0,$expire);
}

/**
 * @param $pname 想要获取的值对应的key
 * @param string $method 获取的方法，默认是GET，还有POST
 * @return bool|mixed|string 返回经过处理的值
 */
function GET($pname,$method='get')
{
    $plist=$method=='get'?$_GET:$_POST;
    if(isset($plist[$pname]))
    {
        $getValue=trim($plist[$pname]);
        $getValue=strip_tags($getValue);        //去除HTML和php标记
        $getValue=addslashes($getValue);        //预定义字符转义
        $getValue=str_replace(array('gcd'),'',$getValue);
        return $getValue;
    }
    else
    {
        return false;
    }
}
function IP(){
    if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $cip=$_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif(!empty($_SERVER["REMOTE_ADDR"])){
        $cip=$_SERVER["REMOTE_ADDR"];
    }else{
        $cip="";
    }
    return $cip;
}













$foreach_id=array();        //存放每个foreach的唯一标识符
/**
 * @param $match
 * @return string
 * 该函数是用于给每个foreach添加唯一标示符
 */
function foreachCallBack($match){
//    $id=md5(uniqid());        //这种方案在高并发状态下还是会重复，我觉得还是rand好点
    $id=md5(rand());        //这种方案在高并发状态下还是会重复，我觉得还是rand好点
    global $foreach_id;
    $foreach_id[]=$id;
    return $match[1].":".$match[2].":".$id;
}
