<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="MVC/View/v1/css/bootstrap.css">

<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="MVC/View/v1/css/bootstrap-theme.css">

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<!--<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>-->

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="MVC/View/v1/js/bootstrap.min.js"></script>
<style>
    .panel-body{
        padding:0;
    }
</style>


<div style="padding:0 10px;">
    <div class="row clearfix">
        <div class="col-md-8 column">
            <h2>Row Editing in DataGrid</h2>
            <p>Click the row to start editing.</p>
            <div style="margin:20px 0;"></div>

            <table id="dg" class="easyui-datagrid" title="Customize WeChat Menu" style="height:auto"></table>

            <div id="tb" style="height:auto">
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Append</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeit()">Remove</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept()">Accept</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="reject()">Reject</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-search',plain:true" onclick="getChanges()">GetChanges</a>
            </div>
        </div>

        <div class="col-md-4 column" style="border:1px solid #cccccc">
            <div id="buttons" style="text-align:center;padding-top:300px;">
                <div class="btn-group" id="menuStyle">
                    <button type="button" class="btn btn-default">Left</button>
                    <button type="button" class="btn btn-default">Middle</button>
                    <button type="button" class="btn btn-default">Right</button>
                    <div class="btn-group dropup">
                        <button type="button" class="btn btn-default">Dropup</button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <!-- Dropdown menu links -->
                            <li><a href="#">test1</a></li>
                            <li><a href="#">test2</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    var editIndex = undefined;
    function endEditing(){
        if (editIndex == undefined){return true}
        if ($('#dg').datagrid('validateRow', editIndex)){
            var ed = $('#dg').datagrid('getEditor', {index:editIndex,field:'menu_type'});
            var menuType = $(ed.target).combobox('getText');
            $('#dg').datagrid('getRows')[editIndex]['menu_type'] = menuType;
            $('#dg').datagrid('endEdit', editIndex);
            editIndex = undefined;
            return true;
        } else {
            return false;
        }
    }
    function onClickRow(index){     //包含2个参数，index被点击行的索引，从0开始;Data被点击行对应的记录
        if (editIndex != index){
            if (endEditing()){
                $('#dg').datagrid('selectRow', index)
                        .datagrid('beginEdit', index);
                editIndex = index;
            } else {
                $('#dg').datagrid('selectRow', editIndex);
            }
        }
    }
    function append(){
        if (endEditing()){
            $('#dg').datagrid('appendRow',{status:'P'});
            editIndex = $('#dg').datagrid('getRows').length-1;
            $('#dg').datagrid('selectRow', editIndex)
                    .datagrid('beginEdit', editIndex);
        }
    }
    function removeit(){
        if (editIndex == undefined){return}
        $('#dg').datagrid('cancelEdit', editIndex)
                .datagrid('deleteRow', editIndex);
        editIndex = undefined;
    }
    function accept(){
        if (endEditing()){
            $('#dg').datagrid('acceptChanges');
        }
    }
    function reject(){
        $('#dg').datagrid('rejectChanges');
        editIndex = undefined;
    }
    //get changes按钮功能
    function getChanges(){
//        var rows = $('#dg').datagrid('getChanges');
//        alert(rows.length+' rows are changed!');
        var rows=$('#dg').datagrid('getRows');
        var json='';
        for(var i=0;i<rows.length;i++){
            var row=rows[i];
            rows[i]['menu_type']=changeMenuType(row);
        }
        json=JSON.stringify(rows);
        $.ajax({
            url:'?control=menu&action=receiveMenu',
            data:{"json":json},
            type:'POST',
            dataType:'json',
            success:function(callback){
                alert(callback);
            }
        });

    }
    function changeMenuType(row){
            switch(row.menu_type){
                case 'click':
                    return 1;
                case 'view':
                    return 2;
                case 'scancode_push':
                    return 3;
                case 'scancode_waitmsg':
                    return 4;
                case 'pic_sysphoto':
                    return 5;
                case 'pic_photo_or_album':
                    return 6;
                case 'pic_weixin':
                    return 7;
                case 'location_select':
                    return 8;
                case 'media_id':
                    return 9;
                case 'view_limited':
                    return 10;
            }
    }
    $(document).ready(function(){
        $('#dg').datagrid({
            iconCls: 'icon-edit',
            striped: true, //行背景交换
            idField: 'id', //主键
            fitColumns:true,    //自动宽度防止滚动
            singleSelect: true,
            toolbar: '#tb',
            url: '?control=menu&action=menuDetail',
            method: 'get',
            onClickRow: onClickRow,
            rowStyler: function (index, row) {          //定义为返回样式字符串用于定义数据表格的样式，2个参数，行的索引和相应记录
                return 'background-color:#fff;color:#666666;';
            },
            columns: [
                [
                    {field: 'id', title: 'Menu Id', align: 'center'},
                    {field: 'menu_text', title: 'Menu Name', align: 'center', editor: 'text'},
                    {
                        field: 'menu_type', title: 'Menu Type', align: 'center',width:'15%',
                        formatter: function (value, row, index) {   //value是该行的值，row是该行的对象，index是索引
                            return row.menu_type;
                        },
                        editor: {
                            type: 'combobox',
                            options: {
                                valueField: 'type_text',
                                textField: 'type_text',
                                method: 'get',
                                url: '?control=menu&action=menuType',
                                required: true
                            }
                        }
                    },
                    {field: 'menu_key', title: 'Menu Key', align: 'center', editor: 'text'},
                    {field: 'menu_url', title: 'Menu URL', align: 'center', editor: 'text'},
                    {field: 'menu_media_id', title: 'Menu Media Id', align: 'center', editor: 'text'},
                    {
                        field: 'menu_pid', title: 'Menu Father', align: 'center',
                        formatter:function(value,row,index) {
                            return row.menu_pid;
                        },
                        editor: {
                            type: 'combobox',
                            options: {
                                valueField: 'menu_text',
                                textField: 'menu_text',
                                method: 'get',
                                url: '?control=menu&action=menuFather',
                                require: true
                            }
                        }
                    },
                    {
                        field: 'menu_status', title: 'Menu Status', align: 'center',
                        editor: {
                            type: 'checkbox',
                            options: {on: 'P', off: ''}
                        }
                    }
                ]
            ],
        });
//        $('#menuStyle').append("<button type='button' class='btn btn-default'>Left</button>");

    });
</script>