<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/21/16
 * Time: 1:01 PM
 */
date_default_timezone_set('Asia/Shanghai');
require('vendor/autoload.php');
$get_control=isset($_GET["control"])?trim($_GET["control"].'Controller'):"m_indexController";
$get_action=isset($_GET["action"])?trim($_GET["action"]):"index";


$control=new $get_control();
if(method_exists($control,$get_action))
{
    $control->$get_action();
    $control->run();
}