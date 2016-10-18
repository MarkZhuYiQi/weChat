<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/18/16
 * Time: 3:23 PM
 */

namespace MVC\Model;


class sendModel
{
    //群发接口
    function sendMsgAll($msgType)
    {
        //获取token
        //组装群发接口数据array
        //array->json
        //curl
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".getWeChatToken();
        switch($msgType)
        {
            case 'text':
                $param=array(
                    "touser"=>'o8SEYwhNzG-hPuEjw_kjxb9nZ1aA',   //openid
                    "text"=>array('content'=>'test api'),
                    "msgtype"=>$msgType
                );
                break;
            case 'image':
                $param=array(
                    "touser"=>'o8SEYwhNzG-hPuEjw_kjxb9nZ1aA',   //openid
                    "image"=>array("media_id"=>"yJcsFgqN3h98J9gD5nX6q7-y6cVDbChFNp2V8K5tGR0"),
                    "msgtype"=>$msgType
                );
                break;
            case 'mpnews':
                $param=array(
                    "touser"=>'o8SEYwhNzG-hPuEjw_kjxb9nZ1aA',   //openid
                    "mpnews"=>array("media_id"=>"yJcsFgqN3h98J9gD5nX6q7-y6cVDbChFNp2V8K5tGR0"),
                    "msgtype"=>$msgType
                );
                break;
        }
        $post=json_encode($param);
        $res=httpPOST($url,$post);
        var_dump($res);
    }
    function sendMsg()
    {
        
    }
}