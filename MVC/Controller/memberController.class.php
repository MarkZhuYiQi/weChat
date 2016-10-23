<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/21/16
 * Time: 9:36 PM
 */
namespace MVC\Controller;


class memberController
{
    function login()
    {
        $this->setViewName='login';
    }
    function login_action()
    {
        $get_userName=GET('username','post');
        $get_password=GET('password','post'); 
        $get_remember=intval(GET('remember','post'));
        if($get_userName==''||$get_password=='')
        {
            exit('Error！user name or password could not be null!');
        }
        $m=load_model('admin');
        $m->load(" `admin_userName`='".$get_userName."' ");
        foreach($m->all() as $r)
        {
            $db_password=$r['admin_password'];
        }
//        exit(myCrypt($get_password,USER_PASSWORD_CRYPT_KEY));
        if($get_password)
        {
            if($db_password==myCrypt($get_password,USER_PASSWORD_CRYPT_KEY))
            {
                $userInfo=new \stdClass();
                $userInfo->userName=$get_userName;
                $userInfo->password=$get_password;
                $userInfo->user_loginIP=IP();
                $userInfo->user_loginTime=strtotime(date('Y-m-d H:i:s'));
                $cookie_string=myCrypt(serialize($userInfo),BACKGROUND_ENCRYPTKEY);
                if($get_remember>0)
                {
                    $cookieTime=time()+60*60*24*7;  //一周时间
                }
                else
                {
                    $cookieTime=time()+1800;
                }
                setcookie(BACKGROUND_LOGINKEY,$cookie_string,$cookieTime,'/');
                exit('1');
            }
            exit(0);
        }
        else
        {
            exit(0);
        }
    }
    
    
    
}