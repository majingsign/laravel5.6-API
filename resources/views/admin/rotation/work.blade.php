<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>系统生成轮班系统</title>
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
          <cite>系统生成轮班系统</cite></a>
      </span>

    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>

</div>
<div style="display: flex;align-items: center;">
    <div style="flex: 0.8">
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
        <br/>
        <span style="color: red; font-size: 15px;font-weight: bold;">系统已为你生成最新轮班排班,如需修改, 请双击修改</span>
    </div>

    <div class="layui-row" style="display: flex">
        <div>
            @if($is_old == 1)
             <a href="{{route('admin.rotation.discardedgenerate')}}">
                 <button class="discarded layui-btn layui-btn-radius">
                     废弃下月轮休排班数据
                 </button>
             </a>
                @endif
        </div>

        <div style="margin-left: 50px;">
            <a href="{{route('admin.rotation.nextrexcelport')}}">
                <button class="layui-btn layui-btn-radius layui-btn-normal export-excel">
                    导出数据
                </button>
            </a>
        </div>
    </div>
</div>
<div class="x-body">
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
                   @foreach($user['work'] as $work)
                       @if($work == '休')
                        <td style="color: #66666682;" class="show_td">{{$work}}</td>
                           @else
                        <td style="color: rgb(251, 131, 43)" class="show_td">{{$work}}</td>
                           @endif

                        @endforeach
            </tr>
        @endforeach()
        </tbody>
    </table>

    <button class="generate layui-btn layui-btn-radius save">保存下月的值班内容</button>






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
          var text = $(this).text();
             $('table').find('.edit_td').removeClass('edit_td');
            str = '<form class="layui-form" action="#">\
              <div class="layui-form-item">\
                <div class="layui-inline">\
                <label class="layui-form-label"><span class="x-red">*</span>选择类型</label>\
                <div class="layui-input-inline">\
                <select name="type" lay-verify="type"   style="display: block;height:32px;" id="change_type">\
                <option value="">请选择</option>';

            if(text == 'T'){
                str += '<option value="T" selected="selected">T</option><option>休</option><option value="">请假或离职</option>';
            } else if(text == '休'){
                str += ' <option value="休" selected="selected">休</option><option>T</option><option value="">请假或离职</option>';
            } else if(text == ''){
                str += '<option value="休" >休</option><option>T</option><option value="" selected="selected">请假或离职</option>';
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


//当点击保存数据的时候
        $('.save').click(function () {
           var arr = new Array();
           $('tbody tr').each(function(i,val){
               var user_id = $(this).find('.user_id').val();
                var a = new Array();
               $(this).find('.show_td').each(function () {
                   a.push($(this).text());
               })
               arr[user_id]= new Array();
               arr[user_id]= a;
           })
            $.ajax({
                url : '{{route("admin.rotation.shiftsave")}}',
                type : 'post',
                dataType : 'json',
                data : {"list_arr" : arr,'is_next':1},
                success: function(data){
                    if(data.code == 1){
                        alert('保存成功');
                    } else {
                       alert('保存失败');
                    }
                    location.href = '/admin/rotation/index';
                }
            })




          return false;
        })

    })
</script>
</body>
</html>