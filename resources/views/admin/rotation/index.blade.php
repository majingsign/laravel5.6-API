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
        <span style="color: red; font-size: 15px;font-weight: bold;">导入时,请将Excel格式转为csv格式</span>
    </div>

    <div class="x-body">
        <div class="layui-row" style="display: flex">
                <div>
                   <button class="import layui-btn layui-btn-radius" onclick="x_admin_show('导入','{{route('admin.rotation.importlist')}}',600,400)" href="javascript:;">导入本月数据</button>
                </div>


            <div style="margin-left: 50px;">
               <a href="{{route('admin.rotation.currexcelport')}}">
                   <button class="layui-btn layui-btn-radius layui-btn-normal cur-export">导出本月数据</button>
               </a>
            </div>


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
    })

</script>
</body>
</html>