<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>导入倒班排班</title>
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
        <form class="layui-form" method="post" action='{{route("admin.changeinverted.importlistpost")}}' enctype="multipart/form-data">
            {{ csrf_field() }}
        <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                  <span class="x-red">*</span>导入文件
              </label>
              <div class="layui-input-inline">
                  <input type="file" name="file" value="">
              </div>
            <span style="color: red;font-weight: bold;">
                请将导入文件,修改为CSV格式,为避免数据错误,上传的表格内容字段,不能为空
            </span>
        </div>
            <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="add" lay-submit="submit" id="edit_check">
                 导入
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