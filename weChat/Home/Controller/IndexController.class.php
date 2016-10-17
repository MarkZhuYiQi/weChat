<?php
namespace Home\Controller;
use Home\Model\indexModel;
use Think\Controller;
use Think\Model;
//测试账号的信息
define('APPID','wx9a7e00ceaf1818fc');
define('APPSECRET','be27d8bf1bcdde5065454e943341268c');
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
//            echo $this->getWeChatToken();
//            $this->responseMsg();
            echo $this->upImage();
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
    public function getWeChatToken()
    {
        $data = json_decode(file_get_contents(getcwd() . '/access_token'));
        if ($data->expire_time < time()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID . '&secret=' . APPSECRET;
            $output=$this->httpGET($url);
            $output = json_decode($output);
            $data->access_token = $output->access_token;
            $data->expire_time = time() + 7000;
            file_put_contents(getcwd() . '/access_token', json_encode($data));
        }
        return $data->access_token;
    }
    public function httpGET($url)
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
    public function httpPOST($url,$postParam=false,$header=false)
    {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //php5.5以上要加这句话
        curl_setopt($ch,CURLOPT_SAFE_UPLOAD,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        if($postParam)curl_setopt($ch,CURLOPT_POSTFIELDS,$postParam);
        if($header)curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_URL,$url);
        $res=curl_exec($ch);
        if(curl_errno($ch))echo 'Error!'.curl_error($ch);
        curl_close($ch);
        return $res;
    }
    public function upImage()
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->getWeChatToken();
        $file=dirname(__FILE__).'/big.png';
        $param=array(
            'media'=>'@'.$file
        );
        return $this->httpPOST($url,$param);
    }
}