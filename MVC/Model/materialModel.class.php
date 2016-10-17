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
    public function acquireMaterialList()
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".getWeChatToken();
        $param='{"type":"image","offset":"0","count":"20"}';
        return httpPOST($url,$param);
    }
    public function addImage()
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".getWeChatToken()."&type=image";
        $file=dirname(__FILE__).'/big.png';
        $param=array(
            'media'=>'@'.$file,
        );

        $param='{"media":"@$'.$file.'"}';
        $param=json_decode($param);

        return httpPOST($url,$param);
    }
    public function acquireMaterial($media_id)
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".getWeChatToken();
        $param='{"media_id":'.$media_id.'}';
        return httpPOST($url,$param);
    }
    public function deleteMaterial($media_id)
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=".getWeChatToken();
        $param='{"media_id":'.$media_id.'}';
        return httpPOST($url,$param);
    }
}