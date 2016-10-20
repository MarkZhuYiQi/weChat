<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/20/16
 * Time: 11:09 AM
 */

namespace MVC\Controller;

class replyController
{
    /**
     * @param $postObj
     */
    function eventHandler($postObj)
    {
        if(isset($postObj->Event))
        {
            $m=load_model('user');
            switch($postObj->Event)
            {
                case 'subscribe':
                    //在数据库中添加该用户的相关信息
                    $Content="I've seeing you fucking asshole!!";
//                    'we_subscribeDate'=>date('Y-m-d H:i:s',get_object_vars($postObj)['CreateTime'])
                    $insertRow=array(
                        'we_openid'=>(string)$postObj->FromUserName,
                        'we_subscribeDate'=>get_object_vars($postObj)['CreateTime']
                    );
//                    $insertRow=array('we_openid'=>'123123123','we_subscribeDate'=>'123123123');
                    $m->insert($insertRow,'we_user');
                    $this->replies('text',$postObj,$Content);
                    break;
                case 'unsubscribe':
                    //数据库中删除该用户相关信息。
                    $m->delete(" we_openid='".(string)$postObj->FromUserName."' ");
                    break;
                case 'CLICK':
                    //CLICK事件，基本都是从自定义菜单中发送
                    if($postObj->EventKey=='WE_ABOUT')
                    {
                        $Content="关于我们！";
                        $this->replies('news',$postObj);
                    }
                    elseif($postObj->EventKey=='WE_NEWEST')
                    {
                        $this->replies('news',$postObj);
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
                    $this->replies('text',$postObj,$Content);
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
        $this->replies('text',$postObj,$Content);
    }

    /**
     * @param $type     想要回复的消息类型
     * @param $postObj  服务器发来的被实例化的xml对象
     */
    function replies($type,$postObj,$Content='')
    {
        //自动回复一个消息
        $re_toUserName=$postObj->FromUserName;
        $re_fromUserName=$postObj->ToUserName;
        $time=time();
        switch($type)
        {
            //回复单文本消息
            case 'text':
                $MsgType='text';
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
            //回复多图文消息
            case 'news':
                $MsgType='news';
                //从数据库中获取
                $items=array(
                    array(
                        'title'=>'weChat developer document',
                        'description'=>'a detailed introduction to teach developer how to use its API',
                        'picurl'=>'http://markzhu.imwork.net/big.png',
                        'url'=>'http://mp.weixin.qq.com/s?__biz=MzI1NjM2MTQ1Nw==&mid=100000003&idx=1&sn=cef3b66d2cc22eb4ffc013310b03acb3&chksm=6a26979d5d511e8bd58f2626078af274caabf45c740ab57c3fd33d92dbf2ec85d0f890b9ba99#rd'
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