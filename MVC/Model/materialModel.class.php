<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/17/16
 * Time: 8:33 PM
 */
namespace MVC\Model;
class materialModel
{
    public function acquireMaterialList($type='image',$count=20)
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".getWeChatToken();
        $param='{"type":"'.$type.'","offset":"0","count":"'.$count.'"}';
        return httpPOST($url,$param);
    }
    public function addImage()
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".getWeChatToken()."&type=image";
        $file=getcwd().'/big.png';
        $param=array(
            'media'=>'@'.$file,
        );
        return httpPOST($url,$param);
    }
    public function addImageNews()
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=".getWeChatToken();
        $content="这是一片公众号测试图文文章，试试看能不能用呢？<img src='http://markzhu.imwork.net/small.png' alt='pic' />";
        $param='{
            "articles":[
                {
                    "title":"test",
                    "thumb_media_id":"yJcsFgqN3h98J9gD5nX6qwjbIQ_4u2VXy_byIp__dYo",
                    "author":"mark",
                    "digest":"摘要！",
                    "show_cover_pic":"1",
                    "content":"正文",
                    "content_source_url":"http://www.baidu.com"
                }
            ]
        }';
        $res=httpPOST($url,$param);
        var_dump($res);
    }
    public function acquireMaterial($media_id="yJcsFgqN3h98J9gD5nX6qwjbIQ_4u2VXy_byIp__dYo")
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".getWeChatToken();
        $param='{"media_id":"'.$media_id.'"}';
        return httpPOST($url,$param);
    }
    public function deleteMaterial($media_id)
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=".getWeChatToken();
        $param='{"media_id":"'.$media_id.'"}';
        return httpPOST($url,$param);
    }
}