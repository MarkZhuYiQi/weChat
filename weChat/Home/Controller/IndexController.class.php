<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index()
    {
        $signature=$_GET['signature'];
        $timestamp=$_GET['timestamp'];
        $nonce=$_GET['nonce'];
        $echoStr=$_GET['echostr'];
        $token='markzhu';
        $tempArr=array($token,$timestamp,$nonce);
        sort($tempArr);
        $tempStr=sha1(implode('',$tempArr));
        if($tempStr==$signature && $echoStr)
        {
            echo $echoStr;
            exit;
        }
        else
        {
            $this->responseMsg();
        }
    }
/*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>*/
    public function responseMsg()
    {
        $postStr=$GLOBALS['HTTP_RAW_POST_DATA'];
        if(!empty($postStr))
        {
            $postObj=simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
            $fromUserName=$postObj->FromUserName;
            $toUserName=$postObj->ToUserName;
            $keyWord=$postObj->Content;
            $time=time();
            //判断该数据包是否是订阅的事件推送
            if(strtolower($postObj->MsgType)=='event')
            {
                //如果是关注
                if(strtolower($postObj->Event='subscribe'))
                {
                    //自动回复一个消息
                    $re_toUserName=$fromUserName;
                    $re_fromUserName=$toUserName;
                    $MsgType='text';
                    $Content='welcome to subscribe my account!';
                    //回复消息格式
/*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[你好]]></Content>
</xml>*/
                    $template="<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                    $info=sprintf($template,$re_toUserName,$re_fromUserName,$time,$MsgType,$Content);
                    echo $info;
                }
            }

        }
    }
}