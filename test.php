<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/14/16
 * Time: 9:26 PM
 */
//define("APPID","wx26ab595b1d049a06");
//define("APPSECRET","c0e84fcfcecbbe3cec8e80189870d0b8");
define("APPID","wx9a7e00ceaf1818fc");
define("APPSECRET","be27d8bf1bcdde5065454e943341268c");
/**
 * 获取微信access_token
 * 返回2个参数
 * access_token:全局接口调用唯一凭据，至少512字符空间，有效期为2小时
 * expire_in:返回一个0-7200之间的数字，超过7200及2小时token失效，需要更新
 */

/*
date_default_timezone_set('Asia/Shanghai');



//get_access_token();
//get_weixin_ip();

function get_access_token()
{
    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
    $return=file_get_contents($url);
//    return json_decode($return)->access_token;
    foreach(json_decode($return) as $key=>$value)
    {
        echo $key .'='. $value.PHP_EOL;
    }
    file_put_contents(getcwd().'/access_token',$return);
}
function get_weixin_ip()
{
    $access_token=get_access_token();
    echo $access_token;
    $url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
    $ip=json_decode(file_get_contents($url));
    foreach($ip as $key =>$value)
    {
        echo var_export($value)."<br />";
    }
}


//curl实例
function http_curl()
{
    $url='http://www.baidu.com';
    //初始化curl
    $ch=curl_init();
    //设置curl参数
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    //采集
    $output=curl_exec($ch);
    curl_close($ch);
    var_dump($output);
}
//http_curl();

function getWeChatToken()
{
    //请求地址
    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    //调用接口
    $res=curl_exec($ch);
    if(curl_errno($ch))var_export(curl_error($ch));
    curl_close($ch);
    $arr=json_decode($res,true);
    file_put_contents(getcwd().'/access_token',$res);
    var_export($arr);
}
//getWeChatToken();
function getWeChatIp()
{
    $token=json_decode(file_get_contents(getcwd().'/access_token'),true)['access_token'];
    $url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$token;
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    $ip=curl_exec($ch);
    curl_close($ch);
    echo '<pre>';
    var_export(json_decode($ip,true));
    echo '</pre>';
}
//getWeChatIp();
function customMenu()
{
    $token=json_decode(file_get_contents(getcwd().'/access_token'),true)['access_token'];
    $url=' https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$token;
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
//customMenu();


*/
//-------------------------------------------------------------------------------------------------
require("vendor/autoload.php");
class model
{
    public $_dsn;
    public $_result;
    public $_db = false;
    public $_modelName = '';

    function __construct($mName, $dsn)
    {
        //mName是要查询的表名
        //NOTORM用的初始化字符串
        $this->_dsn = $dsn;
        if ($dsn == DB_DSN) {
            $this->_modelName = DB_PREFIX . '_' . $mName;
        } else {
            $this->_modelName = $mName;
        }
        $this->modelInit();
    }

    function modelInit()
    {
        $pdo = new \PDO($this->_dsn, 'root', '7777777y');
        $pdo->query("set time_zone = '+08:00'");
        $structure = new \NotORM_Structure_Convention(
            $primary = 'id',          //这里告诉NotORM我们的主键都是ID这种英文单词
            $foreign = '%sid',        //同理，外键都是外表名+id,这很重要，否则NotORM拼接SQL都会拼错
            $table = '%s',
            $prefix = ''              //表前缀
        );
        $date = $pdo->query("select `we_subscribeDate` from `we_user` where `we_id`=4  ");
        foreach ($date as $row) {
            $date = $row['we_subscribeDate'];
        }
//        $pdo->exec("set names utf8");
        $this->_db = new \NotORM($pdo, $structure);  //初始化
    }
    function insert($array)
    {
        $tbName=$this->_modelName;
        echo $this->_db->$tbName()->insert($array);
    }


}

$data=new model('menu',DB_DSN);
$data->insert(array('menu_text'=>'test','menu_type'=>'2','menu_key'=>'test'));