<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>新增管理员</title>
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
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form" method="post">
            {{ csrf_field() }}
          <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                  <span class="x-red">*</span>管理员昵称
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" lay-verify="nikename"
                  autocomplete="off" class="layui-input">
              </div>
          </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>请选择公司</label>
                <div class="layui-input-inline">
                    <select name="company" id="company" lay-verify="company" lay-filter="company">
                        <option value="">--请选择公司--</option>
                        @if(isset($company) && !empty($company))
                            @foreach($company as $v)
                                <option value="{{$v->id}}">{{$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择部门</label>
                <div class="layui-input-inline">
                    <select name="depart" id="depart" lay-verify="depart">
                        <option value="">--请选择部门--</option>
                    </select>
                </div>
            </div>
        </div>
          <div class="layui-form-item">
              <label for="L_pass" class="layui-form-label">
                  <span class="x-red">*</span>登陆密码
              </label>
              <div class="layui-input-inline">
                  <input type="password" id="L_pass" name="pass" required="" lay-verify="pass"
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
                  <input type="password" id="L_repass" name="repass" required="" lay-verify="repass"
                  autocomplete="off" class="layui-input">
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_repass" class="layui-form-label">
              </label>
              <button  class="layui-btn" lay-filter="add" lay-submit="">
                  增加
              </button>
          </div>
      </form>
    </div>
    <script type="application/javascript">
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
          var form = layui.form
          ,layer = layui.layer;
            form.on('select(company)', function(data){
                com_id = data.value;
                if(com_id == "" || com_id == 0){
                    $("#depart").html("<option value=''>--请选择部门--</option>");
                    form.render('select');
                    layer.msg('请选择公司');
                    return false;
                }
                $.ajax({
                    type: "get",
                    url: "{{route('admin.admin.ajaxDepart')}}",
                    dataType: 'json',
                    cache: false,
                    data: {company: com_id},
                    success: function (data) {
                        if (data.code == 200) {
                            var cityhtml = "<option value=''>--请选择部门--</option>";
                            $.each(data.data, function (k, v) {
                                cityhtml += "<option value='" + v.id + "'>" + v.name + "</option>";
                            });
                            $("#depart").html(cityhtml);
                            form.render('select');
                        }
                    }
                });
            });
            //自定义验证规则
          form.verify({
            nikename: function(value){
              if(value.length < 2){
                return '姓名至少得2个字符';
              }
            },
            company: function(value){
             if(value == null || value == ''){
                 return '选择公司';
             }
            },
             depart: function(value){
             if(value == null || value == ''){
                 return '选择部门';
             }
            }
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
          });

          //监听提交
          form.on('submit(add)', function(data){
            //发异步，把数据提交给php
              token   = $("input[name='_token']").val();
              $.ajax({
                  url:"{{route('admin.admin.addAdmin')}}",
                  type:"post",
                  data:{username:data.field.username,company:data.field.company,depart:data.field.depart,pass:data.field.pass,_token:token},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          parent.location.href = "{{route('admin.admin.list')}}";
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