<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>员工考勤列表</title>
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
  <br/>

    <div class="x-body">
      <div class="layui-row">
        <div>
          <a href="{{route('admin.records.getlastmonthlsit')}}">
            <button class="import layui-btn layui-btn-radius" href="javascript:;">获取上个月打卡信息</button>
          </a>

        </div>
        <form class="layui-form layui-col-md12 x-so" action="{{route('admin.member.recordsList')}}" method="get" style="margin-top: 20px;">
          <input class="layui-input" placeholder="开始日" value="{{$start}}" autocomplete="off" name="start" id="start">
          <input class="layui-input" placeholder="截止日" value="{{$end}}" autocomplete="off" name="end" id="end">
          {{--<input type="text" name="username"  placeholder="请输入用户名" autocomplete="off" class="layui-input">--}}
          <button class="layui-btn"  lay-submit="submit" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
        </form>
      </div>
      <table class="layui-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>所属公司</th>
            <th>员工姓名</th>
            <th>打卡时间</th>
            </tr>
        </thead>
        <tbody  id="yg">
        @if (isset($list))
          @foreach($list as $k => $v)
            <tr>
              <td>{{$k+1}}</td>
              <td>@if(!empty($v->comname)){{$v->comname}}@else未设置@endif</td>
              <td>{{$v->username}}</td>
              <td>{{date('Y-m-d H:i:s',$v->clock_time)}}</td>
            </tr>
          @endforeach
        @endif
        </tbody>
      </table>
    </div>
  </body>
<script type="application/javascript">
    layui.use('laydate', function(){
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });
    });
</script>
</html>