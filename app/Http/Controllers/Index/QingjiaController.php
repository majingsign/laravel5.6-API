<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 11:08
 */

namespace App\Http\Controllers\Index;


use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Qingjia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QingjiaController extends IndexBaseController {

    public function list(){
        $memberid = Session::get('memberid');
        if(empty($memberid)){
            return redirect(route('index.login.login'));
        }
        $qingjia = new Qingjia();
        $list = $qingjia->qjPassUser($memberid);
        return view('index.qingjia.list',['list'=>$list]);
    }

    //提交申请
    public function add(){
        $memberid = Session::get('memberid');
        if(empty($memberid)){
            return redirect(route('index.login.login'));
        }
        $member = new Member();
        $user   = $member->memberFindId($memberid);
        //选择公司
        $company = new Company();
        if($user && $user->com_id){
            $com = $company->companyListsId($user->com_id);
        }else{
            $com = $company->companyLists();
        }
        //部门信息
        $depart = new Depart();
        $departList = $depart->findDepartId($user->depar_id);
        return view('index.qingjia.add',['user'=>$user,'departList'=>$departList,'company'=>$com,'qingjia'=>$this->qingjia]);
    }


    /*
     * 提交需要审核
     */
    public function QingjiaAdd(Request $request){
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
                'is_pass'   =>  0,
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
                    'is_pass'   =>  0,
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
                        'is_pass'   =>  0,
                        'create_at' =>  $times,
                        'pass_time' =>  $times,
                        'admin_id'  =>  $admin_id,
                        'username'  =>  $username,
                        'bindid'    =>  $ids,
                        'date_tme'  =>  date('Y-m-d',strtotime($starttime)),
                    ]);
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
                    'is_pass'   =>  0,
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
                    'is_pass'   =>  0,
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
                    'is_pass'   =>  0,
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


}