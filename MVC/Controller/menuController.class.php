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


        exit(json_encode($res));
    }
}