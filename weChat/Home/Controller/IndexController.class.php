<?php
namespace Home\Controller;
use Home\Model\indexModel;
use Think\Controller;
use Think\Model;
class IndexController extends Controller
{
    public function index()
    {
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
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$menuData);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        $res=curl_exec($ch);
        if(curl_errno($ch))echo 'Error!'.curl_error($ch);
        curl_close($ch);
        echo($res);
    }
}