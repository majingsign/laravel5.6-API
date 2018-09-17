<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 9:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\Admin;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends AdminBaseController {

    /**
     * 管理员列表
     * @param Request $request
     */
    public function list (Request $request) {
        $adminname = $request->input('username');
        $admin = new Admin();
        $comAdmin = Session::get('comAdmin');
        if(!empty($comAdmin) || $comAdmin != ''){
            $list = $admin->adminList($comAdmin,$adminname);
        }else{
            $list = $admin->adminList(0,$adminname);
        }
        return view('admin.admin.list',['list'=>$list,'admin'=>$adminname]);
    }

    /**
     * 添加管理员
     * @param Request $request
     */
    public function add() {
        $company = new Company();
        $com_id = Session::get('comAdmin');
        if($com_id){
            $com = $company->companyListsId($com_id);
        }else{
            $com = $company->companyLists();
        }
//        $depart = new Depart();
//        $departs = $depart->departLists();
        return view('admin.admin.add',['company'=>$com]);
    }

    /**
     * 根据公司id查询部门
     * @param Request $request
     */
    public function ajaxDepart(Request $request){
        $com_id = $request->input('company');
        if(empty($com_id) || $com_id == 0 || $com_id == ''){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $depart = new Depart();
        $list = $depart->getDepartList($com_id);
        return ['code'=>200,'msg'=>'成功','data'=>$list];
    }
    /**
     * 添加管理员
     * @param Request $request
     */
    public function addAdmin(Request $request) {
        $usernmae = $request->input('username');
        $com_id   = $request->input('company');  //只做参考，实际不操作
        $pass     = $request->input('pass');
        $departs  = $request->input('depart');
        $admin = new Admin();
        if($com_id == 0 || $com_id == '' || empty($com_id) || empty($departs) || empty($usernmae) || empty($pass)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        if($admin->adminName($usernmae)){
            return ['code'=>0,'msg'=>'此管理员已存在'];
        }
        DB::beginTransaction();
        $admin_id = $admin->adminAdd(['admin_name'=>$usernmae,'admin_password'=>md5($pass),'depart_id'=>$departs]);
        if(!$admin_id){
            DB::rollBack();
            return ['code'=>0,'msg'=>'添加失败'];
        }
        if($admin_id){
            $depart = new Depart();
            if($depart->findDepartAdminId($admin_id)){
                DB::rollBack();
                return ['code'=>0,'msg'=>'不能重复设置部门负责人'];
            }
            if($depart->departEdit($departs,['admin_id'=>$admin_id])){
                DB::commit();
                return ['code'=>200,'msg'=>'添加成功'];
            }
        }
        DB::rollBack();
        return ['code'=>0,'msg'=>'添加失败'];
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
        DB::beginTransaction();
        $admin =new Admin();
        //删除管理员
        if(!$admin->adminDel($id)) {
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        //删除相关部门
        $depart = new Depart();
        if(!$depart->delAdminDepartId($id)) {
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        DB::commit();
        return ['code'=>200,'msg'=>'删除成功'];
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