<?php
//namespace MVC\Controller;
use MVC\Model\indexModel;
use MVC\Model\materialModel;

//测试账号的信息
define('APPID','wx9a7e00ceaf1818fc');
define('APPSECRET','be27d8bf1bcdde5065454e943341268c');
class indexController
{
    public function index()
    {
        //自定义菜单设置
//        $this->customMenu();
//        $index=new indexModel();
//        $index->sendMsgAll();
//        $material=new materialModel();
//        var_export(json_decode($material->acquireMaterialList('news')));
//        $this->customMenu();
//        exit;

//        if($this->checkSignature())
//        {
        echo "123123132";
            $this->responseMsg();
//        }
    }
    /**
     * 回复类
     */
    public function responseMsg()
    {
        //获得发来的XML
        $postStr=file_get_contents('php://input');
        file_put_contents(getcwd().'/msg',$postStr);
        if(!empty($postStr))
        {
            $postObj=simplexml_load_string($postStr);
            $indexModel=new indexModel();
            //根据消息类型判断去向
            switch(strtolower($postObj->MsgType))
            {
                case "event":
                    $indexModel->eventHandler($postObj);
                    break;
                case "text":
                    //自动回复一个消息
                    $indexModel->textHandler($postObj);
                break;
            }
        }
    }
    //验证数据是否来自微信服务器
    public function checkSignature()
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
        if($tempStr==$signature)
        {
            return true;
        }
        return false;
    }
    //这个函数用于第一次验证服务器有效性
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature())
        {
            echo $echoStr;
            exit;
        }
    }

    public function customMenu()
    {
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getWeChatToken();
        $menuData='
    {
        "button":
        [
            {	
                "type":"click",
                "name":"最新发布",
                "key":"WE_NEWEST"
            },
            {
                "name":"菜单",
                "sub_button":
                [
                    {	
                        "type":"view",
                        "name":"搜索",
                        "url":"http://www.soso.com/"
                    },
                    {
                        "type":"view",
                        "name":"视频",
                        "url":"http://v.qq.com/"
                    },
                    {
                        "type":"click",
                        "name":"赞一下我们",
                        "key":"V1001_GOOD"
                    }
                ]
            },
            {
                "type":"click",
                "name":"关于我们",
                "key":"WE_ABOUT"
            }
        ]
    }';
        echo(httpPOST($url,$menuData));
    }
}