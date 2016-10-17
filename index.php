<?php
require(getcwd()."/MVC/Common/function.php");
use MVC\Controller\IndexController;
//require("MVC/Controller/IndexController.class.php");
//require("MVC/Model/indexModel.class.php");
//require("MVC/Model/materialModel.class.php");
function __autoload($className)
{
    $className=str_replace('\\','/',$className);
    if(file_exists($className.'.class.php'))
    {
        require_once (getcwd().'/'.$className.'.class.php');
    }
}
$C=new IndexController();
$C->index();