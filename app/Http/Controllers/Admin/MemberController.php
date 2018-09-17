<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 13:26
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\City;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Records;
use App\Http\Model\UserCity;
use app\Logic\ExcelLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Logic\UserWork;
use App\Logic\ShiftLogic;
use App\Logic\InvertedLogic;

class MemberController extends AdminBaseController {


    /**
     * 全部员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function list (Request $request){
        $username   = $request->input('username');
        $userstatus = $request->input('userstatus');
        $member = new Member();
        $act = $this->checkDepart();
        $comAdmin = Session::get('comAdmin');
        if($act == false){
            $list = $member->memberList($username,$userstatus,0);
        }else{
            //判断是否是企业负责人
            if(!empty($comAdmin) || $comAdmin != ''){
                $list = $member->memberList($username,$userstatus,$act,$comAdmin);
            }else{
                $list = $member->memberList($username,$userstatus,$act);
            }
        }
        return view('admin.user.list',['list'=>$list,'username'=>$username,'status'=>$this->status,'userstatus'=>$userstatus]);
    }

    /**
     * 查看员工考勤
     * @param Request $request
     */
    public function records(Request $request){
        $userid = $request->input('userid');
        $records = new Records();
        $recordslist = $records->getRecordsUsersAll($userid);
        return view('admin.user.records',['list'=>$recordslist]);
    }

    /**
     * 查看全部的员工的考勤
     */
    public function recordsList(Request $request) {
        $start = $request->input('start');
        $end   = $request->input('end');
        $records = new Records();
        //只有开始时间
        if($start){
            $list = $records->recordsList($start,0);
        }else if($end){
            //只有结束时间
            $list = $records->recordsList(0,$end);
        }else if($start && $end){
            //只有开始和结束时间
            $list = $records->recordsList($start,$end);
        }
        if(empty($start) && empty($end)){
            //查看这个月的员工考勤
            $list = $records->recordsList();
        }
        return view('admin.user.show',['list'=>$list,'start'=>$start,'end'=>$end]);
    }

    /**
     * 新增员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add () {
        $depart = new Depart();
        $departid = Session::get('departid');

        //选择公司
        $company = new Company();
        $com_id = Session::get('comAdmin');
        if($com_id){
            $com = $company->companyListsId($com_id);
        }else{
            $com = $company->companyLists();
        }
        //超级管理员
        if($depart->findDepartMenuList($departid) == '*'){
            $departList = $depart->departLists();
        }else{
            $departList = $depart->getDepartId($departid);
        }
        $city = new City();
        $list = $city->cityList();
        return view('admin.user.add',['list'=>$list,'type'=>$this->type,'departList'=>$departList,'company'=>$com]);
    }

    /**
     * 根据公司id查询下面的部门
     * @param Request $request
     */
    public function ajaxMemberDepart(Request $request){
        $com_id = $request->input('company');
        if(empty($com_id) || $com_id == 0 || $com_id == ''){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $depart = new Depart();
        $list = $depart->getDepartList($com_id);
        return ['code'=>200,'msg'=>'成功','data'=>$list];
    }

    /**
     * 保存新增员工
     * @param Request $request
     */
    public function addMember (Request $request,UserWork $userWork,UserCity $userCity) {
        $username = $request->input('username');
        $type     = $request->input('type');
        $cityid   = $request->input('city');
        $addtime  = $request->input('addtime');
        $depart   = $request->input('depart');
        $companyid= $request->input('company');
        $is_generate = $request -> input('is_generate');
        if($username == '' || empty($username)){
            return ['code'=>0,'msg'=>'昵称为空'];
        }
        if($companyid == '' || empty($companyid) || $companyid == 0){
            return ['code'=>0,'msg'=>'公司必选'];
        }
        if($depart == '' || empty($depart) || $depart == 0){
            return ['code'=>0,'msg'=>'部门未选择'];
        }
        if($type == '' || empty($type) || $type == 0){
            return ['code'=>0,'msg'=>'排班类型未选择'];
        }
//        if($cityid == '' || empty($cityid) || $cityid == 0){
//            return ['code'=>0,'msg'=>'省份未选择'];
//        }
        if($addtime == '' || empty($addtime)){
            return ['code'=>0,'msg'=>'入职时间必填'];
        }
        $times = time();
        $member = new Member();
        //城市的名字
        $citys = new City();
        $cityname = $citys->findCityId($cityid);
//        if(empty($cityname)){
//            return ['code'=>0,'msg'=>'省份不存在'];
//        }

        DB::beginTransaction();
        //添加员工
        $userid = $member->memberAddId([
            'user_name'   => $username,
            'depar_id'    => $depart,
            'password'    => md5('00000000'),
            'input_time'  => intval(strtotime($addtime)),
            'create_time' => $times,
            'duty_type'   => $type,
            'is_del'      => 0,
            'com_id'      => $companyid
        ]);
        //添加城市
        $rst1 = $userCity ->addUserCity(['user_id'=>$userid,'city_id'=>$cityid]);
        $rst2 = true;
        //添加排班
        if($is_generate){
            if($type == 1){
                $shiftLogic = new ShiftLogic();
                $table_name  =  $shiftLogic -> getTableName(date('Y_m',time()));
            } else {
                $invertedLogic = new InvertedLogic();
                $table_name  =  $invertedLogic -> getTableName(date('Y_m',time()));
            }
            $list = $userWork -> addUserWork();
            $data = [
                'user_id'     => $userid,
                'scheduling'  => json_encode($list),
                'last_day'    => 0,  //新入职员工,不设置最后一天的数据
                'create_time' => time(),
            ];
            $rst2 =   DB::table($table_name) -> insert($data);
        }
        if($userid && $rst1 && $rst2){
            DB::commit();
            return ['code'=>200,'msg'=>'新增成功'];
        }else{
            DB::Rollback();
            return ['code'=>0,'msg'=>'新增失败'];
        }
    }

    /**
     * 编辑员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit(Request $request) {
        $userid = $request->input('userid');
        $member = new Member();
        $user = $member->memberFindId($userid);
        $city = new City();
        $list = $city->cityList();
        $usercity = new UserCity();
        $depart = new Depart();
        //选择公司
        $company = new Company();
        $com_id = Session::get('comAdmin');
        if($com_id){
            $com = $company->companyListsId($com_id);
        }else{
            $com = $company->companyLists();
        }

        //部门信息
        $departList = $depart->findDepartId($user->depar_id);
        $usercy = $usercity->findUserCityId($userid);
        $usercys = array();
        if(!empty($usercy)){
            foreach ($usercy as $value){
                $usercys [] = $value->city_id;
            }
        }
        $usercys = array_unique($usercys);
        return view('admin.user.edit',['list'=>$list,'type'=>$this->type,'user'=>$user,'usercy'=>$usercys,'departList'=>$departList,'company'=>$com]);
    }

    /**
     * 保存编辑员工
     * @param Request $request
     */
    public function editMember (Request $request) {
        $user_id  = $request->input('id');
        $username = $request->input('username');
        $type     = $request->input('type');
        $depart   = $request->input('depart');
        $cityid   = $request->input('city');
        $leve_time= $request->input('leve_time');
        if($username == '' || empty($username)){
            return ['code'=>0,'msg'=>'昵称为空'];
        }
        if($type == '' || empty($type)){
            return ['code'=>0,'msg'=>'排班类型未选择'];
        }
//        if($cityid == '' || empty($cityid)){
//            return ['code'=>0,'msg'=>'省份未选择'];
//        }
        $member = new Member();
        //员工的姓名
        if(empty($leve_time)){
            $member->memberEdit($user_id,['user_name'=>$username,'duty_type'=>$type,'is_del'=>0,'depar_id'=>$depart]);
        }else{
            $member->memberEdit($user_id,['user_name'=>$username,'duty_type'=>$type,'depar_id'=>$depart,'is_del'=>0,'leve_time'=> intval(strtotime($leve_time) + 1000)]);
        }
        $usercity = new UserCity();
        //先查询一下数据，再操作
        $oldCity = $usercity->findUserCityId($user_id);
        if(!empty($cityid)){
            //减少省份
            if(count($oldCity) > count($cityid)){
                if(!$usercity->delCityWhereIn($user_id,$cityid)){
                    return ['code'=>0,'msg'=>'修改失败'];
                }
                //新增省份
            }else if(count($oldCity) < count($cityid)){
                foreach ($cityid as $v) {
                    if(!$usercity->findUserCityIdFirst($user_id,$v)){
                        $usercity->addUserCity(['user_id'=>$user_id,'city_id'=>$v]);
                    }
                }
            }
        }
        return ['code'=>200,'msg'=>'修改成功'];
    }

    /**
     * 删除员工
     * @param Request $request
     */
    public function del (Request $request,Member $member,UserCity $userCity,ShiftLogic $shiftLogic,InvertedLogic $invertedLogic) {
        $userid = $request->input('id');
        $duty_type = $request -> input('duty_type');
        if(empty($userid)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        DB::beginTransaction();
        $rst1 = $member -> memberDel($userid);
        $rst2 = $userCity -> delUserid($userid);
        //删除排班中的数据
        if($duty_type == 1){
             list($now_table,$next_table) = $shiftLogic -> getUserScheduling();
        } else {
            list($now_table,$next_table) = $invertedLogic -> getUserInverted();
        }
        $rst3 = DB::table($now_table) -> where('user_id','=',$userid) -> delete();
        $rst4 = DB::table($next_table) -> where('user_id','=',$userid) -> delete();

        if($rst1 !== false && $rst2 !== false && $rst3 !== false && $rst4 !== false){
            DB::commit();
            return ['code'=>200,'msg'=>'删除成功'];
        } else {
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

}