<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>编辑轮休最后一天上班</title>
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
        <form class="layui-form" method="post" action='{{route("admin.shift.editpost")}}'>
            <input type="hidden" name="id" value="{{$change_id}}" id="id"/>
            <input type="hidden" id="user_id" value="{{$user_id}}" name="user_id">
            {{ csrf_field() }}
        <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                  <span class="x-red">*</span>用户名字
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" value="{{$user_name}}" lay-verify="nikename" disabled="disabled"
                  autocomplete="off" class="layui-input">
              </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="type" id="type">
                        <option value="">请选择</option>
                        <option value="1" @if($type ==1) selected="selected" @endif>第一天上班</option>
                        <option value="2" @if($type ==2) selected="selected" @endif>第二天上班</option>
                        <option value="3" @if($type ==3) selected="selected" @endif>第三天上班</option>
                        <option value="4" @if($type ==4) selected="selected" @endif>第四天上班</option>
                        <option value="5" @if($type ==5) selected="selected" @endif>第五天上班</option>
                        <option value="6" @if($type ==6) selected="selected" @endif>第一天休息</option>
                        <option value="7" @if($type ==7) selected="selected" @endif>第二天休息</option>
                    </select>
                </div>
            </div>
        </div>
            <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="add" lay-submit="submit" id="edit_check">
                 修改
             </button>
        </div>
      </form>
    </div>
    <script>
        console.log($('#id').val() );
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
          var form = layui.form
          ,layer = layui.layer;
          //自定义验证规则
          form.verify({
            type: function(value){
              if(value == null || value == ''){
                return '请选择类型';
              }
            }
          });


        });
    </script>
  </body>
</html>