<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller
{
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
        file_put_contents("weixin",date('Y-m-d H:i:s').$signature."|".$timestamp."|".$nonce."|".$echoStr.PHP_EOL);
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
    public function responseMsg()
    {
        $postStr=file_get_contents('php://input');
        file_put_contents(getcwd().'/msg',$postStr);
        if(!empty($postStr))
        {
//            $postObj=simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
            $postObj=simplexml_load_string($postStr);
            $fromUserName=$postObj->FromUserName;
            $toUserName=$postObj->ToUserName;
            $time=time();
            switch(strtolower($postObj->MsgType))
            {
                case "event":
                    if(strtolower($postObj->Event)=='subscribe')
                    {
                        //自动回复一个消息
                        $re_toUserName=$fromUserName;
                        $re_fromUserName=$toUserName;
                        $MsgType='text';
                        $Content='welcome to subscribe my account!';
                        //回复消息格式
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
                break;
                case "text":
                    //自动回复一个消息
                    $re_toUserName=$fromUserName;
                    $re_fromUserName=$toUserName;
                    $MsgType='text';
                    $Content="Don't be so rapid, i see it you fucking asshole!";
                    $template='<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>';
                    $info=sprintf($template,$re_toUserName,$re_fromUserName,$time,$MsgType,$Content);
                    echo $info;
                break;
            }
        }
    }
}