<style>
    .tabs-panels{
        width:100%;
        height:100%;
    }
</style>
<script>
    $(document).ready(function(){
        $("#mytree").tree({
            url:"/ecs/m_index/tree/",
            animate:true,
            onClick:function(node){
                if(node.attributes && node.attributes.url) {
                    addTab(node.text,node.attributes.url);

//                    $("#mainframe").attr("src", node.attributes.url);

                }
            }
        });

        //加载树形菜单
    });
    function addTab(title,url){
        if($("#mainframe").tabs("exists",title)) {
            $("#mainframe").tabs("select", title);
        }else if(url=="/ecs/member/m_unlogin/"){
            window.location.href=url;
        }else{
            var content="<iframe src='"+url+"' width='100%' height='100%' frameborder='0'></iframe>";
            $("#mainframe").tabs("add",{
                title:title,
                content:content,
                closable:true
            });
        }
    }
</script>
<h2>Basic Layout</h2>
<div style="margin:20px 0;"></div>
<div data-options="region:'north'" style="height:50px;line-height:45px;font-size:26px;text-indent:5px;">
    微电商后台管理
</div>


<div data-options="region:'west',split:true" title="我的工作平台" style="width:200px;">

    <div class="easyui-accordion" data-options="fit:true,border:false">
        <div title="基础信息管理" style="padding:10px;" data-options="selected:true">
            <ul class="easyui-tree" id="mytree">

            </ul>

        </div>
        <div title="权限管理" style="padding:10px;">
            权限管理
        </div>
        <div title="系统信息管理" style="padding:10px">
            系统信息管理
        </div>
    </div>
</div>
<div data-options="region:'center',title:'Main Title',iconCls:'icon-ok'">

    <div class="easyui-tabs" style="width:100%;height:100%" id="mainframe" >

    </div>

</div>