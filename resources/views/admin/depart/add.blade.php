<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>新增部门</title>
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
            {{ csrf_field() }}
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择公司</label>
                <div class="layui-input-inline">
                    <select name="company" lay-verify="company">
                        <option value="">--请选择公司--</option>
                        @foreach($company as $k => $v)
                            <option value="{{$v->id}}">{{$v->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
              <label for="departname" class="layui-form-label">
                  <span class="x-red">*</span>部门名称
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="departname" name="departname" required="" lay-verify="departname"
                  autocomplete="off" class="layui-input">
              </div>
        </div>
        {{--<div class="layui-form-item">--}}
            {{--<div class="layui-inline">--}}
                {{--<label class="layui-form-label"><span class="x-red">*</span>部门负责人</label>--}}
                {{--<div class="layui-input-inline">--}}
                    {{--<select name="type" lay-verify="type">--}}
                        {{--<option value="">--请选择部门负责人--</option>--}}
                        {{--@foreach($list as $k => $v)--}}
                            {{--<option value="{{$v->id}}">{{$v->admin_name}}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="layui-form-item">
            <label for="desc" class="layui-form-label">
                描述
            </label>
            <div class="layui-input-inline">
                <input type="text" id="desc" name="desc" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">分配权限</label>
            <table  class="layui-table layui-input-block">
                <tbody>
                @if(isset($menulist) && !empty($menulist))
                    @foreach($menulist as $val)
                        <tr>
                            <td>
                                <input type="checkbox" name="menus[]" lay-skin="primary" class="menus" required="" title="{{$val->name}}" value="{{$val->id}}">
                            </td>
                            <td>
                                <div class="layui-input-block">
                                    @if(isset($val->submenu) && !empty($val->submenu))
                                        @foreach($val->submenu as $v)
                                            <input name="menus[]" lay-skin="primary" class="menus" type="checkbox" title="{{$v->name}}" value="{{$v->id}}">
                                            @if(isset($v->submenu) && !empty($v->submenu))
                                                @foreach($v->submenu as $vv)
                                                    <input name="menus[]" lay-skin="primary" class="menus" type="checkbox" title="{{$vv->name}}" value="{{$vv->id}}">
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="add" lay-submit="submit">
                 增加
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
            departname: function(value){
                if(value == null || value == ''){
                    return '部门必填';
                }
            },
             company: function(value){
              if(value == null || value == '' || value == 0){
                return '公司必选';
              }
            }
          });
            //监听提交
          form.on('submit(add)', function(data){
             checkID = [];//定义一个空数组
              if($(".menus:checked").length == 0){
                  alert('未分配权限');
                  return false;
              }
              $(".menus:checked").each(function(i,v){//把所有被选中的复选框的值存入数组
                  checkID[i] =$(this).val();
              });

              token   = $("input[name='_token']").val();
              $.ajax({
                  url:"{{route('admin.depart.addDepart')}}",
                  type:"post",
                  data:{departname:data.field.departname,company:data.field.company,menus:checkID,desc:data.field.desc,_token:token},
                  dataType:"json",
                  success:function (data) {
                      if(data.code == 200){
                          alert(data.msg);
                          parent.location.href = "{{route('admin.depart.list')}}";
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