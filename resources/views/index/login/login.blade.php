<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>员工登录-忆享科技打卡系统</title>
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
    <script type="text/javascript" src="{{ asset('admin/js/qrcode.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/jquery.min.js') }}"></script>

</head>
<body class="login-bg">
    <div class="login layui-anim layui-anim-up">
        <div class="message" style="background-color:#b7f0ff;color: #000000">员工扫码登录</div>
        <div id="darkbannerwrap"></div>
        <form method="post" class="layui-form">
            {{ csrf_field() }}
            <div id="Ec" style="margin-left: 50px;"></div>
            <div id="opendid" style="margin-left: 50px;"></div>
        </form>
    </div>
</body>
<script language="javascript">
    dwidth  = 200;
    dheight = 200;
    var qrcode = new QRCode('Ec');
    qrcode.makeCode("{{$url}}getwxid1.php?check={{$check}}");
    var check="{{$check}}";
    var T=0;
    function Preload() {
        self.location.reload();
    }
    function checkqcode() {
        var str="";
        $.ajax({
            url:"{{route('index.index.checkWxPost',['check'=>$check])}}",
            type:"get",
            dataType:"text",
            timeout:5000,
            error:function(){
                $("#opendid").html("服务器连接超时，请 <span class=FcblueB onclick='Preload()'><span class=cup>刷新</span></span> 重试");
            },
            success:function(data){
                T++;
                var Rd=$.trim(data);
                var json = (new Function("return " + Rd))();
                if(json["status"]==400){
                    if(T>=100){
                        $("#opendid").html("验证等待超时，请 <span class=FcblueB onclick='Preload()'><span class=cup>刷新</span></span> 重试");
                    }else{
                        $("#opendid").html(T);
                        setTimeout("checkqcode()",1000);
                    }
                }else{
                    var data = json["data"];
                    data=(new Function("return " + data))();
                    token     = $("input[name='_token']").val();
                    //判断登陆
                    $.ajax({
                        url:"{{route('index.index.weixinLogin')}}",
                        type:"post",
                        data:{openid:data["openid"],_token:token},
                        dataType:"json",
                        success:function (data) {
                            if(data.code == 200) {
                                alert(data.msg);
                                window.location.href = "{{route('index.index.index')}}";
                                return false;
                            }else{
                                alert(data.msg);
                                setTimeout(function (){window.location.reload();},2000);
                                return false;
                            }
                        },
                        error:function (err) {
                            console.log(err);
                            return false;
                        }
                    });
                }
            }
        });
    }
    setTimeout(checkqcode,3000);
</script>
</html>