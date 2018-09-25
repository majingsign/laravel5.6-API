<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 11:09
 */

namespace app\Logic;



use Illuminate\Support\Facades\DB;

class RecordLogic
{
    public function getList($scheduling_list,$inverted_name_list){
        return  $this -> getNormalClass($scheduling_list,$inverted_name_list);

    }


    /** 获取用户正常上班的上班时间
     * @param $scheduling_list
     * @param $inverted_name_list
     */
    private function  getNormalClass($scheduling_list,$inverted_name_list){
        $scheduling_list = arrayKeyValue($scheduling_list,'user_id','scheduling');
        $inverted_list = arrayKeyValue($inverted_name_list,'user_id','scheduling');
        $scheduling_arr_list =  $this -> getNormalList($scheduling_list);
        $inverted_arr_list =  $this -> getNormalList($inverted_list);
       //合并两个班次的数据
        $return_arr = $scheduling_arr_list;
        foreach($inverted_arr_list as $k => $value){
            $return_arr[$k] = $value;
        }
        ksort($return_arr);
        return $return_arr;


    }



    public function getNormalList($list){
        $type_list = $this -> _getTypeList();
        $last_month = date('Y-m',strtotime("-1 month"));
        $return_arr = [];
        foreach($list as $user_id =>  $value){
            $value_arr = json_decode($value);
            $return_arr[$user_id] = [];
           foreach($value_arr as  $day => $item){
               if(intval($day+1) < 10){
                   $day_time = $last_month.'-0'.intval($day+1);
               } else {
                   $day_time = $last_month.'-'.intval($day+1);
               }

               $return_arr[$user_id][$day_time] = [];
               if($item != "休" && $item != "E"){
                   $start_time = $day_time.' '.$type_list[$item][0];
                   $end_time = $day_time.' '.$type_list[$item][1];
               } elseif ($item == "休"){
                   $start_time = 0;
                   $end_time = 0;
               }  elseif ($item == "E"){
                   $start_time = $day_time.' '.$type_list[$item][0];
                   $end_time = $last_month.'-'.intval($day+2).' '.$type_list[$item][1];
               }


               $return_arr[$user_id][$day_time] = [$start_time,$end_time];

           }
        }
       return $return_arr;


    }





    /** 获取每个上班的班次的时间
     * @return array
     */
    private function _getTypeList(){
        $list = DB::table('type') -> get() -> toArray();
        $arr_list =  arrayKeyValue($list,'name','on_time');
        $return_arr = [];
        foreach($arr_list as $k => $value){
            $return_arr[$k] = explode('-',$value);
        }
        return $return_arr;


    }


    /**
     * 获取打卡的记录
     */
    public function getRecordList(){
       $start_time =  strtotime(date('Y-m-01', strtotime('-1 month')));
       $end_time = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))).' 23:59:59');
       $list = DB::table('records')
             -> whereBetween('clock_time',[$start_time,$end_time])
             -> get()
            -> toArray();
       $return_arr = [];
       foreach($list as $value){
           $return_arr[$value -> user_id][$value -> date_time][] = date('Y-m-d H:i:s',$value -> clock_time);
       }
       return $return_arr;
    }

    /** 判断每天上班的结果
     * @param $normal_user_list
     * @param $record_list
     */
    public function checkRst($normal_user_list,$record_list){
        $return_arr = [];
        foreach($normal_user_list as $user_id  => $user_list){
            $return_arr[$user_id] = [];
            foreach ($user_list as $day => $item){
               if($item == [0,0]) {$return_arr[$user_id][$day] = '正常';continue;}
                $return_arr[$user_id][$day] = '';
                $satrt_time = $item[0];
                $end_time = $item[1];
                $start_date = date('Y-m-d',strtotime($satrt_time));
                $end_date = date('Y-m-d',strtotime($end_time));
                if(array_key_exists($user_id,$record_list)){
                    //结束上班的日期
                    if(!array_key_exists($end_date,$record_list[$user_id])){
                        $return_arr[$user_id][$day] = '旷工';
                    }
                    //开始上班的日期
                    if(array_key_exists($start_date,$record_list[$user_id])){
                        $return_arr[$user_id][$day] = '正常';
                        if(strtotime($record_list[$user_id][$start_date][0]) > strtotime($satrt_time)){
                            $return_arr[$user_id][$day] = '迟到';
                        }
                           if($start_date == $end_date){
                               if(count($record_list[$user_id][$start_date]) != 2){
                                   $return_arr[$user_id][$day] = '数据异常';
                                   continue;
                               }
                               if(strtotime($record_list[$user_id][$start_date][1]) < strtotime($end_time)){
                                   $return_arr[$user_id][$day] = '早退';
                                   continue;
                               }
                           } else {
                               if(strtotime($record_list[$user_id][$end_date][0]) < strtotime($end_time)){
                                   $return_arr[$user_id][$day] = '早退';
                                   continue;
                               }
                           }


                    }else{
                        $return_arr[$user_id][$day] = '旷工';
                        continue;
                    }

                } else {
                    $return_arr[$user_id][$day] = '旷工';
                    continue;
                }

            }
        }
      return $return_arr;

    }



}