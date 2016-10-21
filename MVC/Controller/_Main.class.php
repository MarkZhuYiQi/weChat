<?php
/**
 * Created by PhpStorm.
 * User: red
 * Date: 10/21/16
 * Time: 1:02 PM
 */

namespace MVC\Controller;


class _Main
{
    public $_viewName="index";  //查询
    public $_objList=array();   //变量数组
    public $cache_time=0;       //0的话就没有缓存处理
    public $isFileCache=false;   //默认是否保存文件缓存
    public $isAdmin=false;      //是否是后台管理
    function run()
    {
        if ($this->cache_time > 0) {
            $getVars = get_cache($this->_viewName);
            if ($getVars) {
                echo 'use cache!';
                extract($getVars);
            } else {
                //将objList数组放到视图模板名称变量下
                set_cache($this->_viewName, $this->_objList, $this->cache_time);
                extract($this->_objList);
            }
        } else {
            extract($this->_objList);
        }
        ob_start();
        $view_path = CURRENT_VIEWPATH;
        if ($this->isAdmin) $view_path = CURRENT_VIEWPATH_ADMIN;
        include('MVC/View/' . $view_path . '/head.tpl');
        include('MVC/View/' . $view_path . '/' . $this->_viewName . '.tpl');
        include('MVC/View/' . $view_path . '/footer.tpl');
        $getContent = ob_get_contents();
        ob_clean();
        if ($this->isFileCache)
        {
            $fileName = md5($_SERVER['REQUEST_URI']);
            if (file_exists(CACHE_PATH, $fileName))
            {
                echo 'use_file_cache!';
                echo file_get_contents(CACHE_PATH . $fileName);   //直接加载模板文件
            }
            else
            {
                $getContent = $this->generateTpl($getContent);
                file_put_contents(CACHE_PATH.$fileName,$getContent);
                echo $getContent;
            }
        }
        else
        {
            echo $this->generateTpl($getContent);
        }
    }


    function generateTpl($content)
    {
        $content=$this->getInclude($content);
        $content=$this->getForeach($content);
        $content=$this->getSimpleVars($content);
        return $content;

    }

    /**
     * @param $vname
     * 设置视图模板名称
     */
    function setViewName($vname){
        //设置view的名称
        $this->_viewName=$vname;
    }


    /**
     * @param $tplContent
     * @return mixed
     * 用于解析单变量
     * 获取到本来输出到缓冲区的内容，用正则表达式匹配需要的关键字
     * 然后替换成需要的结果值
     */
    function getSimpleVars($tplContent){
        if(preg_match_all("/\{(?<varObject0>[^\{]*?\(\'(?<varObject1>[\w\.]{1,30})\'\))\}|{(?<varObject2>[\w\.]{1,30}?)}/is",$tplContent,$result)){
            $varObject0=$result["varObject0"];      //如果有函数包含，值为函数包含的变量名，否则为空
            $varObject1=$result["varObject1"];      //如果有函数，值为变量名
            $varObject2=$result["varObject2"];      //如果没有函数，值为变量名
            $result=$result[0];
            foreach ($result as $r){
                $var0=current($varObject0);     //获得该数组当前指针指向的元素，初始为0
                $var1=current($varObject1);
                $var2=current($varObject2);
                $getVar=$var1==""?$var2:$var1;
                if($getVar=="")$getVar=$var0;
                if("{".$getVar."}"==$r){        //如果直接是一个变量
                    if(array_key_exists($getVar,$this->_objList)){
                        $tplContent=preg_replace("/".$r."/",$this->_objList[$getVar],$tplContent);
                    }
                }else{                          //如果是被函数包围了的变量
                    if(array_key_exists($getVar,$this->_objList)){
                        $newr=str_replace($getVar,$this->_objList[$getVar],$r); //将其中的变量替换掉
                        $newr=str_replace(array("{","}"),"",$newr);             //再将{}大括号去掉
                        eval('$last='.$newr.";");                               //执行函数
                        if($last){
                            $tplContent=str_replace($r,$last,$tplContent);      //将执行的结果替换给原文
                        }
                    }
                }
                $var0=next($varObject0);        //指向指针下移一位
                $var1=next($varObject1);
                $var2=next($varObject2);
            }
            return $tplContent;
        }else{
            return $tplContent;
        }

    }

    function getInclude($tplContent){
        if(preg_match_all("/\{include\s+\"([\w\.]{1,30})\s*\"\}/is",$tplContent,$result)){
            $result=$result[1];
            foreach($result as $r){
                if(file_exists(INCLUDE_PATH.$r)){
                    $getFile=file_get_contents(INCLUDE_PATH.$r);
                    $tplContent=preg_replace("/\{include\s+\"".$r."\s*\"\}/is",$getFile,$tplContent);
                }
            }
        }
        return $tplContent;
    }

    /**
     * @param $replaceContent   需要循环的内容
     * @param $varName          foreach循环中as后面的那个变量
     * @param $row              需要循环的数组在列表中的值
     * @return mixed
     */
    function replaceForeachVars($replaceContent,$varName,$row)
    {
        //替换循环内部内容  red('user.user_name')
        if (preg_match_all("/{(.*?)}/is", $replaceContent, $result)) {
            $result = $result[1];
//            var_dump($result);
            foreach ($result as $r) {
                //根据{}中内容取出变量值，如user.username，匹配出username
                if(!preg_match_all("/".$varName."\.(?<varValue>\w{1,30})/is",$r,$varResult)) continue;
                $varList = $varResult["varValue"];        //取出匹配到的集合,所有varValue匹配到的值
                if (count($varList) == 1 && $varName . "." . $varList[0]==trim($r)) { //这种情况下代表没有函数
                    $varValue = $varList[0];  //没有函数只有变量且只有一个
                    if ($row[$varValue]) {
                        $replaceContent = preg_replace("/{" . $varName . "\." . $varValue . "}/is", $row[$varValue], $replaceContent);
                    }
                } else {        //代表有函数
                    $newr = $r;
                    foreach ($varList as $varValue) {     //有函数的情况下循环里面所有变量
                        if (!$row[$varValue]) continue;
                        $newr = preg_replace("/" . $varName . "\." . $varValue . "/is", $row[$varValue], $newr);

                    }
                    eval('$last=' . $newr . ';');
//                    var_dump($last);
                    if ($last) {
//                        $tplContent = str_replace("{" . $r . "}", $last, $replaceContent);
                        /**
                         * 2016-09-19修改成如下：
                         * 如果每次都用$replaceContent去替换，由于foreach里面不止一个带函数的变量，所以一个foreach中会循环几次
                         * 那么每次只替换$replaceContent中的一项内容并赋给新变量，每次循环赋值一次，最后得到的结果是将最后一个结果替换
                         * 完的结果而不是将所有变量替换完的结果。
                         */
                        $replaceContent = str_replace("{" . $r . "}", $last, $replaceContent);
                    }
                }
            }
        }
        if (isset($replaceContent)) {
            return $replaceContent;
        }
    }

    function getForeach($tplContent){
        /**
         * 注意：这里在括号内头部加入?<NAME>相当于给该位置的变量设定了别名，遍历出来可以得知在result中增加了一个以NAME命名的数组
         *      同时原来的数组依然做保留，也就等于说有两个名字不同但是内容完全相同的数组。
         * 为了区别同一页面不同的foreach需要给每个foreach加上唯一标识符
         */
        global $foreach_id;         //外部变量
        //逐个替换页面中的标记并做唯一标识符
        $tplContent=preg_replace_callback("/(foreach):([a-zA-Z]{1,30})/is","foreachCallBack",$tplContent);
        //有几个foreach循环就会循环几次
        foreach($foreach_id as $fid){
            //有几个foreach就循环取出几个。找出对应id的foreach
            $pattern="/{foreach\:(?<varObject>[\w]{1,30})\:".$fid."\s+name=\"(?<varName>[\w]{1,30}?)\"}/";
            if(preg_match_all($pattern,$tplContent,$result)){
                $finalResult="";
                $varObject=$result["varObject"][0];     //获得每次需要循环取出的数组
                $varName=$result["varName"][0];         //作为来传递每次的值的变量
                if($this->_objList[$varObject]){       //寻找该数组是否在已被赋值的列表中
                    //取出循环的中间部分
                    $pattern="/{foreach:".$varObject.":".$fid."\s+.*?}(?<replaceContent>.*?){\/foreach}/is";
                    if(preg_match($pattern,$tplContent,$contentResult)){    //
                        $contentResult = $contentResult["replaceContent"];   //取出需要循环的内容
                        foreach ($this->_objList[$varObject] as $row) {       //将列表中的值取出来
                            $tmp = $this->replaceForeachVars($contentResult, $varName, $row);
                            $finalResult .= $tmp;
                        }
                    }
                }
                //替换最终foreach的值
                $tplContent=preg_replace('/{foreach:'.$varObject.':'.$fid.'\s+.*?}.*?{\/foreach}/is',$finalResult,$tplContent);
            }
        }
        return $tplContent;
    }

}



