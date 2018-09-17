<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10
 * Time: 17:16
 */

namespace app\Logic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Logic\UserWork;
/** 倒班的逻辑
 * Class InvertedLogic
 * @package app\Logic
 */
class InvertedLogic
{
    /** 按照正常上班, 每天每个人应该上班的逻辑
     * @param $wrok_arr
     */
    public function normalWork($wrok_arr){
        $user_last_work = $this -> _getLastMonthDay();
        $return_arr = [];
        if(count($wrok_arr) > 0){
            foreach($wrok_arr as  $k => $value){
                $return_arr[$k] = [];
                //下个月每天上班的情况
                $next_work_arr = $this -> nextWorkCheck($user_last_work[$k]);
                foreach($value as $b => $item){
                    if($item){
                        if(array_key_exists($b,$next_work_arr)){
                            array_push($return_arr[$k],$next_work_arr[$b]);
                        }
                    } else {
                        //离职的日期也新增键值
                        array_push($return_arr[$k],null);
                    }
                }

            }
        }
        return $return_arr;
    }


    private function _getLastMonthDay(){
        $date = date('Y-m',time());
        $list = DB::table('inverted_last_month_day','=',$date) -> get() -> toArray();
        $return_arr = [];
        foreach($list as $value){
            $return_arr[$value -> user_id] = $value -> last_month_dat_type;
        }
        return $return_arr;
    }

    private function nextWorkCheck($type){
        $a = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,1,2,3,4,5,6,7,8,9,10';
        $arr = explode(',',$a);
        $index = 0;
        $return_arr = [];
        foreach($arr as $k => $value){
            if($value == $type){
                $index = $k;
                break;
            }
        }
        foreach($arr as  $k => $value){
            if($k > $index){
                $return_arr[] = $value;
            }
        }
        return $return_arr;
    }



    /** 获取倒班中的的表名
     * @return string
     */
    public function getTableName($date){
        $table_name = "user_change_inverted";
        $new_table_name = $table_name . $date;
        if(!Schema::hasTable($new_table_name)){
            Schema::create($new_table_name,function ($table){
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id');
                $table->string('scheduling',255);
                $table->string('last_day',10);
                $table->integer('create_time');
            });
        }
        return $new_table_name;
    }


    /** 获取本月和下月的倒班表
     */
    public function getUserInverted(){
        $user_work = new UserWork();
        $next_time = $user_work -> getNextMonthLastDay(date('Y-m-d'));
        $now_time  = $user_work -> getCurrentLastDay();
        return [$this  -> getTableName(date('Y_m',strtotime($now_time))),$this  -> getTableName(date('Y_m',strtotime($next_time)))];
    }



    /** 判断在下个月的第一天应该上什么班
     * @param $arr    排班的数组
     * @param $count  下个月应该上班的天数
     * @return int
     */
    public function getShiftLastDay($arr,$count){
        //判断是否离职
        if(count($arr) < $count){
            return 0;
        }
        $arr = array_reverse($arr);
        $num =0 ; $str = '';
        $xiu_first = array_keys($arr,"休");
        if($xiu_first[0] == 0){
            //最后两天都在休息
            if($xiu_first[1] == 1){
                if($arr[3] == 'D1'){ $num = 1;  $str =  'c1第一天'; }
                if($arr[3] == 'C4'){ $num = 22; $str =  'd1第一天 '; }
                if($arr[3] == 'C2'){ $num = 15; $str =  'c4第一天 '; }
                if($arr[3] == 'C1'){ $num = 8;  $str =  'c2第一天'; }
            } else {
                //最后一天在休息
                if($arr[2] == 'D1'){ $num = 28; $str =  'd1后的休息第二天'; }
                if($arr[2] == 'C4'){ $num = 21; $str =  'c4休息第二天 '; }
                if($arr[2] == 'C2'){ $num = 14; $str =  'c2后的休息第二天   '; }
                if($arr[2] == 'C1'){ $num = 7;  $str =  'c1后的休息第二天 '; }
            }
        }
        $check_arr = [];
        for($i = 0;$i< $xiu_first[0];++$i){
            $check_arr[] = $arr[$i];
        }


        if($check_arr == ["E","C1","C1","C1","C1"]){ $num = 6; $str =  'c1后的休息第一天 ';};
        if($check_arr == ["C1","C1","C1","C1"]){     $num = 5; $str =  'C1班后上E班';};
        if($check_arr == ["C1","C1","C1"]){          $num = 4; $str =  '第四个C1班';};
        if($check_arr == ["C1","C1"]){               $num = 3; $str =  '第3个C1班';};
        if($check_arr == ["C1"]){                    $num = 2; $str =  '第2个C1班';};

        if($check_arr == ["E","C2","C2","C2","C2"]){ $num = 13; $str =  'c2后休息第一天';};
        if($check_arr == ["C2","C2","C2","C2"]){     $num = 12; $str =  'c2班后的E班';};
        if($check_arr == ["C2","C2","C2"]){          $num = 11; $str =  'c2班的第四个';};
        if($check_arr == ["C2","C2"]){               $num = 10; $str =  'c2班的第3个';};
        if($check_arr == ["C2"]){                    $num = 9;  $str =  'c2班的第2个';};

        if($check_arr == ["E","C4","C4","C4","C4"]){ $num = 20; $str =  'c4休息第一天';};
        if($check_arr == ["C4","C4","C4","C4"]){     $num = 19; $str =  'c4班后的E班';};
        if($check_arr == ["C4","C4","C4"]){          $num = 18; $str =  'c4班的第四个';};
        if($check_arr == ["C4","C4"]){               $num = 17; $str =  'c4班的第3个';};
        if($check_arr == ["C4"]){                    $num = 16; $str =  'c4班的第2个';};

        if($check_arr == ["E","D1","D1","D1","D1"]){ $num = 27; $str =  'd1后的休息第一天';};
        if($check_arr == ["D1","D1","D1","D1"]){     $num = 26; $str =  'd1班后的E班';};
        if($check_arr == ["D1","D1","D1"]){          $num = 25; $str =  'd1班的第四个';};
        if($check_arr == ["D1","D1"]){               $num = 24; $str =  'd1班的第3个';};
        if($check_arr == ["D1"]){                    $num = 23; $str =  'd1班的第2个';};


       return $num;
    }



}