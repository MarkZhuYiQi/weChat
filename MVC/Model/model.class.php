<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/19/16
 * Time: 1:36 PM
 */

namespace MVC\Model;

class model
{
    public $_dsn;
    public $_result;
    public $_db=false;
    public $_modelName='';

    function __construct($mName,$dsn)
    {
        //mName是要查询的表名
        //NOTORM用的初始化字符串
        $this->_dsn=$dsn;
        if($dsn==DB_DSN)
        {
            $this->_modelName=DB_PREFIX.'_'.$mName;
        }
        else
        {
            $this->_modelName=$mName;
        }
        $this->modelInit();
    }
    function modelInit()
    {
        load_Lib('db','NotORM');        //将notorm.php加载进来
        $pdo=new \PDO($this->_dsn,DB_USER,DB_PWD);
        $pdo->query("set time_zone = '+08:00'");
        $structure=new \NotORM_Structure_Convention(
            $primary='id',          //这里告诉NotORM我们的主键都是ID这种英文单词
            $foreign='%sid',        //同理，外键都是外表名+id,这很重要，否则NotORM拼接SQL都会拼错
            $table='%s',            
            $prefix=''              //表前缀
        );
        $date=$pdo->query("select `we_subscribeDate` from `we_user` where `we_id`=4  ");
        foreach($date as $row){
            $date=$row['we_subscribeDate'];
        }
//        $pdo->exec("set names utf8");
        $this->_db=new \NotORM($pdo,$structure);  //初始化
    }

    function load($where)           //加载表格
    {
        $tbName=$this->_modelName;  //表名
        if(trim($where)=='')return false;   //禁止程序员没有任何条件的加载全表
        $this->_result=$this->_db->$tbName()->select('*')->where($where)->limit(1);
    }

    function loadAll($cols="",$where="",$order="",$limit=""){                  //加载表格
        $tbName=$this->_modelName;          //表名
        if($cols==""){
            $this->_result=$this->_db->$tbName();
        }elseif($cols!=""&&$where==""){
            $this->_result=$this->_db->$tbName()->select($cols);
        }else{
            $this->_result=$this->_db->$tbName()->select($cols)->where($where);
            if($order=="")$this->_result->order($order);
            if($limit=="")$this->_result->limit($order);
        }
    }
    function all()
    {
        return $this->_result;
    }
    function insert($array,$tbName)
    {
        return $this->_db->$tbName()->insert($array);
    }
    function update($array,$tbName)
    {
        return $this->_db->$tbName()->update($array);
    }
    function delete($where)
    {
        $this->load($where);
        $this->_result->delete();
    }

    /**
     * @param $pname
     * @return bool
     * 魔术方法，获取私有变量
     * 对查询结果做处理，取出单条，然后返回单条结果
     */
    function __get($pname){     //魔术方法，在该对象下无法获取某个变量时就会执行他试图寻找变量
        if($this->_result && count($this->_result)==1){
            $ret=$this->_result[1];       //取出一条数据，也可以写成$this->_result[0], fetch()是取下一行的
            if($ret[$pname]){
                return $ret[$pname];
            }else{
                return false;
            }
        }
        return false;
    }
}