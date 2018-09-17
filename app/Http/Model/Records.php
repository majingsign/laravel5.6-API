<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10
 * Time: 17:02
 */

namespace App\Http\Model;
use Illuminate\Support\Facades\DB;


/**
 * 考勤记录
 * Class Records
 * @package App\Http\Model
 */
class Records {

    private $table = 'records';

    /**
     * 查看当月全部的考勤记录
     */
    public function recordsList($start = 0,$end = 0) {
        $day_end    = date("t");   //当月有多少天
        $start_time = date('Y-m') . '-1';
        $end_time   = date('Y-m') . '-' . $day_end;
        $query = DB::table($this->table)->select('id','user_id','username','clock_time','com_id');
        if($start != 0){
            $query->where('clock_time','>',strtotime($start));
        }else if($end){
            $query->where('clock_time','<',strtotime($end));
        }else if($start != 0 && $end != 0){
            $query->whereBetween('clock_time',[strtotime($start),strtotime($end)]);
        }else{
            $query->whereBetween('clock_time',[strtotime($start_time),strtotime($end_time)]);
        }
        $list = $query->orderBy('clock_time','desc')->paginate();
        if($list){
            foreach ($list as $value){
                $value->comname = DB::table('company')->where(['id'=>$value->com_id])->value('name');
            }
        }
        return $list;
    }


    /**
     * 每天考勤记录
     * @param $data
     */
    public function recordsAdd($data){
        return DB::table($this->table)->insert($data);
    }

    /**
     * 自己考勤记录
     * @param $userid
     */
    public function findRecordsUsers($userid){
        return DB::table($this->table)->select('id','user_id','username','clock_time','com_id')->where(['user_id'=>$userid])->first();
    }

    /**
     * 查看今天是否打卡
     * @param $userid
     * @param $times
     */
    public function findClockDayRecords($userid,$times){
        return DB::table($this->table)->select('id','user_id','username','clock_time','com_id',DB::raw('count(*) as num'))->where(['user_id'=>$userid])
            ->whereBetween('clock_time',[strtotime(date('Y-m-d 00:00:00', $times)),strtotime(date('Y-m-d 23:59:59', $times))])->first();
    }

    /**
     * 每天考勤记录
     * @param $userid
     */
    public function getRecordsUsers($userid){
        return DB::table($this->table)->select('id','user_id','username','clock_time','com_id')->where(['user_id'=>$userid])->whereBetween('clock_time',[strtotime(date('Y-m-d 00:00:00', time())),strtotime(date('Y-m-d 23:59:59', time()))])->get()->toArray();
    }

    /**
     * 全部考勤记录
     * @param $userid
     */
    public function getRecordsUsersAll($userid){
        return DB::table($this->table)->select('id','user_id','username','clock_time','com_id')->where(['user_id'=>$userid])->orderBy('id','desc')->get()->toArray();
    }



    /**
     * 根据公司id删除该公司下的所有员工
     * @param $com_id
     */
    public function delRecordsCompany($com_id){
        return DB::table($this->table)->where(['com_id'=>$com_id])->delete();
    }

}