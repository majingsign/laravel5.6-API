<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title>员工打卡</title>
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
  <br/>
    <div class="x-nav">

    </div>

    <br/>
    <br/>
    <div class="x-body">
      <div class="layui-row" style="text-align: center;">
        <form class="layui-form layui-col-md12 x-so">
          {{ csrf_field() }}
          <input type="text" name="username" id="username" lay-verify="username" placeholder="请输入姓名打卡" style="width: 200px;" autocomplete="off" class="layui-input">
          <button class="layui-btn"  lay-submit="" lay-filter="search"><i class="layui-icon">&#xe664;</i>&nbsp;&nbsp;确认打卡</button>
        </form>
      </div>
      <table class="layui-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>员工姓名</th>
            <th>打卡时间</th>
            </tr>
        </thead>
        <tbody  id="yg">
        @if (isset($recordslist))
          @foreach($recordslist as $k => $v)
            <tr>
              <td>{{$k+1}}</td>
              <td>{{$v->username}}</td>
              <td>{{date('Y-m-d H:i:s',$v->clock_time)}}</td>
            </tr>
          @endforeach
        @endif
        </tbody>
      </table>

    </div>
    <script type="application/javascript">
      $(function () {
          layui.use(['form','layer'], function(){
              $ = layui.jquery;
              var form = layui.form,layer = layui.layer;
              //自定义验证规则
              form.verify({
                  username: function(value){
                      if(value == null || value == ''){
                          return '打卡姓名必填';
                      }
                  }
              });
              //监听提交
              form.on('submit(search)', function(data){
                  username  = $("input[name='username']").val();
                  token     = $("input[name='_token']").val();
                  $.ajax({
                      url:"{{route('index.index.memberLogin')}}",
                      type:"post",
                      data:{username:username,_token:token},
                      dataType:"json",
                      success:function (data) {
                          if(data.code == 200) {
                             alert(data.msg);
                             var yg = '';
                              $.each(data.data,function (i,v) {
                                  yg += '<tr><td>'+i+'</td>';
                                  yg +=  '<td>'+v.username+'</td>';
                                  yg += '<td>'+v.clock_time+'</td></tr>';
                              });
                              $('#yg').html(yg);
                              $("#username").val('');
                              return false;
                          }else{
                              alert(data.msg);
                              setTimeout(function (){window.location.reload();},5000);
                              return false;
                          }
                      },
                  error:function (err) {
                      console.log(err);
                      return false;
                  }
                  });
                  return false;
              });
              return false;
          });
      });
    </script>
  </body>
</html>