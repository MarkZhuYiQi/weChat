<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/16/16
 * Time: 10:51 AM
 */

namespace Home\Model;
use Think\Model;

class indexModel
{
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
                    $Content="<a href='http://markzhu.imwork.net/demo/index.html'>demo</a>";
                    break;
                case 'auth':
                    $scope='snsapi_userinfo';
                    $redirect_uri='http://markzhu.imwork.net/OAUTH.php?'.mt_rand(0,2);
                    $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$redirect_uri."&response_type=code&scope=".$scope."&state=1#wechat_redirect";
                    $Content="<a href='".$url."'>auth</a>";
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
                    $Content="I've seeing you fucking asshole!!";
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
                //从数据库中获取
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


    function weather()
    {
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/weatherservice/cityid?cityid=101010100';
        $header = array(
            'apikey: 您自己的apikey',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);

        var_dump(json_decode($res));
    }


}