<div class="easyui-panel" title="Login to weChat" style="width:100%;height:100%;padding:30px 70px 20px 70px;text-align:center">
    <h2>WeChat Background</h2>
    <form id="login" method="post">
        <div style="margin-bottom:10px">
            <input id="userName" class="easyui-textbox" style="width:50%;height:40px;padding:12px" data-options="prompt:'Username',iconCls:'icon-man',iconWidth:38">
        </div>
        <div style="margin-bottom:20px">
            <input id="password" class="easyui-textbox" type="password" style="width:50%;height:40px;padding:12px" data-options="prompt:'Password',iconCls:'icon-lock',iconWidth:38">
        </div>
        <div style="margin-bottom:20px">
            <input type="checkbox" checked="checked" id="remember">
            <span>Remember me</span>
        </div>
    </form>
    <div>
        <a href="javascript:void(0)" onclick="submitForm()" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" style="padding:5px 0px;width:50%">
            <span style="font-size:14px;">Login</span>
        </a>
    </div>
</div>
<script>
    function submitForm()
    {
        var remember=0;
        $('#login').submit(
            function(e){
                e.preventDefault();
            }
        );
        if($('#remember').prop('checked'))
        {
            remember=1;
        }
        $.post(
            "",              //这里写URL
            {
                "username":$('#userName').val(),
                "password":$('#password').val(),
                "remember":remember
            },
            function (result)
            {
                if(result==1)
                {
                    window.location.href="";        //重载页面
                }
                else
                {
                    $.messager.alert('weChat Login','userName or Password Error!','error');
                }
            }
        )
    }
    $(document).keydown(function(e){
        if(e.keyCode=="13"){
            submitForm();
        }
    });
</script>