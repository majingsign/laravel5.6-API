<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>菜单管理</title>
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
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">首页</a>
        <a href="{{route('admin.menu.list')}}">菜单管理</a>
        <a>
          <cite>菜单列表</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so layui-form-pane">
          {{ csrf_field() }}
          <input class="layui-input" placeholder="菜单名称" autocomplete="off" required="" lay-verify="menu_name" name="menu_name" >

          <div class="layui-input-inline">
            <select name="cateid">
              <option value="0">顶级权限</option>
               @if(isset($lists) && !empty($lists))
                 @foreach($lists as $v)
                   @if($v->pid == 0)
                      <option value="{{$v->id}}">{{$v->name}}</option>
                      @if(isset($v->submenu) && (is_array($v->submenu) || is_object($v->submenu)))
                         @foreach($v->submenu as $vv)
                            @if($v->id == $vv->pid)
                              <option value="{{$vv->id}}">&nbsp;&nbsp;&nbsp;&nbsp;|-- {{$vv->name}}</option>
                            @endif
                         @endforeach
                     @endif
                   @endif
                 @endforeach
                @endif
            </select>
          </div>
          <input class="layui-input" placeholder="控制器名" name="contro_name" autocomplete="off" required="" lay-verify="contro_name">
          <input class="layui-input" placeholder="方法名称" name="action_name" autocomplete="off" lay-verify="action_name">
          <button class="layui-btn"  lay-submit="submit" lay-filter="sreach"><i class="layui-icon"></i>增加</button>
        </form>
      </div>
      <table class="layui-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>权限规则</th>
            <th>权限名称</th>
            <th>所属分类</th>
            <th>操作</th>
        </thead>
        <tbody>
        @foreach($list as $k => $v)
          <tr>
            <td>{{$k+1}}</td>
            <td>{{$v->mm}}/{{$v->mc}}/{{$v->ma}}</td>
            <td>{{$v->name}}</td>
            <td>@if($v->pid == 0) 顶级分类 @else 二级分类 @endif </td>
            <td class="td-manage">
              <a title="编辑"  onclick="x_admin_show('编辑','{{route('admin.menu.edit',['menuid'=>$v->id])}}',500,500)" href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
              <a title="删除" onclick="member_del(this,{{$v->id}})" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="page">{{$list->links()}}</div>
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
            form.on('submit(sreach)', function(data){
                //发异步，把数据提交给php
                token   = $("input[name='_token']").val();
                $.ajax({
                    url:"{{route('admin.menu.addMenu')}}",
                    type:"post",
                    data:{menu_name:data.field.menu_name,cateid:data.field.cateid,contro_name:data.field.contro_name,action_name:data.field.action_name,_token:token},
                    dataType:"json",
                    success:function (data) {
                        if(data.code == 200){
                            alert(data.msg);
                            window.location.href = "{{route('admin.menu.list')}}";
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

      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              $.ajax({
                  url:"{{route('admin.menu.del')}}",
                  type:"get",
                  data:{id:id},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          window.location.href = "{{route('admin.menu.list')}}";
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
      }
    </script>
  </body>

</html>