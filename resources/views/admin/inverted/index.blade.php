<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>倒班最后一天上班情况</title>
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
        <a href="#">倒班管理</a>
        <a>
          <cite>倒班最后一天上班情况</cite></a>
      </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div class="x-body">
    <div class="layui-row">
       <button class="save layui-btn layui-btn-radius">更新本月最后一天上班内容</button>
    </div>
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
                        C1第一天
                    @elseif($user->last_month_dat_type == 2)
                        C1第二天
                    @elseif($user->last_month_dat_type == 3)
                        C1第三天
                    @elseif($user->last_month_dat_type == 4)
                        C1第四天
                    @elseif($user->last_month_dat_type == 5)
                        C1后的E班
                    @elseif($user->last_month_dat_type == 6)
                        C1后的休息第一天
                    @elseif($user->last_month_dat_type == 7)
                        C1后的休息第二天


                    @elseif($user->last_month_dat_type == 8)
                        C2第一天
                    @elseif($user->last_month_dat_type == 9)
                        C2第二天
                    @elseif($user->last_month_dat_type == 10)
                        C2第三天
                    @elseif($user->last_month_dat_type == 11)
                        C2第四天
                    @elseif($user->last_month_dat_type == 12)
                        C2后的E班
                    @elseif($user->last_month_dat_type == 13)
                        C2后休息第一天
                    @elseif($user->last_month_dat_type == 14)
                        C2后的休息第二天


                    @elseif($user->last_month_dat_type == 15)
                        C4第一天
                    @elseif($user->last_month_dat_type == 16)
                        C4第二天
                    @elseif($user->last_month_dat_type == 17)
                        C4第三天
                    @elseif($user->last_month_dat_type == 18)
                        C4第四天
                    @elseif($user->last_month_dat_type == 19)
                        C4后的E班
                    @elseif($user->last_month_dat_type == 20)
                        C4休息第一天
                    @elseif($user->last_month_dat_type == 21)
                        C4休息第二天



                    @elseif($user->last_month_dat_type == 22)
                        D1第一天
                    @elseif($user->last_month_dat_type == 23)
                        D1第二天
                    @elseif($user->last_month_dat_type == 24)
                        D1第三天
                    @elseif($user->last_month_dat_type == 25)
                        D1第四天
                    @elseif($user->last_month_dat_type == 26)
                        D1后的E班
                    @elseif($user->last_month_dat_type == 27)
                        D1后的休息第一天
                    @elseif($user->last_month_dat_type == 28)
                        D1后的休息第二天
                        @else
                        暂未设置或最后一天已离职
                  @endif
                </td>

                <td class="td-manage">
                    <a title="编辑"  onclick="x_admin_show('编辑','{{route('admin.inverted.edit',['user_name' => $user ->user_name,'user_id' => $user -> user_id,'id' => $user->id,'type' => $user -> last_month_dat_type])}}',600,400)" href="javascript:;">
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


    $(function () {
        //当点击更新数据的时候
        $('.save').click(function(){
           $.ajax({
               url : '{{route("admin.inverted.syssetting")}}',
               type : 'get',
               dataType : 'json',
               success: function (data) {
                   if(data.code == 1){
                       layer.alert(data.message);
                       location.reload();
                   } else {
                       layer.alert(data.message);
                     return false;
                   }
               }
           })
        })

    })
</script>
</body>
</html>