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
use App\Http\Model\Qingjia;
use App\Http\Model\Records;
use App\Http\Model\UserCity;
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
        return view('admin.user.list',['list'=>$list,'username'=>$username,'status'=>$this->type,'userstatus'=>$userstatus]);
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


    public function qingjia(Request $request){
        $userid = $request->input('userid');
        // 查询请假记录
        $qj = new Qingjia();
        $act = $this->checkDepart();
        $comAdmin = Session::get('comAdmin');
        if($act == false){
            $list = $qj->qjList(0,0,$userid);
        }else{
            //判断是否是企业负责人
            if(!empty($comAdmin) || $comAdmin != ''){
                $list = $qj->qjList($comAdmin,$act,$userid);
            }else{
                $list = $qj->qjList(0,$act,$userid);
            }
        }

        $member = new Member();
        $user   = $member->memberFindId($userid);
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
        $usercy  = $usercity->findUserCityId($userid);
        $usercys = array();
        if(!empty($usercy)){
            foreach ($usercy as $value){
                $usercys [] = $value->city_id;
            }
        }
        $usercys = array_unique($usercys);
        return view('admin.user.qingjia',['type'=>$this->type,'user'=>$user,'usercy'=>$usercys,'departList'=>$departList,'company'=>$com,'qingjia'=>$this->qingjia,'list'=>$list]);
    }

    public function ajaxPass(Request $request){
        $id = $request->input('id');
        $qj = new Qingjia();
        if($qj->updateQj($id,['is_pass'=>1,'pass_time'=>time()])){
            return ['code'=>200,'msg'=>'通过成功'];
        }else{
            return ['code'=>0,'msg'=>'通过失败'];
        }
    }

    /**
     * 部门负责人添加 将直接通过
     * @param Request $request
     * @return array
     */
    public function qingjiasave(Request $request){
        $id         = $request->input('id','');       //用户id
        $username   = $request->input('username','');//开始时间
        $starttime  = $request->input('starttime','');//开始时间
        $endtime    = $request->input('endtime','');  //结束时间
        $desc       = $request->input('desc','');     //请假事由
        $company    = $request->input('company','');  //公司id
        $depart     = $request->input('depart','');   //部门id
        $type       = $request->input('type','');     //请假类型

        if($id == '' || $starttime == '' || $endtime == '' || $desc == '' || $company == '' || $depart == '' || $type == ''){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $long_time = strtotime($endtime) - strtotime($starttime);   //小时
        $depar = new Depart();
        $departs = $depar->findDepartId($depart);
        $admin_id = 0;
        if($departs){
            $admin_id = $departs->admin_id;
        }
        $qj = new Qingjia();
        $times = time();
        //判断是否有多条数据
        $start_day = date('d',strtotime($starttime));  //开始请假时间（天）
        $end_day   = date('d',strtotime($endtime));    //结束请假时间（天）
        //当天请假
        if($start_day == $end_day){
            $ids = $qj->qjAdd([
                'userid'    =>  $id,
                'comid'     =>  $company,
                'departid'  =>  $depart,
                'type'      =>  $type,
                'start_time'=>  strtotime($starttime),
                'end_time'  =>  strtotime($endtime),
                'long_time' =>  $long_time,
                'desc'      =>  $desc,
                'is_pass'   =>  1,
                'create_at' =>  $times,
                'pass_time' =>  $times,
                'admin_id'  =>  $admin_id,
                'username'  =>  $username,
                'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
            ]);
            $qj->updateQjId($ids,['bindid'=>$ids]);
            //不是当天，跨天数
        }else{
            $days = $end_day - $start_day;//夸了多少天
            if($days > 2){
                //第一天起的时间
                $ids = $qj->qjAdd([
                    'userid'    =>  $id,
                    'comid'     =>  $company,
                    'departid'  =>  $depart,
                    'type'      =>  $type,
                    'start_time'=>  strtotime($starttime),
                    'end_time'  =>  $this->day_time_end(strtotime($starttime)),
                    'long_time' =>  $long_time,
                    'desc'      =>  $desc,
                    'is_pass'   =>  1,
                    'create_at' =>  $times,
                    'pass_time' =>  $times,
                    'admin_id'  =>  $admin_id,
                    'username'  =>  $username,
                    'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                ]);
                $qj->updateQjId($ids,['bindid'=>$ids]);
                //中间的时间
                for ($i = 1;$i < $days + 1 ;$i ++) {
                    $qj->qjAdd([
                        'userid'    =>  $id,
                        'comid'     =>  $company,
                        'departid'  =>  $depart,
                        'type'      =>  $type,
                        'start_time'=>  $this->day_time_start(strtotime("$starttime +$i day")),
                        'end_time'  =>  $this->day_time_end(strtotime("$starttime +$i day")),
                        'long_time' =>  $long_time,
                        'desc'      =>  $desc,
                        'is_pass'   =>  1,
                        'create_at' =>  $times,
                        'pass_time' =>  $times,
                        'admin_id'  =>  $admin_id,
                        'username'  =>  $username,
                        'bindid'    =>  $ids,
                        'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                    ]);
//                    $qj->updateQj($ids,['bindid'=>$ids]);
                }
                //最后一天的时间
                $qj->qjAdd([
                    'userid'    =>  $id,
                    'comid'     =>  $company,
                    'departid'  =>  $depart,
                    'type'      =>  $type,
                    'start_time'=>  $this->day_time_start(strtotime($endtime)),
                    'end_time'  =>  strtotime($endtime),
                    'long_time' =>  $long_time,
                    'desc'      =>  $desc,
                    'is_pass'   =>  1,
                    'create_at' =>  $times,
                    'pass_time' =>  $times,
                    'admin_id'  =>  $admin_id,
                    'username'  =>  $username,
                    'bindid'    =>  $ids,
                    'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                ]);

            }else{
                //第一天起的时间
                $ids = $qj->qjAdd([
                    'userid'    =>  $id,
                    'comid'     =>  $company,
                    'departid'  =>  $depart,
                    'type'      =>  $type,
                    'start_time'=>  strtotime($starttime),
                    'end_time'  =>  $this->day_time_end(strtotime($starttime)),
                    'long_time' =>  $long_time,
                    'desc'      =>  $desc,
                    'is_pass'   =>  1,
                    'create_at' =>  $times,
                    'pass_time' =>  $times,
                    'admin_id'  =>  $admin_id,
                    'username'  =>  $username,
                    'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                ]);
                $qj->updateQjId($ids,['bindid'=>$ids]);
                //最后一天的时间
                $qj->qjAdd([
                    'userid'    =>  $id,
                    'comid'     =>  $company,
                    'departid'  =>  $depart,
                    'type'      =>  $type,
                    'start_time'=>  $this->day_time_start(strtotime($endtime)),
                    'end_time'  =>  strtotime($endtime),
                    'long_time' =>  $long_time,
                    'desc'      =>  $desc,
                    'is_pass'   =>  1,
                    'create_at' =>  $times,
                    'pass_time' =>  $times,
                    'admin_id'  =>  $admin_id,
                    'username'  =>  $username,
                    'bindid'    =>  $ids,
                    'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                ]);
            }
        }
        return ['code'=>200,'msg'=>'提交成功'];
    }

    //开始时间
    public function day_time_start($date){
        return mktime(0,0,0,date('m',$date),date('d',$date),date('Y',$date));
    }
    //结束时间
    public function day_time_end($date){
        return mktime(0,0,0,date('m',$date),date('d',$date)+1,date('Y',$date))-1;
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
        $url = 'http://wx.uktong.cn/wx_att/';
        $check = time().rand(1000,9999);
        return view('admin.user.add',['list'=>$list,'type'=>$this->type,'departList'=>$departList,'company'=>$com,'check'=>$check,'url'=>$url]);
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
        $openid   = $request->input('openid');
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
        if($openid == '' || empty($openid)){
            return ['code'=>0,'msg'=>'openid必填'];
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
            'com_id'      => $companyid,
            'openid'      => $openid
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