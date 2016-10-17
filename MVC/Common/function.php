<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/17/16
 * Time: 8:45 PM
 */
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
            if(strpos($value,'@'))
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