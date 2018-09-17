<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>编辑员工</title>
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
                  <span class="x-red">*</span>员工姓名
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" value="{{$user->user_name}}" lay-verify="nikename"
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
                        @foreach($type as $k => $t)
                            <option value="{{$k}}" @if($k == $user->duty_type) selected="selected" @endif>{{$t}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择省份</label>
            <div class="layui-input-block">
                @foreach($list as $k => $v)
                      <input type="checkbox" name="city[]" @if(in_array($v->id,$usercy))checked  @endif class="city"  lay-verify="city" value="{{$v->id}}" title="{{$v->name}}">
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label for="levetime" class="layui-form-label">
                离职时间
            </label>
            <div class="layui-input-inline">
                <input type="text" id="levetime" name="leve_time" value="@if($user->leve_time){{date('Y-m-d',$user->leve_time)}} @endif" autocomplete="off" class="layui-input">
            </div>
            <br/>
            <span style="font-size: 12px;color: red;">
                离职当天有上班，第二天不上班。如将在本月离职,请在排班中设置为空,如延长了离职时间,请将对应的上班内容补充完整
            </span>
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
                elem: '#levetime'
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
                return '请选择类型';
              }
            }
          });
          //监听提交
          form.on('submit(add)', function(data){
              checkID = [];//定义一个空数组
              if($(".city:checked").length == 0){
                    // alert('未选择省份');
                    // return false;
              }
              $(".city:checked").each(function(i,v){//把所有被选中的复选框的值存入数组
                    checkID[i] =$(this).val();
              });
              token   = $("input[name='_token']").val();
              $.ajax({
                  url:"{{route('admin.member.editMember')}}",
                  type:"post",
                  data:{id:data.field.id,leve_time:data.field.leve_time,company:data.field.company,depart:data.field.depart,username:data.field.username,type:data.field.type,city:checkID,_token:token},
                  dataType:"json",
                  success:function (data) {
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
          });
        });
    </script>
  </body>
</html>