<?php
namespace MVC\Controller;
use MVC\Model\indexModel;
use MVC\Model\materialModel;

//测试账号的信息
define('APPID','wx9a7e00ceaf1818fc');
define('APPSECRET','be27d8bf1bcdde5065454e943341268c');
class IndexController
{
    public function index()
    {
        $material=new materialModel();
        echo $material->addImage();
        exit();
//        $this->customMenu();
//        exit;
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
//            echo getWeChatToken();
//            $this->responseMsg();
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
            $indexModel=new indexModel();
            switch(strtolower($postObj->MsgType))
            {

                case "event":
                case "text":
                    //自动回复一个消息
                    if(strtolower(trim($postObj->Content))=='news')
                    {
                        $indexModel->replies('news',$postObj);
                    }
                    else
                    {
                        $indexModel->replies('text',$postObj);
                    }
                break;
            }
        }
    }
    public function customMenu()
    {
        $token=json_decode(file_get_contents(getcwd().'/access_token'),true)['access_token'];
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$token;
        $menuData='
    {
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
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
            }]
       }]
 }';
        echo($this->httpPOST($url,$menuData));
    }

}