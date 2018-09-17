<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>查看权限</title>
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
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">查看权限</label>
            <table  class="layui-table layui-input-block">
                <tbody>
                @foreach($menulist as $val)
                    <tr>
                        <td>
                            <input type="checkbox" name="menus[]" lay-skin="primary"
                                   @if(!empty($lists->act_list))
                                       @if($lists->act_list == '*')
                                              checked="checked"
                                       @else
                                           @if(in_array($val->id,explode(',',$lists->act_list)))
                                           checked="checked"
                                           @endif
                                       @endif
                                   @endif
                                   class="menus" required="" title="{{$val->name}}" value="{{$val->id}}">
                        </td>
                        <td>
                            <div class="layui-input-block">
                                @if(isset($val->submenu) && !empty($val->submenu))
                                    @foreach($val->submenu as $v)
                                        <input name="menus[]" lay-skin="primary"
                                               @if(!empty($lists->act_list))
                                                   @if($lists->act_list == '*')
                                                      checked="checked"
                                                   @else
                                                       @if(in_array($v->id,explode(',',$lists->act_list)))
                                                         checked
                                                       @endif
                                                   @endif
                                               @endif
                                               class="menus" type="checkbox" title="{{$v->name}}" value="{{$v->id}}">
                                        @if(isset($v->submenu) && !empty($v->submenu))
                                            @foreach($v->submenu as $vv)
                                                <input name="menus[]" lay-skin="primary"
                                                       @if(!empty($lists->act_list))
                                                           @if($lists->act_list == '*')
                                                              checked="checked"
                                                           @else
                                                               @if(in_array($vv->id,explode(',',$lists->act_list)))
                                                                    checked
                                                               @endif
                                                           @endif
                                                       @endif
                                                       class="menus" type="checkbox" title="{{$vv->name}}" value="{{$vv->id}}">
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>
</body>
</html>