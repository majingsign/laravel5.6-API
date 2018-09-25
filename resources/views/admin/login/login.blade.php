<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>后台登录-忆享科技排班系统</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('admin/css/font.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/css/xadmin.css') }}">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ asset('admin/lib/layui/layui.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('admin/js/xadmin.js') }}"></script>

</head>
<body class="login-bg">
    <div class="login layui-anim layui-anim-up">
        <div class="message">忆享科技排班系统-管理员登录</div>
        <div id="darkbannerwrap"></div>
        <form method="post" class="layui-form" action="{{route('admin.loginAction')}}" >
            {{ csrf_field() }}
            <input name="username" autocomplete="off" placeholder="用户名" lay-verify="required" type="text" class="layui-input" >
            <hr class="hr15">
            <input name="password" lay-verify="required" autocomplete="off" placeholder="密码"  type="password" class="layui-input">
            <hr class="hr15">
            <input value="登录" lay-submit lay-filter="login" id="login" style="width:100%;" type="button">
            <hr class="hr20" >
            <span style="float: right;font-family: 微软雅黑;color: #000000"><i class="layui-icon">&#xe623;&#xe623;&#xe623;</i><a href="{{route('index.index.index')}}">去打卡</a></span>
        </form>
    </div>
    <script>
        $(function  () {
            $("input[name='password']").keydown(function(e){
                if(e.keyCode == 13){
                    $("#login").click();
                }
            });
            $('#login').click(function(){
                username = $("input[name='username']").val();
                userpwd  = $("input[name='password']").val();
                token    = $("input[name='_token']").val();
                $.ajax({
                    url:"{{route('admin.loginAction')}}",
                    type:"post",
                    data:{username:username,password:userpwd,_token:token},
                    dataType:"json",
                    success:function (data) {
                        if(data.code == 200) {
                            $("#login").val(data.msg);
                            setTimeout(function () {
                                window.location.href = "{{route('admin.index')}}";
                            }, 2000);
                        }else if(data.code == 100){
                            $("#login").val(data.msg);
                            setTimeout(function () {
                                window.location.href = "{{route('index.index.index')}}";
                            }, 2000);
                        }else{
                            $("#login").val(data.msg);
                            setTimeout(function (){$("#login").val('登陆');},2000);
                            // setTimeout(function (){window.location.reload();},2000);
                        }
                    },
                    error:function (err) {
                        console.log(err);
                    }
                });
            });
        });
    </script>
</body>
</html>