<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>轮休当月排班列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
          <cite>轮休当月排班列表</cite></a>
      </span>
</div>
<div style="display: flex">
    <div style="flex: 0.9">
        <div style="margin-top: 20px;font-size: 12px;">
            @foreach($type_list as $type)
                @if($type -> name== "T")
                    <span style="color: rgb(251, 131, 43)"> {{$type -> name}} {{$type -> on_time}}&nbsp;&nbsp;</span>
                @elseif($type -> name == "C1")
                    <span style="color: rgb(125, 43, 251)"> {{$type -> name}} {{$type -> on_time}}&nbsp;&nbsp;</span>
                @elseif($type -> name == "C2")
                    <span style="color: rgb(27, 241, 232)"> {{$type -> name}} {{$type -> on_time}}&nbsp;&nbsp;</span>
                @elseif($type -> name == "C4")
                    <span style="color: rgb(27, 121, 242)"> {{$type -> name}} {{$type -> on_time}}&nbsp;&nbsp;</span>
                @elseif($type -> name == "E")
                @endif

            @endforeach
        </div>
        <br>
        <span style="color: red; font-size: 15px;font-weight: bold;">系统已为你生成最新轮班排班,如需修改, 请双击修改</span>
    </div>

    <div class="x-body">
        <div class="layui-row" style="display: flex">
            @if(empty($next_list))
             <div><button class="generate layui-btn layui-btn-radius">生成下月的轮休排班内容</button></div>
                @else
               <div>
                  <a href="{{route('admin.rotation.seenextgenerate')}}">
                      <button class="see layui-btn layui-btn-radius">查看下月的轮休排班内容</button>
                  </a>
               </div>
                @endif
                @if(count($list) > 0)
            <div style="margin-left: 50px;">
               <a href="{{route('admin.rotation.currexcelport')}}">
                   <button class="layui-btn layui-btn-radius layui-btn-normal cur-export">导出本月数据</button>
               </a>
            </div>
                @endif

        </div>
    </div>
</div>

@if(count($list) > 0)

    <table class="layui-table">
        <thead>
        <tr>
            <th>用户名</th>
            @foreach($week_list as $week)
                <th>{{$week['day']}}- {{$week['week']}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($list as  $k => $user)
            <tr>
                <input type="hidden" class="user_id" value="{{$k}}"/>
                <td>{{$user['user_name']}}</td>
                @foreach($user['work'] as $k => $work)
                    @if($work == '休')
                        <td style="color: #66666682;" class="show_td" data-k="{{$k}}">{{$work}}</td>
                    @elseif($work == "T")
                        <td style="color: rgb(251, 131, 43)" class="show_td" data-k="{{$k}}">{{$work}}</td>
                        @elseif($work == "---")
                        <td style="color: rgb(173, 154, 147)" class="show_td" data-k="{{$k}}">{{$work}}</td>
                        @else
                        <td  class="show_td" data-k="{{$k}}">{{$work}}</td>
                    @endif

                @endforeach
            </tr>
        @endforeach()
        </tbody>
    </table>

    <button class="layui-btn layui-btn-radius save">保存本月的值班内容</button>
    @endif



</div>

<script>
   
    $(function () {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        //动态修改样式信息
        var week_arr = new Array();
        $('th').each(function (i) {
            if($(this).text().indexOf("星期六") != -1 || $(this).text().indexOf("星期天") != -1){
                week_arr.push(i);
            }
        })
        $('tr').each(function (ii,val) {
            $(this).find('td').each(function(a,b){
                if(week_arr.indexOf(a) != -1){
                    $(this).css('background','rgba(234,227,236,1)');
                }
            })
        })


        //当点击修改班别的时候
        $('td').dblclick(function(){
          //判断是否是可以修改
              var Nowdate=new Date();
                  var day = Nowdate.getDate();
                 var date_k = parseInt($(this).attr('data-k')) +1 ;

              var text = $(this).text();
            if(text == '---'){
                   layer.alert('当天该员工还没有入职');
                   return false;
              }
            $('table').find('.edit_td').removeClass('edit_td');
            str = '<form class="layui-form" action="#">\
              <div class="layui-form-item">\
                <div class="layui-inline">\
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>\
                <div class="layui-input-inline">\
                <select name="type" lay-verify="type"   style="display: block;height:32px;" id="change_type">';


            if(day >= date_k  ){
                if(text == 'T'){
                    str += '<option value="T" selected="selected">T</option><option value="">请假或离职</option>';
                } else if(text == '休'){
                    str += ' <option value="休" selected="selected">休</option><option value="">请假或离职</option>';
                } else if(text == ''){
                    str += '<option value="" selected="selected">请假或离职</option>';
                }
            } else {
                if(text == 'T'){
                    str += '<option value="T" selected="selected">T</option><option>休</option><option value="">请假或离职</option>';
                } else if(text == '休'){
                    str += ' <option value="休" selected="selected">休</option><option>T</option><option value="">请假或离职</option>';
                } else if(text == ''){
                    str += '<option value="休" >休</option><option>T</option><option value="" selected="selected">请假或离职</option>';
                }

            }



            str += ' </select>\
                </div>\
                </div>\
                </div>\
                <div class="layui-form-item">\
                <label for="L_repass" class="layui-form-label">\
                </label>\
                <button  class="layui-btn" lay-filter="add" lay-submit="submit" id="edit_check">修改</button>\
                </div>\
                </form>';
            layer.open({
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', '240px'], //宽高
                content: str
            });

            $(document).on('click', '#edit_check', function() {
                var text = $('#change_type').val();
                $('.edit_td').text(text);
                if(text == '休'){
                    $('.edit_td').css('color',' #66666682');
                } else {
                    $('.edit_td').css('color',' rgb(251, 131, 43)');
                }
                layer.closeAll();
                return false;
            });

            $(this).addClass('edit_td');
        })


        //当点击保存本月的轮班系统的时候
        var flag = true;
        $('.save').click(function () {
            var month_day = getCurrentMonthDay();
             var arr = new Array();
            $('tbody tr').each(function(i,val){
                var user_id = $(this).find('.user_id').val();
                var a = new Array();
                $(this).find('.show_td').each(function () {
                    a.push($(this).text());
                })
                arr[user_id]= new Array();
                arr[user_id]= a;
                if(month_day == a.length){
                    if(a[a.length-1] == ''){
                        flag = false;
                    }
                }

            })
           if(!flag){
               layer.confirm('由于你修改了本月最后一天数据设置为请假或者离职,系统暂不清楚是否涉及下月的排班安排,如需修改请手动修改最后一天轮班数据', {
                   btn: ['确定','暂不修改'] //按钮
               }, function(){
                   saveList(arr);
               }, function(){
                   layer.closeAll();
                     return false;
               });
           } else {
               saveList(arr);
        }
        })


        function saveList(arr) {
            layer.load(3, {shade: false});
            $.ajax({
                url: '{{route("admin.rotation.shiftsave")}}',
                type: 'post',
                dataType: 'json',
                data: {"list_arr": arr, 'is_next': 0},
                success: function (data) {
                    if (data.code == 1) {
                        layer.alert('数据保存成功且最后一天数据已更新成功');
                        location.href = '/admin/rotation/index';
                    } else {
                        layer.alert('保存失败');
                    }
                }
            })
        }



        //点击生成下个月的轮班系统
        $('.generate').click(function () {
            layer.confirm('为确保数据的正确性,请先确认本月轮休最后一天上班内容,如果已将正常的上班打乱,请手动修改最后一天的上班情况', {
                btn: ['确定','取消'] //按钮
            }, function(){
                nextShift();
            }, function(){
               layer.closeAll();
            });




        })



        function nextShift(){
            $.ajax({
                url : '{{route("admin.rotation.checkgenerate")}}',
                type :'get',
                dataType : 'json',
                success: function (data) {
                    if(data.code == 0){
                        alert(data.message);
                        layer.closeAll();
                        return false;
                    } else {
                        var str = '<h3 style="color:red;">下月离职名单</h3><table class="layui-table">\
                                    <thead><tr><th>用户名</th><th>离职时间</th></thead>\
                                    <tbody>';
                        if(data.message.length > 0){
                            $.each(data.message,function(i,val){
                                str += ' <tr><td>'+val.user_name+'</td><td>'+val.leve_time+'</td></tr>';
                            })
                        } else {
                            str += '<tr><td colspan="2">下个月暂无离职人员</td></tr>'
                        }
                        str +=  '</tbody>\
                      </table><a href="/admin/rotation/shift"><button class=" layui-btn layui-btn-radius">确定</button></a>\
                      <a href="/admin/admin/list"><button class=" layui-btn layui-btn-radius" style="margin-left: 50px;">修改离职时间</button></a>'
                    }
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['420px', '240px'], //宽高
                        content: str
                    });



                }
            })
        }

        /**
         * 获取本月一共有多少天
         * @returns {number}
         */
        function getCurrentMonthDay() {
            var d = new Date();
          return  new Date(d.getFullYear(), (d.getMonth()+1), 0).getDate();
        }



    })

</script>
</body>
</html>