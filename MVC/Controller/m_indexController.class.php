<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/21/16
 * Time: 2:17 PM
 */
namespace MVC\Controller;
use MVC\Controller\_Main;

class m_indexController extends _Main
{
    public function index()
    {
        if(!the_user())
        {
            $this->setViewName('login');
        }
    }
}