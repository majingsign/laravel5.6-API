<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>请假审批</title>
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
            <input type="hidden" name="id" value="{{$user->user_id}}"/>
            {{ csrf_field() }}
        <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                  员工姓名
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" value="{{$user->user_name}}" disabled lay-verify="nikename"
                  autocomplete="off" class="layui-input">
              </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择公司</label>
                <div class="layui-input-inline">
                    <select name="company" id="company" lay-verify="company" lay-filter="company">
                        <option value="">--请选择公司--</option>
                        @if(isset($company) && !empty($company))
                            @foreach($company as $v)
                                <option value="{{$v->id}}" @if($v->id == $user->com_id) selected="selected" @endif>{{$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择部门</label>
                <div class="layui-input-inline">
                    <select name="depart" id="depart" lay-verify="depart">
                        <option value="">--请选择部门--</option>
                            @if(isset($departList) && !empty($departList))
                                <option value="{{$departList->id}}" selected="selected">{{$departList->name}}</option>
                            @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="type">
                        <option value="0">--选择请假类型--</option>
                        @foreach($qingjia as $k => $t)
                            <option value="{{$k}}">{{$t}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>开始时间</label>
                <div class="layui-input-inline">
                    <input type="text" id="starttime" class="layui-input" name="starttime" lay-verify="starttime" autocomplete="off" placeholder="YYYY-MM-dd HH:mm">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>结束时间</label>
                <div class="layui-input-inline">
                    <input type="text" id="endtime" class="layui-input" name="endtime" lay-verify="endtime" autocomplete="off" placeholder="YYYY-MM-dd HH:mm">
                </div>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label"><span class="x-red">*</span>请假事由</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入请假事由" style="width: 300px;" id="desc" name="desc" lay-verify="desc" autocomplete="off" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="add" lay-submit="submit">
                 保存
             </button>
        </div>
      </form>
    </div>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            laydate.render({
                elem: '#starttime',
                type: 'datetime',
                format:'yyyy-MM-dd HH:mm'
            });
            laydate.render({
                elem: '#endtime',
                type: 'datetime',
                format:'yyyy-MM-dd HH:mm'
            });

        });
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
          var form = layui.form
          ,layer = layui.layer;
            form.on('select(company)', function(data){
                com_id = data.value;
                if(com_id == "" || com_id == 0){
                    $("#depart").html("<option value=''>--请选择部门--</option>");
                    form.render('select');
                    layer.msg('请选择公司');
                    return false;
                }
                $.ajax({
                    type: "get",
                    url: "{{route('admin.member.ajaxMemberDepart')}}",
                    dataType: 'json',
                    cache: false,
                    data: {company: com_id},
                    success: function (data) {
                        if (data.code == 200) {
                            var cityhtml = "<option value=''>--请选择部门--</option>";
                            $.each(data.data, function (k, v) {
                                cityhtml += "<option value='" + v.id + "'>" + v.name + "</option>";
                            });
                            $("#depart").html(cityhtml);
                            form.render('select');
                        }
                    }
                });
            });
          //自定义验证规则
          form.verify({
            nikename: function(value){
                if(value == null || value == ''){
                    return '员工昵称必填';
                }
            },
            company: function(value){
                 if(value == null || value == '' || value == 0){
                     return '请选择公司';
                 }
            },
            depart: function(value){
                 if(value == null || value == '' || value == 0){
                     return '请选择部门';
                 }
            },
            type: function(value){
              if(value == null || value == '' || value == 0){
                return '请选择请假类型';
              }
            },
            starttime: function(value){
              if(value == null || value == '' || value == 0){
                return '请选择开始时间';
              }
            },
            endtime: function(value){
              if(value == null || value == '' || value == 0){
                return '请选择结束时间';
              }
            },
            desc: function(value){
              if(value == null || value == '' || value == 0){
                return '请假事由必填';
              }
            }
          });
          //监听提交
          form.on('submit(add)', function(data){
              token   = $("input[name='_token']").val();
              $.ajax({
                  url:"{{route('index.qingjia.QingjiaAdd')}}",
                  type:"post",
                  data:{id:data.field.id,starttime:data.field.starttime,endtime:data.field.endtime,desc:data.field.desc,company:data.field.company,depart:data.field.depart,username:data.field.username,type:data.field.type,_token:token},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          parent.location.href = "{{route('index.qingjia.list')}}";
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
        function pass(id) {
            $.ajax({
                url:"{{route('admin.member.ajaxPass')}}",
                type:"get",
                data:{id:id},
                dataType:"json",
                success:function (data) {
                    console.log(data);
                    if(data.code == 200){
                        alert(data.msg);
                        parent.location.href = "{{route('admin.member.list')}}";
                    }else{
                        alert(data.msg);
                        return false;
                    }
                },
                error:function (err) {
                    console.log(err);
                }
            });
        }
    </script>
  </body>
</html>