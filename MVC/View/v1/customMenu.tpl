<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="MVC/View/v1/css/bootstrap.css">

<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="MVC/View/v1/css/bootstrap-theme.css">

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<!--<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>-->

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="MVC/View/v1/js/bootstrap.min.js"></script>



<div style="padding:0 10px;">
    <div class="row clearfix">
        <div class="col-md-8 column">
            <h2>Row Editing in DataGrid</h2>
            <p>Click the row to start editing.</p>
            <div style="margin:20px 0;"></div>
            <table id="dg" class="easyui-datagrid" title="Customize WeChat Menu" style="width:100%;height:auto"></table>
            <!--<table id="dg" class="easyui-datagrid" title="Customize WeChat Menu" style="width:750px;height:auto"
                   data-options="
				iconCls: 'icon-edit',
				singleSelect: true,
				toolbar: '#tb',
				url: 'datagrid_data1.json',
				method: 'get',
				onClickRow: onClickRow
			">
                <thead>
                <tr>
                    <th data-options="field:'itemid',width:80">Menu ID</th>
                    <th data-options="field:'productid',width:100,
						formatter:function(value,row){
							return row.productname;
						},
						editor:{
							type:'combobox',
							options:{
								valueField:'productid',
								textField:'productname',
								method:'get',
								url:'products.json',
								required:true
							}
						}">Menu Type</th>
                    <th data-options="field:'menuKey',width:80,align:'right',editor:'numberbox'">Menu Key</th>
                    <th data-options="field:'menuMediaId',width:90,align:'right',editor:'numberbox'">Menu Media Id</th>
                    <th data-options="field:'menuFather',width:80,align:'right',editor:'numberbox'">Menu Father</th>
                    <th data-options="field:'attr1',width:170,editor:'textbox'">Attribute</th>
                    <th data-options="field:'status',width:60,align:'center',editor:{type:'checkbox',options:{on:'P',off:''}}">Status</th>
                </tr>
                </thead>
            </table>-->

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
                <div class="btn-group">
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
            var ed = $('#dg').datagrid('getEditor', {index:editIndex,field:'productid'});
            var productname = $(ed.target).combobox('getText');
            $('#dg').datagrid('getRows')[editIndex]['productname'] = productname;
            $('#dg').datagrid('endEdit', editIndex);
            editIndex = undefined;
            return true;
        } else {
            return false;
        }
    }
    function onClickRow(index){
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
    function getChanges(){
        var rows = $('#dg').datagrid('getChanges');
        alert(rows.length+' rows are changed!');
    }
    $(document).ready(function(){
        $('#dg').datagrid({
            iconCls: 'icon-edit',
            striped: true, //行背景交换
            idField: 'id', //主键
            singleSelect: true,
            toolbar: '#tb',
            url: '?control=menu&action=menuDetail',
            method: 'get',
            onClickRow: onClickRow,
            columns:[
                [
                    {field:'id',title:'Menu Id',width:'10%',align:'center'},
                    {field:'menu_type',title:'Menu Type',width:'10%',align:'center',
                        formatter:function(value,row){
                            return row.productname;
                        },
                        editor: {
                            type: 'combobox',
                            options: {
                                valueField: 'typeId',
                                textField: 'typeName',
                                method: 'get',
                                url: 'products.json',
                                required: true
                            }
                        }
                    },
                    {field:'menu_key',title:'Menu Key',width:'15%',align:'center',editor:'text'},
                    {field:'menu_url',title:'Menu URL',width:'15%',align:'center',editor:'text'},
                    {field:'menu_media_id',title:'Menu Media Id', width:'20%', align:'center',editor:'text'},
                    {field:'menuFather',title:'Menu Father', width:'20%',align:'center',
                        editor:{
                            type:'combobox',
                            options:{
                                valueField:'menuPid',
                                textField:'menuText',
                                method:'get',
                                url:'',
                                require:true
                            }
                        }
                    },
                    {field:'menu_status',title:'Menu Status',width:'10%',align:'center',
                        editor:{
                            type:'checkbox',
                            options:{on:'P',off:''}
                        }
                    },
                    {field:'action',title:'Action',width:70,align:'center',
                        formatter:function(value,row,index){
                            if (row.editing){
                                var s = '<a href="#" onclick="saverow(this)">Save</a> ';
                                var c = '<a href="#" onclick="cancelrow(this)">Cancel</a>';
                                return s+c;
                            } else {
                                var e = '<a href="#" onclick="editrow(this)">Edit</a> ';
                                var d = '<a href="#" onclick="deleterow(this)">Delete</a>';
                                return e+d;
                            }
                        }
                    }

                ]
            ],
            onBeforeEdit:function(index,row){
                row.editing = true;
                updateActions(index);
            },
            onAfterEdit:function(index,row){
                row.editing = false;
                updateActions(index);
            },
            onCancelEdit:function(index,row){
                row.editing = false;
                updateActions(index);
            }
        });
        function updateActions(index){
            $('#dg').datagrid('updateRow',{
                index: index,
                row:{}
            });
        }
        function getRowIndex(target){
            var tr = $(target).closest('tr.datagrid-row');
            return parseInt(tr.attr('datagrid-row-index'));
        }
        function editrow(target){
            $('#tt').datagrid('beginEdit', getRowIndex(target));
        }
        function deleterow(target){
            $.messager.confirm('Confirm','Are you sure?',function(r){
                if (r){
                    $('#dg').datagrid('deleteRow', getRowIndex(target));
                }
            });
        }
        function saverow(target){
            $('#dg').datagrid('endEdit', getRowIndex(target));
        }
        function cancelrow(target){
            $('#dg').datagrid('cancelEdit', getRowIndex(target));
        }
    });
</script>