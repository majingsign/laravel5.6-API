<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title>请假列表</title>
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
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <xblock>
        <button class="layui-btn" onclick="x_admin_show('提交申请','{{route('index.qingjia.add')}}')"><i class="layui-icon"></i>提交申请</button>
      </xblock>
      <table class="layui-table">
        <thead>
        <tr>
          <th>序号</th>
          <th>员工姓名</th>
          <th>请假类型</th>
          <th>开始时间</th>
          <th>结束时间</th>
          <th>请假时长(小时)</th>
          <th>请假事由</th>
          <th>是否通过</th>
          <th>提审时间</th>
        </thead>
        <tbody>
        @if(!empty($list))
          @foreach($list as $key => $vo)
            <tr>
              <td>{{$key+1}}</td>
              <td>{{$vo->username}}</td>
              <td>
                @switch($vo->type)
                  @case(1)
                  事假 @break
                  @case(2)
                  病假 @break
                  @case(3)
                  婚假 @break
                  @case(4)
                  产假 @break
                  @case(5)
                  丧假 @break
                  @case(6)
                  其他 @break
                  @default
                  无
                @endswitch
              </td>
              <td>
                {{date('Y-m-d H:i',$vo->start_time)}}
              </td>
              <td>
                {{date('Y-m-d H:i',$vo->end_time)}}
              </td>
              <td>{{$vo->long_time / 3600}}</td>
              <td>{{$vo->desc}}</td>
              <td>
                @if(($vo->is_pass == 1))
                  <button class="layui-btn" style="color: #ffffff;">已通过</button>
                @elseif($vo->is_pass == 2)
                  <button class="layui-btn" style="color: red;">未通过</button>
                @else
                  <button class="layui-btn" style="color: #ffffff;">审核中</button>
                @endif
              </td>
              <td>
                {{date('Y-m-d H:i',$vo->create_at)}}
              </td>
            </tr>
          @endforeach()
        @endif
        </tbody>
      </table>
    </div>
  </body>
  <script>
      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              //发异步删除数据
              $.ajax({
                  url:"{{route('admin.admin.del')}}",
                  type:"get",
                  data:{id:id},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          window.location.href = "{{route('admin.admin.list')}}";
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
</html>