<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>新增员工</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('admin/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/xadmin.css') }}">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('admin/lib/layui/layui.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('admin/js/xadmin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/qrcode.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/jquery.min.js') }}"></script>
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
              <label for="L_username" class="layui-form-label">
                  <span class="x-red">*</span>员工姓名
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_username" name="username" required="" lay-verify="nikename"
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
                                    <option value="{{$v->id}}">{{$v->name}}</option>
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
                    <select name="depart" lay-verify="depart" id="depart">
                        <option value="">--请选择部门--</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="type">
                        @if(isset($type) && !empty($type))
                            @foreach($type as $k => $t)
                                <option value="{{$k}}">{{$t}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">选择省份</label>
                <div class="layui-input-inline">
                    <select name="city" lay-verify="city">
                        <option value="">请选择省份</option>
                        @if(isset($list) && !empty($list))
                            @foreach($list as $v)
                                <option value="{{$v->id}}">{{$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="addtime" class="layui-form-label">
                <span class="x-red">*</span>入职时间
            </label>
            <div class="layui-input-inline">
                <input type="text" id="addtime" name="addtime" required=""  lay-verify="addtime" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="openid" class="layui-form-label">
                <span class="x-red">*</span>openid
            </label>
            <div class="layui-input-inline">
                <input type="text" id="openid" name="openid" required="" placeholder="扫二维码获得"  lay-verify="openid" autocomplete="off" class="layui-input">
            </div>
            <div id="Ec" style="margin-left: 50px;"></div>
        </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label"><span class="x-red"></span>是否排班</label>
                    <div class="layui-input-inline">
                        <select name="is_generate" lay-verify="depart">
                            <option value="0">否</option>
                            <option value="1">是</option>

                        </select>
                    </div>
                    <span style="font-size: 12px;color: red;">
                默认新入职员工,统一都按照轮班上班,且系统不生成本月最后一天上班数据,需自行手动修改
            </span>
                </div>

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
    <script language="javascript">
        dwidth  = 100;
        dheight = 100;
        var qrcode = new QRCode('Ec');
        qrcode.makeCode("{{$url}}getwxid1.php?check={{$check}}");
        var check="{{$check}}";
        var T=0;
        function Preload() {
            self.location.reload();
        }
        function checkqcode() {
            var str="";
            $.ajax({
                url:"{{route('index.index.checkWxPost',['check'=>$check])}}",
                type:"get",
                dataType:"text",
                timeout:5000,
                error:function(){
                    $("#opendid").html("服务器连接超时，请 <span class=FcblueB onclick='Preload()'><span class=cup>刷新</span></span> 重试");
                },
                success:function(data){
                    T++;
                    var Rd=$.trim(data);
                    var json = (new Function("return " + Rd))();
                    if(json["status"]==400){
                        if(T>=100){
                            $("#opendid").html("验证等待超时，请 <span class=FcblueB onclick='Preload()'><span class=cup>刷新</span></span> 重试");
                        }else{
                            setTimeout("checkqcode()",1000);
                        }
                    }else{
                        var data = json["data"];
                        data=(new Function("return " + data))();
                        $('#openid').val(data["openid"]);
                    }
                }
            });
        }
        setTimeout(checkqcode,3000);
    </script>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            laydate.render({
                elem: '#addtime'
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
              if(value == null || value == ''){
                return '请选择公司';
              }
            },
            depart: function(value){
              if(value == null || value == ''){
                return '请选择部门';
              }
            },
            type: function(value){
              if(value == null || value == ''){
                return '请选择类型';
              }
            // },
            // city: function(value){
            //     if(value == null || value == ''){
            //         return '请选省份';
            //     }
            },
            addtime: function(value){
                if(value == null || value == ''){
                    return '入职时间必填';
                }
            }
          });
          //监听提交
          form.on('submit(add)', function(data){
              token   = $("input[name='_token']").val();
              openid  = $("input[name='openid']").val();
              $.ajax({
                  url:"{{route('admin.member.addMember')}}",
                  type:"post",
                  data:{username:data.field.username,openid:openid,type:data.field.type,company:data.field.company,depart:data.field.depart,city:data.field.city,addtime:data.field.addtime,_token:token,is_generate:data.field.is_generate},
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