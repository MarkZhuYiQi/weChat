<?php
//use MVC\Controller\indexController;
date_default_timezone_set('Asia/Shanghai');
require(getcwd()."/MVC/Conf/config.php");
require(getcwd()."/MVC/Common/function.php");
require(getcwd()."/MVC/Controller/indexController.class.php");
function __autoload($className)
{
    $className=str_replace('\\','/',$className);
    if(file_exists($className.'.class.php'))
    {
        require_once (getcwd().'/'.$className.'.class.php');
    }
}

$get_control=isset($_GET['control'])?trim($_GET['control'].'Controller'):'indexController';
$get_action=isset($_GET['action'])?trim($_GET['action']):'index';
$control=new $get_control();
if(method_exists($control,$get_action))
{
    /**
     * 这里可以进一步通过注释给方法加上权限
     */
    $control->$get_action();
}