<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title>新增菜单</title>
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
            <input type="hidden" name="id" value="{{$list->id}}"/>
            {{ csrf_field() }}
          <div class="layui-form-item">
              <label for="menu_name" class="layui-form-label">
                  <span class="x-red">*</span>菜单名称
              </label>
              <div class="layui-input-inline">
                  <input class="layui-input" id="menu_name" placeholder="菜单名称" value="{{$list->name}}" autocomplete="off" required="" lay-verify="menu_name" name="menu_name" >
              </div>
          </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择权限</label>
                <div class="layui-input-inline">
                    <select name="cateid" lay-verify="cateid">
                        <option value="0">顶级权限</option>
                        @if(isset($lists) && !empty($lists))
                            @foreach($lists as $v)
                                @if($v->pid == 0)
                                    <option value="{{$v->id}}" @if($list->id == $v->id) selected="selected" @endif>{{$v->name}}</option>
                                    @if(isset($v->submenu) && (is_array($v->submenu) || is_object($v->submenu)))
                                        @foreach($v->submenu as $vv)
                                            @if($v->id == $vv->pid)
                                                <option value="{{$vv->id}}" @if($list->id == $vv->id) selected="selected" @endif>&nbsp;&nbsp;&nbsp;&nbsp;|-- {{$vv->name}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
            <div class="layui-form-item">
                <label for="contro_name" class="layui-form-label">
                    <span class="x-red">*</span>控制器名称
                </label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="contro_name" value="{{$list->mc}}" placeholder="控制器名" name="contro_name" autocomplete="off" required="" lay-verify="contro_name">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="action_name" class="layui-form-label">
                    方法名称
                </label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="action_name" value="{{$list->ma}}" placeholder="方法名称" name="action_name" autocomplete="off" lay-verify="action_name">
                </div>
            </div>
          <div class="layui-form-item">
              <label for="L_repass" class="layui-form-label">
              </label>
              <button  class="layui-btn" lay-filter="edit" lay-submit="">
                  保存
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
                menu_name: function(value){
                    if(value == null || value == ''){
                        return '菜单名称不能为空';
                    }
                },
                contro_name: function(value){
                    if(value == null || value == ''){
                        return '控制器不能为空';
                    }
                }
            });
            //监听提交
            form.on('submit(edit)', function(data){
                //发异步，把数据提交给php
                token   = $("input[name='_token']").val();
                $.ajax({
                    url:"{{route('admin.menu.saveMenu')}}",
                    type:"post",
                    data:{id:data.field.id,menu_name:data.field.menu_name,cateid:data.field.cateid,contro_name:data.field.contro_name,action_name:data.field.action_name,_token:token},
                    dataType:"json",
                    success:function (data) {
                        if(data.code == 200){
                            alert(data.msg);
                            parent.location.href = "{{route('admin.menu.list')}}";
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