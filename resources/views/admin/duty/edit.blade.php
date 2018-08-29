<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>编辑排班类型</title>
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
            <input type="hidden" id="L_id" name="dutyid" value="{{$list->id}}" required="" autocomplete="off" class="layui-input">
            {{ csrf_field() }}
        <div class="layui-form-item">
              <label for="L_name" class="layui-form-label">
                  <span class="x-red">*</span>类型名称
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_name" name="name" value="{{$list->name}}" required="" lay-verify="name" autocomplete="off" class="layui-input">
              </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="type">
                        @foreach($type as $k => $t)
                            <option value="{{$k}}" @if($k == $list->duty_type) selected="selected" @endif>{{$t}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_time" class="layui-form-label">
                <span class="x-red">*</span>上班时间段
            </label>
            <div class="layui-input-inline">
                <input type="text" id="L_time" name="ontime" required="" lay-verify="ontime" value="{{$list->on_time}}"
                       autocomplete="off" placeholder="如：9:00 - 18:00" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="edit" lay-submit="submit">
                 提交
             </button>
        </div>
      </form>
    </div>
    <script>
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
          var form = layui.form
          ,layer = layui.layer;
          //自定义验证规则
          form.verify({
            name: function(value){
                if(value == null || value == ''){
                    return '名称必填';
                }
            },
            type: function(value){
              if(value == null || value == ''){
                return '请选择类型';
              }
            },
            ontime: function(value){
              if(value == null || value == ''){
                return '时间段必填';
              }
            }
          });
          //监听提交
          form.on('submit(edit)', function(data){
              token   = $("input[name='_token']").val();
              $.ajax({
                  url:"{{route('admin.duty.saveDuty')}}",
                  type:"post",
                  data:{dutyid:data.field.dutyid,name:data.field.name,type:data.field.type,ontime:data.field.ontime,_token:token},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          parent.location.href = "{{route('admin.duty.list')}}";
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