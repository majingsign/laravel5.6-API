<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>编辑倒班最后一天上班</title>
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
        <form class="layui-form" method="post" action='{{route("admin.inverted.editpost")}}'>
            <input type="hidden" name="id" value="{{$change_id}}" id="id"/>
            <input type="hidden" id="user_id" value="{{$user_id}}" name="user_id">
            {{ csrf_field() }}
        <div class="layui-form-item">
              <label for="L_username" class="layui-form-label">
                  <span class="x-red">*</span>用户名字
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" value="{{$user_name}}" lay-verify="nikename" disabled="disabled"
                  autocomplete="off" class="layui-input">
              </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="type" id="type">
                        <option value="">请选择</option>
                        <option value="1" @if($type ==1) selected="selected" @endif>C1第一天</option>
                        <option value="2" @if($type ==2) selected="selected" @endif>C1第二天</option>
                        <option value="3" @if($type ==3) selected="selected" @endif>C1第三天</option>
                        <option value="4" @if($type ==4) selected="selected" @endif>C1第四天</option>
                        <option value="5" @if($type ==5) selected="selected" @endif>C1后的E班</option>
                        <option value="6" @if($type ==6) selected="selected" @endif>C1后的休息第一天</option>
                        <option value="7" @if($type ==7) selected="selected" @endif>C1后的休息第二天</option>


                        <option value="8" @if($type ==8) selected="selected" @endif>C2第一天</option>
                        <option value="9" @if($type ==9) selected="selected" @endif>C2第二天 </option>
                        <option value="10" @if($type ==10) selected="selected" @endif>C2第三天 </option>
                        <option value="11" @if($type ==11) selected="selected" @endif>C2第四天</option>
                        <option value="12" @if($type ==12) selected="selected" @endif>C2后的E班 </option>
                        <option value="13" @if($type ==13) selected="selected" @endif>C2后休息第一天</option>
                        <option value="14" @if($type ==14) selected="selected" @endif>C2后的休息第二天</option>


                        <option value="15" @if($type ==15) selected="selected" @endif>C4第一天 </option>
                        <option value="16" @if($type ==16) selected="selected" @endif>C4第二天 </option>
                        <option value="17" @if($type ==17) selected="selected" @endif>C4第三天</option>
                        <option value="18" @if($type ==18) selected="selected" @endif>C4第四天  </option>
                        <option value="19" @if($type ==19) selected="selected" @endif>C4后的E班  </option>
                        <option value="20" @if($type ==20) selected="selected" @endif>C4休息第一天</option>
                        <option value="21" @if($type ==21) selected="selected" @endif>C4休息第二天</option>


                        <option value="22" @if($type ==22) selected="selected" @endif>D1第一天 </option>
                        <option value="23" @if($type ==23) selected="selected" @endif>D1第二天</option>
                        <option value="24" @if($type ==24) selected="selected" @endif>D1第三天 </option>
                        <option value="25" @if($type ==25) selected="selected" @endif>D1第四天 </option>
                        <option value="26" @if($type ==26) selected="selected" @endif>D1后的E班 </option>
                        <option value="27" @if($type ==27) selected="selected" @endif>D1后的休息第一天</option>
                        <option value="28" @if($type ==28) selected="selected" @endif>D1后的休息第二天</option>


                    </select>
                </div>
            </div>
        </div>
            <div class="layui-form-item">
             <label for="L_repass" class="layui-form-label">
             </label>
             <button  class="layui-btn" lay-filter="add" lay-submit="submit" id="edit_check">
                 修改
             </button>
        </div>
      </form>
    </div>
    <script>

    </script>
  </body>
</html>