<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>部门列表</title>
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
  <body class="layui-anim layui-anim-up">
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="/">首页</a>
        <a href="{{route('admin.depart.list')}}">部门管理</a>
        <a>
          <cite>部门列表</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" method="get" action="{{route('admin.depart.list')}}">
          <input type="text" name="username"  value="{{$name}}" placeholder="请输入公司/部门查询" autocomplete="off" class="layui-input">
          <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
        </form>
      </div>
      <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加部门','{{route('admin.depart.add')}}')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:40px">共有数据：{{$list->total()}} 条</span>
      </xblock>
      <table class="layui-table">
        <thead>
          <tr>
            <th>序号</th>
            <th>公司名称</th>
            <th>部门名称</th>
            <th>部门负责人</th>
            <th>描述</th>
            <th>操作</th></tr>
        </thead>
        <tbody>
          @foreach($list as $key => $depart)
          <tr>
            <td>{{$key+1}}</td>
            <td>@if(!empty($depart->com_name)){{$depart->com_name}}@else<span style="color: red;">未设置</span>@endif</td>
            <td>{{$depart->name}}</td>
            <td>{{$depart->admin_name}}</td>
            <td>{{$depart->desc}}</td>
            <td class="td-manage">
                <a title="编辑"  onclick="x_admin_show('编辑','{{route('admin.depart.edit',['departid'=>$depart->id])}}')" href="javascript:;">
                  <i class="layui-icon">&#xe642;</i>
                </a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a title="查看权限"  onclick="x_admin_show('查看权限','{{route('admin.depart.show',['departid'=>$depart->id])}}')" href="javascript:;">
                  <i class="layui-icon">&#xe663;</i>
                </a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a title="删除" onclick="member_del(this,{{$depart->id}})" href="javascript:;">
                  <i class="layui-icon">&#xe640;</i>
                </a>
            </td>
          </tr>
          @endforeach()
        </tbody>
      </table>
      <div class="page">
          {{$list->links()}}
      </div>
    </div>
    <script>
      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              //发异步删除数据
              $.ajax({
                  url:"{{route('admin.depart.del')}}",
                  type:"get",
                  data:{id:id},
                  dataType:"json",
                  success:function (data) {
                    if(data.code == 200){
                            alert(data.msg);
                            window.location.href = "{{route('admin.depart.list')}}";
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