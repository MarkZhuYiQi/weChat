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
        the_user()?$this->setViewName('index'):$this->login();

    }
    public function login()
    {
        $this->setViewName('login');
    }
    public function customMenu()
    {
        $this->setViewName('customMenu');
    }
    public function tree()
    {
        $tree=load_model('tree');
        $tree->loadAll();
        $treeList=array();
        $res=$tree->all();
        $original=array();
        //递归的问题都长个心眼，不要传对象进去
        foreach($res as $r)
        {
            $tempArr=array(
                'id'=>$r['id'],
                'tree_text'=>$r['tree_text'],
                'tree_pid'=>$r['tree_pid'],
                'tree_url'=>$r['tree_url'],
                'tree_state'=>$r['tree_state']
            );
            array_push($original,$tempArr);
        }
//        $original=objToArr($res);
        $fuck=$this->make_tree($original);
        exit(json_encode($fuck));
    }
    public function make_tree($res,$pid=0)
    {
        $node=array();
        foreach($res as $r) {
            if ($r['tree_pid'] == $pid) //找到子节点，初始为0
            {
                //先将目前取到的儿子放到数组里面去，然后再去检查有没有孙子
                $subNode = array(
                    'id' => $r['id'],
                    'text' => $r['tree_text'],
                    'state' => $r['tree_state'],
                    'attributes'=>array('url'=>$r['tree_url'])
                );
                if($tempArr=$this->make_tree($res,$r['id']))
                {
                    $subNode['children']=$tempArr;
                }
                array_push($node, $subNode);
            }
        }
        return $node;
    }
}