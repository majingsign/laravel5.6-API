<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 13:43
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\Admin;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DepartController extends AdminBaseController {

    /**
     * 部门列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function list (Request $request) {
        $name = $request->input('username');
        $depart  = new Depart();
        $act = $this->checkDepart();
        $comAdmin = Session::get('comAdmin');
        if($act == false){
            //等于0 是超级管理员
            $list = $depart->departList(0,0,$name);
        }else{
            //判断是否是企业负责人
            if(!empty($comAdmin) || $comAdmin != ''){
                    $list = $depart->departList($act,$comAdmin,$name);
            }else{
                $list = $depart->departList($act,0,$name);
            }
        }
        return view('admin.depart.list',['list'=>$list,'name'=>$name]);
    }

    /**
     * 新增部门
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add () {
        //公司选择
        $company = new Company();
        $menu = new Menu();
        //权限选择
        $menulist  = $menu->menuLists();
        $admin = new Admin();
        $com_id = Session::get('comAdmin');
        if($com_id){
            $com = $company->companyListsId($com_id);
            $adminlist = $admin->adminList($com_id);
        }else{
            $com = $company->companyLists();
            $adminlist = $admin->adminList();
        }

        return view('admin.depart.add',['list'=>$adminlist,'menulist'=>$menulist,'company'=>$com]);
    }

    /**
     * 编辑部门
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit (Request $request) {
        $departid = $request->input('departid');
        $admin = new Admin();
        $depart = new Depart();
        $menu = new Menu();
        $company = new Company();
        //公司选择
        $com_id = Session::get('comAdmin');
        if($com_id){
            $com = $company->companyListsId($com_id);
            $list = $admin->adminList($com_id);
        }else{
            $com = $company->companyLists();
            $list = $admin->adminList();
        }
        $menulist  = $menu->menuLists();
        $lists = $depart->findDepartId($departid);
        return view('admin.depart.edit',['list'=>$list,'lists'=>$lists,'menulist'=>$menulist,'company'=>$com]);
    }

    /**
     * 删除部门
     * @param Request $request
     */
    public function del (Request $request) {
        $id   = $request->input('id');
        if($id == '' || $id == null || $id == 0){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        DB::beginTransaction();
        $depart = new Depart();
        //需要删除该部门下所有的员工及管理员
        $admin = new Admin();
        if(!$admin->delAdminDepartId($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        $member = new Member();
        if($member->delMemberDepartId($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        if(!$depart->departDel($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        DB::commit();
        return ['code'=>200,'msg'=>'删除成功'];
    }

    /**
     * 新增保存部门
     * @param Request $request
     */
    public function addDepart (Request $request) {

        $departname = $request->input('departname');
        $menus      = $request->input('menus');
        $companyid  = $request->input('company');
//        $type       = $request->input('type');
        $desc       = $request->input('desc');
        if($departname == '' || empty($departname)){
            return ['code'=>0,'msg'=>'部门必填'];
        }
//        if($type == '' || empty($type)){
//            return ['code'=>0,'msg'=>'未选择部门负责人'];
//        }
        if($companyid == '' || $companyid == 0 || empty($companyid)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $depart = new Depart();
        if($depart->findDepartName($departname,$companyid)){
            return ['code'=>0,'msg'=>'部门已存在'];
        }
        //查询公司名称
        $com = new Company();
        $coms = $com->findCompanyId($companyid);
        if(empty($coms)){
            return ['code'=>0,'msg'=>'公司信息错误'];
        }
        $menus = implode(',',$menus);
//        if($depart->departAdd(['name'=>$departname,'admin_id'=>$type,'create_time'=>time(),'desc'=>$desc,'act_list'=>$menus])){
        if($depart->departAdd(['name'=>$departname,'create_time'=>time(),'desc'=>$desc,'act_list'=>$menus,'com_name'=>$coms->name,'com_id'=>$companyid])){
            return ['code'=>200,'msg'=>'新增成功'];
        }else{
            return ['code'=>0,'msg'=>'新增失败'];
        }
    }

    /**
     * 编辑保存部门
     * @param Request $request
     */
    public function saveDepart (Request $request) {
        $id         = $request->input('id');
        $departname = $request->input('departname');
        $admin_id   = $request->input('type');
        $desc       = $request->input('desc');
        $menus      = $request->input('menus');
        $companyid  = $request->input('company');
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        if($departname == '' || empty($departname)){
            return ['code'=>0,'msg'=>'部门必填'];
        }
//        if($admin_id == '' || empty($admin_id) || $admin_id == 0){
//            return ['code'=>0,'msg'=>'未选择部门负责人'];
//        }
        if($companyid == '' || empty($companyid) || $companyid == 0){
            return ['code'=>0,'msg'=>'未选择公司'];
        }
        $depart = new Depart();
        $depart_id = $depart->findDepartId($id);
        if(empty($depart_id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $com = new Company();
        $coms = $com->findCompanyId($companyid);
        if(empty($coms)){
            return ['code'=>0,'msg'=>'公司信息错误'];
        }
        $checkAdmin = $depart->findDepartAdminId($admin_id);
        $menus = implode(',',$menus);
        if($checkAdmin && $depart_id->admin_id != $admin_id) {
            return ['code' => 0, 'msg' => '请选择未设置的部门负责人'];
        }
        $admin = new Admin();
        $admin->adminEditDepartId($admin_id,$id);
        $depart->departEdit($id,['name'=>$departname,'admin_id'=>$admin_id,'desc'=>$desc,'act_list'=>$menus,'com_name'=>$coms->name,'com_id'=>$companyid]);
        return ['code'=>200,'msg'=>'编辑成功'];
    }

    public function show(Request $request){
        $departid = $request->input('departid');
        $depart = new Depart();
        $menu = new Menu();
        $menulist  = $menu->menuLists();
        $lists = $depart->findDepartId($departid);
        return view('admin.depart.show',['lists'=>$lists,'menulist'=>$menulist]);
    }
}