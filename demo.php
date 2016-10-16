<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/16/16
 * Time: 9:27 PM
 */
    header("Content-type: text/html; charset=utf-8");
    define ( "TOKEN", "markzhu" );

    $wechatObj = new WeiXin();

    class WeiXin{
        private static $ACCESS_TOKEN = 0;  //当前access_token的值
        private static $ACCESS_TIME = 0;  //access_token最后一次更新的时间
        private static $XMLDATA;  //接收请求的xml数据
        private static $ARRELE = array();  //存放请求的xml节点 用来判断是属于那种请求
        public function __construct()
        {
            // $this->valid();die;   //服务器验证
            // $this->createMenu();die;   //自定义菜单
            $path = dirname(__FILE__);
            $acpath = $path."/weixin.c";  //存放最近一次access_token更新的时间
            $access = $path."/access_token.c";  //最近一次更新的access_token
            $xml = $path."/xmldata.c";   //存放最近一次接收的xml数据
            //检查access_token是否过期
            if(!file_exists($acpath)){
                $this->getAssessToken();
                file_put_contents($acpath, self::$ACCESS_TIME);
                file_put_contents($access, self::$ACCESS_TOKEN);
            }else
            {
                $acint = file_get_contents($acpath)+0;
                if((time()-$acint)>=7000)
                {
                    $this->getAssessToken();
                    file_put_contents($acpath, self::$ACCESS_TIME);
                    file_put_contents($access, self::$ACCESS_TOKEN);
                }else
                {
                    self::$ACCESS_TOKEN = file_get_contents($access);
                }
            }
            //校验信息真实性（防止微信以外的第三方灌水机器人）
            $this->checkSignature();
            //将请求的xml数据存为全局
            self::$XMLDATA = $GLOBALS["HTTP_RAW_POST_DATA"];
            //添加xml数据日志
            file_put_contents($xml, self::$XMLDATA);

            $this->begin();
        }
        private function begin()
        {

            $postObj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);
            foreach ($postObj as $key => $value) {
                array_push(self::$ARRELE,$key);
            }

            //事件消息
            if(in_array("Event", self::$ARRELE))
            {
                //新用户关注
                if($postObj->Event=="subscribe")
                {
                    $textTpl = "  
                    <xml>  
                    <ToUserName><![CDATA[".$postObj->FromUserName."]]></ToUserName>  
                    <FromUserName><![CDATA[".$postObj->ToUserName."]]></FromUserName>  
                    <CreateTime>".time()."</CreateTime>  
                    <MsgType><![CDATA[news]]></MsgType>  
                    <ArticleCount>2</ArticleCount>  
                    <Articles>  
                    <item>  
                    <Title><![CDATA[爱代驾送福利：百万代驾券、iPhone6大奖等你来拿！]]></Title>   
                    <Description><![CDATA[爱代驾送福利]]></Description>  
                    <PicUrl><![CDATA[http://aidaijia.com/Public/Index/image/tu1.jpg]]></PicUrl>  
                    <Url><![CDATA[http://aidaijia.com/home]]></Url>  
                    </item>  
                    <item>  
                    <Title><![CDATA[酒后有车咋个走，爱代驾只需29]]></Title>  
                    <Description><![CDATA[爱代驾最低29元起]]></Description>  
                    <PicUrl><![CDATA[http://aidaijia.com/Public/Index/image/syx4.jpg]]></PicUrl>  
                    <Url><![CDATA[http://aidaijia.com/Index/Index/price]]></Url>  
                    </item>  
                    </Articles>  
                    </xml>";
                    echo $textTpl;
                    return ;
                }
                //取消关注
                if($postObj->Event=="unsubscribe")
                {
                    $sql = "delete from dj_weixin where wuser='$postObj->FromUserName'";
                    $mysqli = new mysqli("localhost", "username", "666666", "driver");
                    $mysqli->query($sql);
                    $mysqli->close();
                    return ;
                }

                //更新用户的坐标
                if($postObj->Event=='LOCATION')
                {
                    //上报地理位置事件
                    if(in_array("Latitude",self::$ARRELE) )
                    {
                        $time = time();
                        $obj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);
                        $sql = "insert into `dj_weixin` (`wuser`, `wlon`, `wlat`, `wtime`,`wto`) values ('$obj->FromUserName', $obj->Longitude, $obj->Latitude, $time,'$obj->ToUserName') ON DUPLICATE KEY UPDATE `wlon` = $obj->Longitude, `wlat` = $obj->Latitude, `wtime` =$time, `wto` ='$obj->ToUserName' ;";
                        // $access = dirname(__FILE__)."/access_token.c";
                        // file_put_contents($access, $sql);
                        $mysqli = new mysqli("localhost", "username", "666666", "driver");
                        $mysqli->query($sql);
                        $mysqli->close();
                        return;
                    }
                }

            }
            //用户主动发送了位置信息
            if(in_array("Location_X",self::$ARRELE))
            {
                self::fdriver();
                return;
            }
            //自定义菜单事件
            if(in_array("EventKey",self::$ARRELE))
            {

                if(substr($postObj->EventKey,0,3)=='WD_')
                {
                    self::menuevent();
                    return ;
                }
            }
            //普通消息
            if($postObj->MsgType=='text')
            {
                self::responseMsg();
                return;
            }
        }

        private function checkSignature(){
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $token = TOKEN;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );

            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }


        //获取access_token
        public function getAssessToken(){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxfa50fcdbae10f8c8&secret=e845e613792e369034a91b9bd2db513a ";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  //以文件流的形式返回
            $data = curl_exec($curl);
            $arr = json_decode($data, true); //把返回的json字符串转化为数组
            self::$ACCESS_TOKEN = $arr['access_token'];  //更新access_token
            self::$ACCESS_TIME = time();  //最后一次获取access_token的时间
            curl_close($curl);
            // echo self::$ACCESS_TOKEN."<br>";
            // echo self::$ACCESS_TIME."<br>";
        }



        //回复文本消息
        public function responseMsg()
        {
            $postObj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $content = $postObj->Content;
            $contentStr = "";
            if(substr($content,0,1)=='@')
            {
                $pos = substr($content,1);
                if(strlen($pos)!=11)
                {
                    $contentStr = "温馨提示：请填写正确的手机号";
                }else
                {
                    $path = dirname(__FILE__);
                    $xmlele = $path."/xmlele.c";
                    $sql = "select * from dj_order_info where from_phonenum='$pos' order by orderid desc limit 4";
                    $mysqli = new mysqli("localhost", "username", "666666", "driver");
                    $result = $mysqli->query($sql);
                    $mysqli->close();
                    while($obj = $result->fetch_object()){
                        $contentStr.="单号".$obj->order_num."\n------->时间".substr($obj->call_time,5,5)."\n";
                    }
                    if($contentStr=='')
                        $contentStr = "你好，没有查询到相关订单";
                }
            }
            $time = time();
            $textTpl = "<xml>  
                        <ToUserName><![CDATA[%s]]></ToUserName>  
                        <FromUserName><![CDATA[%s]]></FromUserName>  
                        <CreateTime>%s</CreateTime>  
                        <MsgType><![CDATA[%s]]></MsgType>  
                        <Content><![CDATA[%s]]></Content>  
                        <FuncFlag>0</FuncFlag>  
                        </xml>";
            $msgType = "text";
            if($contentStr=='')
                $contentStr = "你好，我是爱代驾，你可以在这里快速找代驾，近期订单查询，进入微官网还有更多惊喜，24小时客服热线:4000700029  
            爱代驾是一家历史悠久的代驾公司，公司本着以人为本，诚信经营的宗旨，在全国享有很高的声誉  
            ";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        }
        // 自定义菜单事件消息
        public static function menuevent()
        {
            $obj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);
            $eventKey = $obj->EventKey;
            $fromUsername = $obj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $time = time();
            $msgType = "text";
            $content = $eventKey;
            switch($eventKey)
            {
                case "WD_JIAGE":
                    $content = "7:00-21:00=>29元起\n21:00-24:00=>49元起\n0:00-7:00=>69元起\n"; break;
                case "WD_ZHAODAIJIA":
                    self::finddriver($fromUsername);break;
                // $content = "请发送你的位置以便寻找附近司机:\n1、点击左下方 '小键盘' \n2、点击右下方 '+号键' \n3、点击 '位置' 图标\n4、完成定位后点击 '发送'"; break;
                case "WD_DINGDAN":
                    $content = "请发送@+手机号查询"; break;
                case "WD_FUWUREXIAN":
                    $content = "24小时服务热线：4000-7000-29"; break;
                default:
                    // $content = "无法识别";
            }
            $textTpl = "<xml>  
                        <ToUserName><![CDATA[%s]]></ToUserName>  
                        <FromUserName><![CDATA[%s]]></FromUserName>  
                        <CreateTime>%s</CreateTime>  
                        <MsgType><![CDATA[%s]]></MsgType>  
                        <Content><![CDATA[%s]]></Content>  
                        <FuncFlag>0</FuncFlag>  
                        </xml>";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
            echo $resultStr;
        }
        //寻找附近司机，针对于开启了位置服务的用户，我方公众号需要在公众平台开启高级权限接口并启用位置服务
        public static function finddriver($username)
        {
            // $access = dirname(__FILE__)."/access_token.c";

            $sql = "select * from dj_weixin where wuser = '$username'";
            $mysqli = new mysqli("localhost", "username", "666666", "driver");
            $res = $mysqli->query($sql);
            $mysqli->close();
            $count = 0;
            if($res->num_rows!=0)
            {
                $obj = $res->fetch_object();
                $lon = $obj->wlon;
                $lat = $obj->wlat;
                $fromUsername = $obj->wuser;
                $toUsername = $obj->wto;
                $time = $obj->wtime;
                $nowtime = time();
                if(($nowtime-$time)>120)
                {
                    $count=-1;
                    goto flag;
                }

            }else
            {
                $obj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $obj->FromUserName;
                $toUsername = $obj->ToUserName;
                $count = -1;
                goto flag;
            }
            $sql = "call queryDriverByClient($lat,$lon)";
            $mysqli = new mysqli("localhost", "username", "66666666", "driver");
            $result = $mysqli->query($sql);
            $mysqli->close();
            $count = $result->num_rows;
            //如果没有获取到用户的地理信息 直接跳到这里
            flag :
            $textTpl = "<xml>  
                        <ToUserName><![CDATA[%s]]></ToUserName>  
                        <FromUserName><![CDATA[%s]]></FromUserName>  
                        <CreateTime>%s</CreateTime>  
                        <MsgType><![CDATA[text]]></MsgType>  
                        <Content><![CDATA[%s]]></Content>  
                        <FuncFlag>0</FuncFlag>  
                        </xml>";
            if($count>0)
            {
                $content = "你附近有".$count."个空闲司机。\n马上去选司机: http://aidaijia.com/find?lat=".$lat."&lon=".$lon;
            }
            else
            {
                if($count==-1)
                    $content = "无法获取你的地理位置，请点击爱代驾头像，在资料页面把[提供位置信息]开启\n你也可以手动发送地理位置信息：\n1、点击左下方 '小键盘' \n2、点击右下方 '+号键' \n3、点击 '位置' 图标\n4、完成定位后点击 '发送'";
                else
                    $content = "抱歉！你附近暂没有空闲司机。你可以等会儿在操作或拨打客服咨询4000-7000-29\n";
            }
            $time = time();
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,     $content);
            echo $resultStr;
        }

        //寻找附近的司机  针对没有开启位置服务的用户  (需要用户手动发送位置信息)
        public static function fdriver()
        {
            $obj = simplexml_load_string(self::$XMLDATA, 'SimpleXMLElement', LIBXML_NOCDATA);

            $lon = $obj->Location_Y ;   //经度
            $lat = $obj->Location_X ;  //纬度
            $fromUsername = $obj->FromUserName;
            $toUsername = $obj->ToUserName;
            $sql = "call queryDriverByClient($lat,$lon)";
            $mysqli = new mysqli("localhost", "username", "666666", "driver");
            $result = $mysqli->query($sql);
            $mysqli->close();
            $count = $result->num_rows;
            $textTpl = "<xml>  
                        <ToUserName><![CDATA[%s]]></ToUserName>  
                        <FromUserName><![CDATA[%s]]></FromUserName>  
                        <CreateTime>%s</CreateTime>  
                        <MsgType><![CDATA[text]]></MsgType>  
                        <Content><![CDATA[%s]]></Content>  
                        <FuncFlag>0</FuncFlag>  
                        </xml>";
            if($count>0)
            {
                $content = "你附近有".$count."个空闲司机。\n马上去选司机: http://aidaijia.com/find?lat=".$lat."&lon=".$lon;
            }
            else
            {
                $content = "抱歉！你附近暂没有空闲司机。你可以等会儿在操作或拨打客服咨询4000-7000-29\n";
            }
            $time = time();
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
            echo $resultStr;
        }

        //设置菜单
        public function createMenu(){
            $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=DWJG0l5MhgkJBDfjg032jnOfjwZX3Fatk1PccFNgJuRYr0b8I3w-jd77P2_p95pJd4xtFB_DdZRi5k-WHaSNGA";
            $btnjson =  '{  
                 "button":[{      
                      "type":"click",  
                      "name":"找代驾",  
                      "key":"WD_ZHAODAIJIA"  
                  },  
                  {  
                      "name":"微助手",  
                       "sub_button":[  
                       {  
                           "type":"view",  
                          "name":"微官网",  
                          "url":"http://aidaijia.com/home"  
                       },  
                       {  
                           "type":"view",  
                           "name":"路况信息",  
                           "url":"http://map.baidu.com/mobile/webapp/index/index/foo=bar/vt=map&traffic=on&viewmode=no_ad/?third_party=ucsearchbox#index/index/foo=bar/vt=map&traffic=on&viewmode=no_ad"  
                       },  
                       {  
                           "type":"click",  
                          "name":"最近订单",  
                          "key":"WD_DINGDAN"  
                       }  
                       ]  
                        
                  },   
                  {  
                       "name":"更多服务",  
                       "sub_button":[  
                       {  
                           "type":"click",  
                           "name":"服务热线",  
                           "key":"WD_FUWUREXIAN"  
                       },  
                       {  
                           "type":"click",  
                           "name":"价格列表",  
                           "key":"WD_JIAGE"  
                       },  
                       {  
                           "type":"view",  
                           "name":"下载客户端",  
                           "url":"http://aidaijia.com/d"  
                       }  
                         
                   ]  
                  }]  
             }';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $btnjson);
            $data = curl_exec($curl);
            curl_close($curl);
            echo $data;
        }

        //验证服务器
        public function valid()
        {
            $echoStr = $_GET["echostr"];

            //valid signature , option
            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }

        //收尾工作
        public function __destruct()
        {
            // ..
        }




    }
?>