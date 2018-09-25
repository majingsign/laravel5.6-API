<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>欢迎页面-忆享科技内部排班系统</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="./css/font.css">
        <link rel="stylesheet" href="./css/xadmin.css">
        <script src="https://cdn.bootcss.com/echarts/3.3.2/echarts.min.js" charset="utf-8"></script>
    </head>
    <body>
    <div class="x-body layui-anim layui-anim-up">
        <blockquote class="layui-elem-quote">欢迎管理员：
            <span class="x-red">{{Session::get('username')}}</span>！当前时间:<?php echo date('Y-m-d H:i:s',time())?>
        </blockquote>
        <fieldset class="layui-elem-field">
            @if(!empty($list))
            <legend style="color: red;">系统通知</legend>
            <div class="layui-field-box">
                <table class="layui-table" lay-size="sm">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>公司</th>
                        <th>部门</th>
                        <th>姓名</th>
                        <th>申请时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$value->com_name}}</td>
                            <td>{{$value->depart_name}}</td>
                            <td>{{$value->username}}</td>
                            <td>{{date('Y-m-d H:i',$value->create_at)}}</td>
                            <td><span style="color: #045fe8;">审核中</span></td>
                            <td><i class="layui-icon">&#xe605;</i>&nbsp;&nbsp;<a style="color: red;" href="{{route('admin.member.qingjia',['userid'=>$value->userid])}}">去通过</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </fieldset>
        @endif
        <fieldset class="layui-elem-field">
            <legend>系统数据统计</legend>
            <div class="layui-field-box">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-carousel x-admin-carousel x-admin-backlog" lay-anim="" lay-indicator="inside" lay-arrow="none" style="width: 100%; height: 90px;">
                                <div carousel-item="">
                                    <ul class="layui-row layui-col-space10 layui-this">
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>售前工单人数</h3>
                                                <p>
                                                    <cite>{{$sum_shouqian}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>备案审核人数</h3>
                                                <p>
                                                    <cite>{{$sum_beian}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>离职人数</h3>
                                                <p>
                                                    <cite>{{$sum_del}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>在职人数</h3>
                                                <p>
                                                    <cite>{{$sum_worker}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>已添加省份个数</h3>
                                                <p>
                                                    <cite>{{$citySum}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs2">
                                            <a href="javascript:;" class="x-admin-backlog-body">
                                                <h3>部门总数</h3>
                                                <p>
                                                    <cite>{{$departSum}}</cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset><br/>
        <div id="main" style="width: 100%;height:400px;"></div>
    </div>
    </body>

<script type="application/javascript">
    var myChart = echarts.init(document.getElementById('main'));
    var option = {
        title : {
            text: '系统数据统计',
            subtext: '结构图',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            x : 'center',
            y : 'bottom',
            data:['售前工单人数','备案审核人数','离职人数','在职人数','已添加省份个数','部门总数','公司总数','管理员总数']
        },
        toolbox: {
            show : true,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                magicType : {
                    show: true,
                    type: ['pie', 'funnel']
                },
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'半径模式',
                type:'pie',
                radius : [20, 110],
                center : ['25%', 200],
                roseType : 'radius',
                width: '40%',       // for funnel
                max: 40,            // for funnel
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true
                        },
                        labelLine : {
                            show : true
                        }
                    }
                },
                data:[
                    {value:{{$sum_shouqian}}, name:'售前工单人数'},
                    {value:{{$sum_beian}}, name:'备案审核人数'},
                    {value:{{$sum_del}}, name:'离职人数'},
                    {value:{{$sum_worker}}, name:'在职人数'},
                    {value:{{$citySum}}, name:'已添加省份个数'},
                    {value:{{$citySum}}, name:'部门总数'},
                    {value:{{$companySum}}, name:'公司总数'},
                    {value:{{$adminSum}}, name:'管理员总数'}
                ]
            },
            {
                name:'面积模式',
                type:'pie',
                radius : [30, 110],
                center : ['75%', 200],
                roseType : 'area',
                x: '50%',               // for funnel
                max: 40,                // for funnel
                sort : 'ascending',     // for funnel
                data:[
                    {value:{{$sum_shouqian}}, name:'售前工单人数'},
                    {value:{{$sum_beian}}, name:'备案审核人数'},
                    {value:{{$sum_del}}, name:'离职人数'},
                    {value:{{$sum_worker}}, name:'在职人数'},
                    {value:{{$citySum}}, name:'已添加省份个数'},
                    {value:{{$citySum}}, name:'部门总数'},
                    {value:{{$companySum}}, name:'公司总数'},
                    {value:{{$adminSum}}, name:'管理员总数'}
                ]
            }
        ]
    };
    myChart.setOption(option);
</script>
</html>