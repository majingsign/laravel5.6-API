<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 15:05
 */

namespace App\Http\Controllers\Admin;
use App\Http\Model\Admin;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Records;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * 公司
 * Class CompanyController
 * @package App\Http\Controllers\Admin
 */
class CompanyController extends AdminBaseController {

    /**
     * 公司列表
     */
    public function list (){
        $company = new Company();
        $list = $company->companyList();
        return view('admin.company.list',['list'=>$list]);
    }

    /**
     * 新增公司
     */
    public function add (){
        //查询管理员当公司负责人
        $admin = new Admin();
        $list = $admin->adminDepartName();
        return view('admin.company.add',['list'=>$list]);
    }

    /**
     * 编辑公司
     */
    public function edit (Request $request){
        $id = $request->input('id');
        //查询管理员当公司负责人
        $admin = new Admin();
        $adminList = $admin->adminDepartName();
        $company = new Company();
        $list = $company->findCompanyId($id);
        return view('admin.company.edit',['list'=>$list,'admin'=>$adminList]);
    }

    /**
     * 删除公司
     * @param Request $request
     */
    public function del (Request $request){
        $id = $request->input('id');
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $company = new Company();
        DB::beginTransaction();
        //删除公司
        if(!$company->companyDel($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        $depart = new Depart();
        //所有部门的id
        $departids = $depart->findDepartCompanyId($id);
        //删除该部门下所有的管理员和员工
        $admin   = new Admin();
        $member  = new Member();
        $records = new Records();
        if(!empty($departids)){
            foreach ($departids as $v){
                if(!$admin->adminDel($v->id)){
                    DB::rollBack();
                    return ['code'=>0,'msg'=>'删除失败'];
                }
                if(!$member->delMemberDepartId($v->id)){
                    DB::rollBack();
                    return ['code'=>0,'msg'=>'删除失败'];
                }
            }
        }
        //删除部门
        if(!$depart->delDepartCompanyId($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        //删除考勤
        if(!$records->delRecordsCompany($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        DB::commit();
        return ['code'=>200,'msg'=>'删除成功'];

    }

    /**
     * 新增保存公司
     * @param Request $request
     */
    public function addCompany (Request $request){
        $name    = $request->input('name');
        $desc    = $request->input('desc');
        $adminid = $request->input('company');
        if($name == '' || empty($name)){
            return ['code'=>0,'msg'=>'公司名称必填'];
        }
//        if($adminid == '' || empty($adminid) || $adminid == 0){
//            return ['code'=>0,'msg'=>'公司负责人必选'];
//        }
        $admin = new Admin();
        $adminname = $admin->findAdminId($adminid);
//        if($adminname == null || empty($adminname)){
//            return ['code'=>0,'msg'=>'参数错误'];
//        }
        $company = new Company();
        if($company->companyAdd(['name'=>$name,'admin_id'=>$adminid,'admin_name'=>$adminname,'desc'=>$desc])){
            return ['code'=>200,'msg'=>'新增成功'];
        }else{
            return ['code'=>0,'msg'=>'新增失败'];
        }
    }

    /**
     * 编辑保存公司
     * @param Request $request
     */
    public function saveCompany (Request $request){
        $id      = $request->input('id');
        $name    = $request->input('name');
        $adminid = $request->input('company');
        $desc    = $request->input('desc');
        if($name == '' || empty($name)){
            return ['code'=>0,'msg'=>'公司名称必填'];
        }
//        if($adminid == '' || empty($adminid) || $adminid == 0){
//            return ['code'=>0,'msg'=>'负责人必选'];
//        }
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $admin = new Admin();
        $adminname = $admin->findAdminId($adminid);
//        if($adminname == null || empty($adminname)){
//            return ['code'=>0,'msg'=>'参数错误'];
//        }
        $company = new Company();
        if($company->companyEdit($id,['name'=>$name,'desc'=>$desc,'admin_name'=>$adminname,'admin_id'=>$adminid])){
            return ['code'=>200,'msg'=>'编辑成功'];
        }else{
            return ['code'=>0,'msg'=>'编辑失败'];
        }
    }

}