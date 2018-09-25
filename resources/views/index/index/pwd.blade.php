<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>修改密码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('admin/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/xadmin.css') }}">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('admin/lib/layui/layui.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('admin/js/xadmin.js') }}"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  
  <body>
    <div class="x-body">
        <form class="layui-form" method="post">
            {{ csrf_field() }}
          <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                    用户名
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" disabled="" value=" {{Session::get('membername')}}" class="layui-input">
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_passs" class="layui-form-label">
                  <span class="x-red">*</span>原密码
              </label>
              <div class="layui-input-inline">
                  <input type="password" id="L_passs" name="pass" required="" lay-verify="required"
                  autocomplete="off" class="layui-input">
              </div>
              <div class="layui-form-mid layui-word-aux">
                  6到16个字符
              </div>
          </div>
            <div class="layui-form-item">
              <label for="L_pass" class="layui-form-label">
                  <span class="x-red">*</span>新密码
              </label>
              <div class="layui-input-inline">
                  <input type="password" id="L_pass" name="newpass" required="" lay-verify="required"
                  autocomplete="off" class="layui-input">
              </div>
              <div class="layui-form-mid layui-word-aux">
                  6到16个字符
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_repass" class="layui-form-label">
                  <span class="x-red">*</span>确认密码
              </label>
              <div class="layui-input-inline">
                  <input type="password" id="L_repass" name="repass" required="" lay-verify="required"
                  autocomplete="off" class="layui-input">
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_repass" class="layui-form-label">
              </label>
              <button  class="layui-btn" id="save" lay-filter="save" lay-submit="submit">
                  确认修改
              </button>
          </div>
      </form>
    </div>
    <script>
        $(function  () {
            $('#save').click(function(){
                pass = $("input[name='pass']").val();
                if(pass == '' || pass == null){
                    layer.alert('原密码不能为空!', {icon: 5});
                    return false;
                }
                newpass = $("input[name='newpass']").val();
                if(newpass == '' || newpass == null){
                    layer.alert('密码不能为空!', {icon: 5});
                    return false;
                }
                if(newpass.length < 6 ){
                    layer.alert('密码范围在6-16个字符之间!', {icon: 5});
                    return false;
                }
                repass  = $("input[name='repass']").val();
                if(repass == '' || repass == null){
                    layer.alert('确认密码不能为空!', {icon: 5});
                    return false;
                }
                if(newpass != repass){
                    layer.alert('2次密码不一致!', {icon: 5});
                    return false;
                }
                token   = $("input[name='_token']").val();
                $.ajax({
                    url:"{{route('index.index.savepwd')}}",
                    type:"post",
                    data:{pass:pass,newpass:newpass,repass:repass,_token:token},
                    dataType:"json",
                    success:function (data) {
                        if(data.code == 200 || data.code == 100){
                            alert(data.msg);
                            parent.location.href = "{{route('index.login.login')}}";
                        }else{
                            alert(data.msg);
                            return false;
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