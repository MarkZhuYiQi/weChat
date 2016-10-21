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
        else
        {
            $userInfo=new \stdClass();
            $userInfo->userName=$get_userName;
            $userInfo->password=$get_password;
            $userInfo->user_loginIP=IP();
            $userInfo->user_loginTime=strtotime(date('Y-m-d H:i:s'));
            $cookie_string=serialize($userInfo);
            var_dump($userInfo);
            exit;
        }
    }
}