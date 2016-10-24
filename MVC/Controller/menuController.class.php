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
    function menuDetail()
    {
        $menu=load_model('menu');
        $menu->loadAll();
        $return=$menu->all();
        $res=objToArr($return);
        //下面这段玩意儿是用来吧menutype的数值转换为对应的值的。
        $type=$this->menuType(true);
        foreach($res as $k=>$r)
        {
            if($r['menu_pid']!=0)
            {
                $res[$k]['menu_pid']=$r['menu_text'];
            }
            else
            {
                $res[$k]['menu_pid']='ROOT';
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
        $res=objToArr($return);
        if($get)
        {
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
        $res=objToArr($return);
        array_push($res,array('menu_text'=>'ROOT'));
        exit(json_encode($res));
    }
    function receiveMenu()
    {
        $menuRec=(json_decode($_POST['json']));
        foreach($menuRec as $k=>$r)
        {

            if($r['menu_pid']=='ROOT')
            {
                $menuRec[$k]['menu_pid']=0;
            }
            else
            {
                
            }
        }






        $menu=load_model('menu_type');
        $menu->insert_update();
    }
}