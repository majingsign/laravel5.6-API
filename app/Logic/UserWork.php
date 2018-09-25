<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 15:48
 */

namespace app\Logic;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Logic\ShiftLogic;

class UserWork
{
    /** 获取下个月第一天的日期
     * @param $date
     * @return false|string
     */
  public   function getNextMonthFirstDay($date) {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month'));
    }

    /** 获取下个月最一天的日期
     * @param $date
     * @return false|string
     */
  public   function getNextMonthLastDay($date) {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +2 month -1 day'));
    }


    /** 获取当前月的最后一天
     * @return false|string
     */
    public function getCurrentLastDay(){
        $start_time = date('Y-m-01', strtotime(date("Y-m-d")));
        return   date('Y-m-d', strtotime("$start_time +1 month -1 day"));
    }

    /**
     * 获取当前月的第一天
     */
    public function getCurrentFirstDay(){
        return  date('Y-m-01', strtotime(date("Y-m-d")));
    }

    /**
     * 获取上个月的月份
     */
    public function getLastMonth(){
        echo date('Y_m', strtotime('-1 month'));
    }


    /** 获取下个月那些员工在上班
     * @param $type 1为轮休 2为倒班
     */
    public function getUserWork($type){
       $list = DB::table('user') -> where('duty_type','=',$type) -> get() -> toArray();
       $date = date('Y-m-d',time());
       $last_month_start = strtotime($this -> getNextMonthFirstDay($date));
       $last_month_end = strtotime($this  -> getNextMonthLastDay($date));
       $return_arr = [];
       if(count($list) > 0){
           foreach($list as $value){
               $return_arr[$value -> user_id] = [];
               for($i = $last_month_start;$i<=$last_month_end;$i = $i+86400){
                   if(!$value -> leve_time){
                       $return_arr[$value -> user_id][] = date('Y-m-d',$i);
                   }else {
                       if($value -> leve_time < $i){
                           $return_arr[$value -> user_id][] = null;
                       } else {
                           $return_arr[$value -> user_id][] = date('Y-m-d',$i);
                       }
                   }
               }
           }
           //删除已经离职的人
           foreach($return_arr as $k => $value){
               if(!$this -> array_null($value)){
                   unset($return_arr[$k]);
               }
           }
       }
       return $return_arr;
    }

    /** 判断整个数组是否为空
     * @param $arr
     * @return bool
     */
    public function  array_null($arr){
        $flag = false;
       foreach($arr as $value){
           if($value){
               $flag = true;
               break;
           }
       }
       return $flag;
    }




    /**
     * 获取下个月对应的的日期和星期几
     */
    public function getMonthWeek($is_next_month = 1){
       if($is_next_month == 1){
           $start_time = date('Y-m-1',strtotime('next month'));
           $end_time = date('Y-m-d',strtotime(date('Y-m-1',strtotime('next month')).'+1 month -1 day'));
       } else {
           $start_time = date('Y-m-01', strtotime(date("Y-m-d")));
           $end_time =  date('Y-m-d', strtotime("$start_time +1 month -1 day"));

       }
        $return_arr = [];
        for($i = strtotime($start_time); $i <= strtotime($end_time);$i+=86400){
            $week_str = '';
            switch (date('w',$i)){
                case 0 :
                    $week_str = '星期天';
                    break;
                case 1 :
                    $week_str = '星期一';
                    break;
                case 2 :
                    $week_str = '星期二';
                    break;
                case 3 :
                    $week_str = '星期三';
                    break;
                case 4 :
                    $week_str = '星期四';
                    break;
                case 5 :
                    $week_str = '星期五';
                    break;
                case 6 :
                    $week_str = '星期六';
                    break;

            }
            $date = date('m-d',$i);
           array_push($return_arr,['day' => $date,'week' => $week_str]);
        }
        return $return_arr;
    }


    /** 删除表
     * @param $table_name
     * @return \Illuminate\Database\Schema\Builder
     */
    public function dropTable($table_name){
        return Schema::dropIfExists($table_name);
    }


    /** 获取新增员工的时候,本月要上的班
     * @return array
     */
    public function addUserWork(){
        $start_time = $this -> getCurrentFirstDay();
        $end_time = $this -> getCurrentLastDay();
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $now_time = strtotime(date('Y-m-d',time()));
        $total_day =  date('t',time());
        $return_arr = [];
        for ($i= $start;$i <= $end;$i += 86400){
            if($i < $now_time){
                $return_arr[] = '---';
            }
        }
        $check_arr = ["T","T","T","T","T","休","休","T","T","T","T","T","休","休","T","T","T","T","T","休","休","T","T","T","T","T","休","休","T","T","T","T","T","休","休"];
        $arr = array_merge($return_arr,$check_arr);
        return array_slice($arr,0,$total_day);
    }



    public function getLastDay(){
        $start_time =  strtotime(date('Y-m-01', strtotime('-1 month')));
        $end_time = strtotime(date('Y-m-t', strtotime('-1 month')));
        $return_arr = [];
        for($i = $start_time;$i<= $end_time;$i+=86400){
            $return_arr[] = date('m-d',$i);
        }
        return $return_arr;
    }






}