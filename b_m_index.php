<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/21/16
 * Time: 1:01 PM
 */

require(getcwd()."/MVC/Conf/config.php");
require(getcwd()."/MVC/Common/function.php");
//require(getcwd()."/MVC/Controller/indexController.class.php");
require(getcwd()."/MVC/Controller/m_indexController.class.php");
$get_control=isset($_GET["control"])?trim($_GET["control"]):"m_indexController";
$get_action=isset($_GET["action"])?trim($_GET["action"]):"index";

function __autoload($className)
{
    $className=str_replace('\\','/',$className);
    if(file_exists($className.'.class.php'))
    {
        require (getcwd().'/'.$className.'.class.php');
    }
}
$control=new $get_control();
if(method_exists($control,$get_action))
{
    $control->$get_action();
    $control->run();
}