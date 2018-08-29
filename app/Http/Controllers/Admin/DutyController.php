<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 9:58
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\Duty;
use Illuminate\Http\Request;

class DutyController extends AdminBaseController {

    /**
     * 查看全部类型
     */
    public function list () {
        $duty = new Duty();
        $list = $duty->dutyList();
        return view('admin.duty.list',['list'=>$list]);
    }

    /**
     * 新增类型
     */
    public function add () {
        return view('admin.duty.add',['type'=>$this->type]);
    }

    /**
     * 删除类型
     */
    public function del (Request $request) {
        $dutyid = $request->input('id');
        if(empty($dutyid)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $duty = new Duty();
        if($duty->dutyDel($dutyid)){
            return ['code'=>200,'msg'=>'删除成功'];
        }else{
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

    /**
     * 修改类型
     */
    public function edit (Request $request) {
        $dutyid = $request->input('dutyid');
        $duty = new Duty();
        $list = $duty->findDutyId($dutyid);
        return view('admin.duty.edit',['list'=>$list,'type'=>$this->type]);
    }

    /**
     * 新增保存
     */
    public function addDuty (Request $request) {
        $name   = $request->input('name');
        $type   = $request->input('type');
        $ontime = $request->input('ontime');
        if(empty($name) || $name == ''){
            return ['code' => 0,'msg' => '名称必填'];
        }
        if(empty($type) || $type == ''){
            return ['code' => 0,'msg' => '未选择排班类型'];
        }
        if(empty($ontime) || $ontime == ''){
            return ['code' => 0,'msg' => '时间段必填'];
        }
        $duty = new Duty();
        if($duty->findDutyName($name)){
            return ['code' => 0,'msg' => '类型已存在'];
        }
        if($duty->dutyAdd(['name'=>$name,'duty_type'=>$type,'on_time'=>$ontime,'create_time'=>time()])){
            return ['code' => 200,'msg' => '新增成功'];
        }else{
            return ['code' => 0,'msg' => '新增失败'];
        }
    }

    /**
     * 修改保存
     */
    public function saveDuty (Request $request) {
        $dutyid = $request->input('dutyid');
        $name   = $request->input('name');
        $type   = $request->input('type');
        $ontime = $request->input('ontime');
        if(empty($dutyid) || $dutyid == ''){
            return ['code' => 0,'msg' => '参数错误'];
        }
        if(empty($name) || $name == ''){
            return ['code' => 0,'msg' => '名称必填'];
        }
        if(empty($type) || $type == ''){
            return ['code' => 0,'msg' => '未选择排班类型'];
        }
        if(empty($ontime) || $ontime == ''){
            return ['code' => 0,'msg' => '时间段必填'];
        }
        $duty = new Duty();
        if($duty->findDutyName($name)){
            return ['code' => 0,'msg' => '类型已存在'];
        }
        if($duty->dutyEdit($dutyid,['id'=>$dutyid,'name'=>$name,'duty_type'=>$type,'on_time'=>$ontime])){
            return ['code' => 200,'msg' => '更新成功'];
        }else{
            return ['code' => 0,'msg' => '更新失败'];
        }
    }

}