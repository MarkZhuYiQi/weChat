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

    /**
     * 回复类
     */
    public function responseMsg()
    {
        $postStr=file_get_contents('php://input');
        file_put_contents(getcwd().'/msg',$postStr);
        if(!empty($postStr))
        {
            $postObj=simplexml_load_string($postStr);
            switch(strtolower($postObj->MsgType))
            {
                case "event":
                case "text":
                    //自动回复一个消息
                    if(strtolower(trim($postObj->Content))=='news')
                    {
                        $this->replies('news',$postObj);
                    }
                    else
                    {
                        $this->replies('text',$postObj);
                    }
                break;
            }
        }
    }

    /**
     * @param $postObj 服务器发来的被实例化成对象的XML信息
     * @return string 返回需要返回的内容
     * 对服务器发来的消息进行分类，按照内容返回相应的回复内容。
     */

    function textHandler($postObj)
    {
        if(isset($postObj->Content))
        {
            switch(trim($postObj->Content))
            {
                case 'test':
                    $Content='your weChat openID: '.$postObj->FromUserName.PHP_EOL
                        .'my Account: '.$postObj->ToUserName.PHP_EOL
                        .'send Time: '.date('Y-m-d H:i:s',$postObj->CreateTime).PHP_EOL;
                    break;
                case 'link':
                    $Content="<a href='http://www.baidu.com'>baidu</a>";
                    break;
                default:
                    $Content="message received, waiting for reading by the master";
            }
        }
        elseif(isset($postObj->Event))
        {
            switch($postObj->Event)
            {
                case 'subscribe':
                    $Content='welcome to subscribe my account!';
                break;
            }
        }
        return $Content;
    }

    /**
     * @param $type     想要回复的消息类型
     * @param $postObj  服务器发来的被实例化的xml对象
     */
    function replies($type,$postObj)
    {
        //自动回复一个消息
        $re_toUserName=$postObj->FromUserName;
        $re_fromUserName=$postObj->ToUserName;
        $time=time();
        switch($type)
        {
            case 'text':
                $MsgType='text';
                $Content=$this->textHandler($postObj);
                //回复消息格式
                $template="<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
                $info=sprintf($template,$re_toUserName,$re_fromUserName,$time,$MsgType,$Content);
            break;
            case 'news':
                $MsgType='news';
                $items=array(
                    array(
                        'title'=>'weChat developer document',
                        'description'=>'a detailed introduction to teach developer how to use its API',
                        'picurl'=>'http://markzhu.imwork.net/big.png',
                        'url'=>'http://www.baidu.com'
                    ),
                    array(
                        'title'=>'developer document',
                        'description'=>'a detailed introduction to teach developer how to use its API',
                        'picurl'=>'http://markzhu.imwork.net/small.png',
                        'url'=>'http://www.baidu.com'
                    )
                );
                $articleCount=count($items);
                $template='<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <ArticleCount>%s</ArticleCount>';
                $template.='<Articles>';
                foreach($items as $item)
                {
                    $template.='<item>
                                    <Title><![CDATA['.$item['title'].']]></Title> 
                                    <Description><![CDATA['.$item['description'].']]></Description>
                                    <PicUrl><![CDATA['.$item['picurl'].']]></PicUrl>
                                    <Url><![CDATA['.$item['url'].']]></Url>
                                </item>';
                }
                $template.='</Articles>
                            </xml>';
                $info=sprintf($template,$re_toUserName,$re_fromUserName,$time,$MsgType,$articleCount);
            break;
        }
        echo $info;
    }
}