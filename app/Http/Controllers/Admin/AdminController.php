<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 9:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends AdminBaseController {

    /**
     * 管理员列表
     * @param Request $request
     */
    public function list (Request $request) {
        $admin = new Admin();
        $list = $admin->adminList();
        return view('admin.admin.list',['list'=>$list]);
    }

    /**
     * 添加管理员
     * @param Request $request
     */
    public function add() {
        return view('admin.admin.add');
    }
    /**
     * 添加管理员
     * @param Request $request
     */
    public function addAdmin(Request $request) {
        $usernmae = $request->input('username');
        $pass     = $request->input('pass');
        $admin = new Admin();
        if($admin->adminName($usernmae)){
            return ['code'=>0,'msg'=>'此管理员已存在'];
        }
        if($admin->adminAdd(['admin_name'=>$usernmae,'admin_password'=>md5($pass)])){
            return ['code'=>200,'msg'=>'添加成功'];
        }else{
            return ['code'=>0,'msg'=>'添加失败'];
        }
    }

    /**
     * 删除管理员
     * @param Request $request
     */
    public function del(Request $request){
        $id = $request->input('id');
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $admin =new Admin();
        if($admin->adminDel($id)){
            return ['code'=>200,'msg'=>'删除成功'];
        }else{
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function editpwd (Request $request) {
        return view('admin.admin.password');
    }

    /**
     * 保存密码
     * @param Request $request
     */
    public function savepwd(Request $request){
        $newpass = $request->input('newpass');
        $repass  = $request->input('repass');
        $messages = [
            'newpass'   => '密码不能为空!',
            'repass'    => '确认密码不能为空!',
        ];
        $validator = Validator::make($request->all(), [
            'newpass' => 'required',
            'repass'  => 'required',
        ],$messages);
        if ($validator->fails()) {
            return ['code'=>0,'msg'=>'信息填写有误'];
        }
        $id = Session::get('adminid');
        if($id == null || empty($id) || $id == ''){
            return ['code'=>100,'msg'=>'登陆已过期，请重新登陆...'];
        }
        $newpass = md5($newpass);
        $admin = new Admin();
        if(!$admin->adminEditPwd($id,$newpass)){
            return ['code'=>0,'msg'=>'修改失败'];
        }
        Session::flush();
        return ['code'=>200,'msg'=>'修改成功'];
    }
}