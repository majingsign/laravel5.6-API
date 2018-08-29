<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>轮班最后一天上班情况</title>
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
        <a href="#">轮班管理</a>
        <a>
          <cite>轮班最后一天上班情况</cite></a>
      </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="username"  placeholder="请输入用户名" autocomplete="off" class="layui-input">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
        </form>
    </div>
    {{--<xblock>--}}
        {{--<button class="layui-btn" onclick="x_admin_show('添加员工','{{route('admin.member.add')}}',600,400)"><i class="layui-icon"></i>添加</button>--}}
    {{--</xblock>--}}
    <table class="layui-table">
        <thead>
        <tr>
            <th>用户名</th>
            <th>最后一天上班类型</th>
            <th>操作</th></tr>
        </thead>
        <tbody>
        @foreach($list as $user)
            <tr>
                <td>{{$user->user_name}}</td>
                <td>
                    @if($user->last_month_dat_type == 1)
                        第一天上班
                    @elseif($user->last_month_dat_type == 2)
                        第二天上班
                    @elseif($user->last_month_dat_type == 3)
                        第三天上班
                    @elseif($user->last_month_dat_type == 4)
                        第四天上班
                    @elseif($user->last_month_dat_type == 5)
                        第五天上班
                    @elseif($user->last_month_dat_type == 6)
                        第一天休息
                    @elseif($user->last_month_dat_type == 7)
                        第二天休息
                        @else
                        暂未设置
                  @endif
                </td>
                <td class="td-manage">
                    <a title="编辑"  onclick="x_admin_show('编辑','{{route('admin.shift.edit',['user_name' => $user ->user_name,'user_id' => $user -> user_id,'id' => $user->id])}}',600,400)" href="javascript:;">
                        <i class="layui-icon">&#xe642;</i>
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
                url:"{{route('admin.member.del')}}",
                type:"get",
                data:{id:id},
                dataType:"json",
                success:function (data) {
                    if(data.code == 200){
                        alert(data.msg);
                        window.location.href = "{{route('admin.member.list')}}";
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