<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/23/16
 * Time: 6:15 PM
 */

namespace MVC\Controller;


class menuController extends _Main
{
    public $res;        //存放从菜单数据库中取出的所有数据，数组格式
    function menuDetail($post=false)
    {
        $menu=load_model('menu');
        $menu->loadAll();
        $return=$menu->all();
        $res=objToArr($return);
        if($post)return $res;
        //下面这段玩意儿是用来吧menutype的数值转换为对应的值的。
        $type=$this->menuType(true);
        foreach($res as $k=>$r)
        {
            if($r['menu_pid']!=0)
            {
                $res[$k]['menu_pid']=$r['menu_pid'].'.'.$res[$r['menu_pid']-1]['menu_text'];
            }
            else
            {
                $res[$k]['menu_pid']='0.'.'ROOT';
            }
            foreach($type as $t)
            {
                if($t['id']==$r['menu_type'])
                {
                    $res[$k]['menu_type']=$t['type_text'];
                }
            }
        }
        exit(json_encode($res));
    }
    function menuType($get=false)
    {
        $type=load_model('menu_type');
        $type->loadAll();
        $return = $type->all();
        $res=objToArr($return,true);
        if($get)
        {
            $res=objToArr($return);
            return $res;
        }
        else
        {
            exit(json_encode($res));
        }
    }
    function menuFather()
    {
        $father=load_model('menu');
        $father->loadAll('id,menu_text',' menu_pid=0 ');
        $return = $father->all();
        $res=objToArr($return,true);
        array_push($res,array('menu_text'=>'ROOT'));
        exit(json_encode($res));
    }

    /**
     * ajax提交到的目的地，接受各种针对数据库操作的数组，然后进行操作
     */
    function receiveMenu()
    {
        $inserted=(json_decode($_POST['inserted']));
        $deleted=(json_decode($_POST['deleted']));
        $updated=(json_decode($_POST['updated']));
        $inserted=count($inserted)>0?$this->menuArr($inserted):null;
        $deleted=count($deleted)>0?$this->menuArr($deleted):null;
        $updated=count($updated)>0?$this->menuArr($updated):null;
        $this->insert($inserted);
        $this->deleteUpdate($deleted,'delete');
        $this->deleteUpdate($updated,'update');



        exit();
    }
    function insert($inserted)
    {
        if($inserted!=null)
        {
            $menu=load_model('menu');
            if(!$menu->insert_multi($inserted))
            {
                exit('ERROR! 插入失败！');
            }
        }
    }
    function deleteUpdate($arr,$operation)
    {
        if($arr!=null)
        {
            $menu=load_model('menu');
            foreach($arr as $k=>$v)
            {
                $where=' `id` = "'.$v['id'].'" ';
                if(!$menu->$operation($where))
                {
                    exit('ERROR! 失败！');
                }
            }
        }
    }
    /**
     * @param $arrObj   传入一个数组，数组里包含多个需要对数据库操作的对象
     * @return mixed
     */
    function menuArr($arrObj)
    {
        $temp=array();
        if(isset($arrObj))
        {
            foreach($arrObj as $k => $v)
            {
                unset($v->status);
                foreach($v as $key=>$value)
                {
                    if($value)$temp[$k][$key]=$value;
                }
            }
        }
        return $temp;
    }

    /**
     * 提交菜单变更到微信服务器
     */
    function postMenu()
    {
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getWeChatToken();
        $string='';
        $menuDetail=$this->menuDetail(true);
        foreach($menuDetail as $key =>$value)
        {
            if($value['menu_pid']==0)
            {
                foreach($value as $k => $v)
                {
                    if($v==null || $v=='')
                    {
                        unset($menuDetail[$key][$k]);
                    }
                }
            }
            $string=$this->menuJsonBuild($value,$string);
        }
        $menuData='{"button":['.$string.']}';
//        var_export($menuDetail);
        var_export($menuData);
        exit();
        $reply=json_decode(httpPOST($url,$menuData));
        if(!$reply->errcode)
        {
            echo 'success';
        }
        exit;

    }

    /**
     * @param $value    该值是从菜单数据中循环出的其中一个项目集合，数组
     * @param $string   该值是最后组成的
     */
    function menuJsonBuild($value,$string)
    {
        if($value['menu_pid']==0)
        {
            switch ($value['menu_type'])
            {
                case 'click':
                    $string.='{"type":"click","name":"'.$value['menu_text'].'","key":"'.$value['menu_key'].'"},';
                    break;
                case 'view':
                    if(isset($value['menu_key']))
                    {
                        $tempView='"key":"'.$value['menu_key'].'"';
                    }elseif(isset($value['menu_url']))
                    {
                        $tempView='"url":"'.$value['menu_url'].'"';
                    }
                    $string.='{"type":"view","name":"'.$value['menu_text'].'",'.$tempView.'},';
                    break;
                case 'father':
                    $string.='{"name":"'.$value['menu_text'].'","sub_button":['.$this->_subMenu($value['id']).']},';
                    break;
                default:
                    exit('Incorrect Menu Type!');
            }
        }
        return substr($string,0,count($string)-2);
    }

    /**
     * 生成微信json的子程序
     * @param $pid
     * @return string
     */
    function _subMenu($pid)
    {
        $subMenu='';
        $menuDetail=$this->menuDetail(true);
        foreach($menuDetail as $key => $value)
        {
            if($value['menu_pid']==$pid)
            {
                switch ($value['menu_type'])
                {
                    case 'click':
                        $subMenu.='{"type":"click","name":"'.$value['menu_text'].'","key":"'.$value['menu_key'].'"},';
                        break;
                    case 'view':
                        if(isset($value['menu_key']))
                        {
                            $tempView='"key":"'.$value['menu_key'].'"';
                        }elseif(isset($value['menu_url']))
                        {
                            $tempView='"url":"'.$value['menu_url'].'"';
                        }
                        $subMenu.='{"type":"view","name":"'.$value['menu_text'].'",'.$tempView.'},';
                        break;
                }
            }
        }
        return $subMenu;
    }
}