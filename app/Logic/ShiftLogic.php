<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 16:30
 */

namespace app\Logic;
use App\Http\Model\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Logic\UserWork;
/**  轮班的逻辑
 * Class ShiftLogic
 * @package app\Logic
 */
class ShiftLogic
{
    //周末上班的人数,需要上班的人数
    private static  $weekend_work_num = 0;

    public function __construct()
    {
        $config = config('public.shfit_probability');
        $user_num = DB::table('user')-> where(['duty_type' => 1]) -> count();
        self::$weekend_work_num = ceil($user_num * $config);
    }


    /**
     * 判断一个月的第几天是周末
     */
  public function getWeekNum(){
      $start_time = date('Y-m-1',strtotime('next month'));
      $end_time = date('Y-m-d',strtotime(date('Y-m-1',strtotime('next month')).'+1 month -1 day'));
      $return_arr = [];
      for($i = strtotime($start_time); $i <= strtotime($end_time);$i+=86400){
          if(date('w',$i) == 6 || date('w',$i) == 0){
              $return_arr[] = intval(date('d',$i) -1 );
          }
      }
      return $return_arr;
  }






    /**
     * 获取这个月上班最后一天的上班内容
     */
    private function _getLastMonthDay(){
       $date = date('Y-m',time());
        $list = DB::table('shift_last_month_day','=',$date) -> get() -> toArray();
        $return_arr = [];
        foreach($list as $value){
            $return_arr[$value -> user_id] = $value -> last_month_dat_type;
        }
        return $return_arr;
    }

    private function nextWorkCheck($type){
        $a = '1,2,3,4,5,6,7,1,2,3,4,5,6,7,1,2,3,4,5,6,7,1,2,3,4,5,6,7,1,2,3,4,5,6,7,1,2,3,4,5,6,7';
        $str = substr($a,$type*2);
         return  explode(',',$str);
    }



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
              if(array_key_exists($k,$user_last_work)){
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
      }
      return $return_arr;
    }


    /** 最好的上班方式
     * @param $city_arr      城市的id数组
     * @param $min_city_arr  最小值
     * @param $user_city_arr 用户城市信息
     * @param $normal_user_list 员工正常上班的信息
     */
    public function checkBestType($city_arr,$min_city_arr,$user_city_arr,$normal_user_list){
        //>>每天都有那些人在上班
        $month_city_arr = [];
        $month_day = [];
        $month_user_arr = [];
        for($i=0;$i<=31;++$i){
            $month_city_arr[$i] = [];
            $month_day[$i] = [];
            $month_user_arr[$i] = [];
        }
        foreach($normal_user_list as $uid =>  $value){
          foreach($value as $k => $item){
              if($item != 6 &&$item != 7){
                  $month_day[$k][$uid] = $user_city_arr[$uid];
                  $month_user_arr[$k][] = $uid;
                  foreach($user_city_arr[$uid] as $city){
                      $month_city_arr[$k][] = $city;

                  }

              }
          }
        }
        //判断每个城市缺少的人
        $luck_arr = [];
        $check_need_change = 0;
       if(count($month_city_arr)> 0 ){
           foreach($month_city_arr as $k =>  &$value){
               //>> 每天每个人城市都有人
               $unique_arr = array_unique($value);
               $keys = array_keys($min_city_arr);
               if($unique_arr != $keys){
                 $diff_arr = array_diff($unique_arr,$keys);
                 foreach ($diff_arr as $diff){
                     array_push( $luck_arr[$k],$diff);
                     $check_need_change = 1;
                 }
               }
                //>> 满足每个城市的最小值
               $check = array_count_values($value);
              foreach($check as $city_id => $num){
                  if($min_city_arr[$city_id] > $num){
                      //该天数差多个城市
                    for($i = 1;$i <= intval($min_city_arr[$city_id] - $num);++$i){
                        array_push( $luck_arr[$k],$city_id);
                        $check_need_change = 1;
                    }
                  }
              }
           }
        }
        //判断是否需要去修改在正常上班逻辑
          if(!$check_need_change){
              //判断可以T出去的员工
              $out_arr = [];
              foreach($month_day as $day => $citys){
                  $out_arr[$day]  =  $this -> getOutUser($citys);
              }
              //todo :　替换掉可以替换掉的内容信息
          }



          //判断周末是否有超过固定的人数数量
          $week_arr = $this -> getWeekNum();



          foreach($week_arr as $week){
              if(array_key_exists($week,$month_user_arr)){
                  if(count($month_user_arr[$week]) < self::$weekend_work_num){
                      //todo :　周末这一天少一个人
                      //todo :　替换掉可以替换掉的内容信息
                  }
              }
          }

          return $normal_user_list;
    }


    private function  getOutUser($arr){
        $return_arr = [];
        $max_key = $this -> getMaxArrKey($arr);
        foreach($arr as $k => $value){
            for($i= 0 ; $i <= $max_key;++ $i){
                if(array_key_exists($i,$arr)){
                   sort($value);
                    sort($arr[$i]);
                    $c = array_values(array_intersect($value,$arr[$i]));
                    if( $c ==  $value){
                        $return_arr[] = $k;
                    } elseif ($c ==  $arr[$i]){

                        $return_arr[] = $i;
                    }
                }
            }
        }
     return array_unique($return_arr);

    }


    /** 获取最大的键值
     * @param $arr
     * @return int|string
     */
    private  function getMaxArrKey($arr){
        $max_key = 0;
        foreach($arr as $k => $value){
          if($k >= $max_key){
              $max_key = $k;
          }

        }
        return $max_key;
    }

    /** 获取轮班中的的表名
     * @return string
     */
    public function getTableName($date){
        $table_name = "user_scheduling";
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


    /** 获取本月和下月的轮休表
     */
    public function getUserScheduling(){
        $user_work = new UserWork();
        $next_time = $user_work -> getNextMonthLastDay(date('Y-m-d'));
        $now_time  = $user_work -> getCurrentLastDay();
        return [$this  -> getTableName(date('Y_m',strtotime($now_time))),$this  -> getTableName(date('Y_m',strtotime($next_time)))];
    }


    /** 判断本月最后一天应该上什么班
     * @param $arr    排班的数组
     * @param $count  下个月应该上班的天数
     * @return int
     */
    function getShiftLastDay($arr,$count){
        $arr = array_reverse($arr);
        $xiu_first = array_keys($arr,"休");
        //已经离职
        if(count($arr) < $count){
            return 0;
        }
        $num = 0;
        switch ($xiu_first[0]){
            case 0:
                if($xiu_first[0] == $xiu_first[1] - 1){
                    //第二天休息
                    $num = 7;
                } else {
                    //第一天休息
                    $num = 6;
                }
                break;
            case 1 :
                //第一天上班
                $num = 1;
                break;
            case 2 :
                //第二天上班
                $num = 2;
                break;
            case 3 :
                //第三天上班
                $num = 2;
                break;
            case 4 :
                //第四天上班
                $num = 4;
                break;
            case 5 :
                //第五天上班
                $num = 5;
                break;
            case 6 :
                //第一天休息
                $num = 6;
                break;
        }
        return $num;
    }



}