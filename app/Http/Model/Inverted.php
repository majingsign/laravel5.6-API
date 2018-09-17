<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10
 * Time: 11:18
 */

namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Inverted extends Model
{
    protected  $table = 'inverted_last_month_day';

    public function getList(){
        $date = date('Y-m');
        $user_list = DB::table('user')
            -> where('duty_type','=',2)
            -> where('is_del','=',0)
            -> paginate(10);
        $shift_last_list = DB::table('inverted_last_month_day')
            -> where('year_month','=',$date)
            -> get()
            -> toArray();
        $shift_arr = [];
        if(count($shift_last_list) > 0){
            foreach($shift_last_list as $value){
                $shift_arr[$value -> user_id]['type'] = $value -> last_month_dat_type;
                $shift_arr[$value -> user_id]['id'] = $value -> id;
            }
        }

        if(count($user_list) > 0){
            foreach ($user_list as &$user){
                if(array_key_exists($user -> user_id,$shift_arr)) {
                    $user -> last_month_dat_type = $shift_arr[$user -> user_id]['type'];
                    $user -> id = $shift_arr[$user -> user_id]['id'];
                } else {
                    $user -> last_month_dat_type = 0;
                    $user -> id = 0;
                }
            }
        }
        return $user_list;
    }



    public function checkRst($change_id,$type,$user_id){
        if(!$change_id){
            $data = [
                'last_month_dat_type' => $type,
                'user_id' => $user_id,
                'year_month' => date('Y-m',time())
            ];
            return   DB::table($this -> table) -> insert($data);
        } else {
            return Db::table($this -> table) -> where(['id' => $change_id]) -> update(['last_month_dat_type' => $type]);
        }
    }



    public function checkGenerateUser($user_list){
        $time = date('Y-m',time());
        $list = Db::table('inverted_last_month_day')
            -> leftJoin('user','user.user_id','=','inverted_last_month_day.user_id')
            -> select('user.user_name','user.user_id','user.leve_time')
            -> where(['year_month' => $time])
            -> get()
            -> toArray();
        if(count($list) > 0){
            foreach ($list as $value){
                unset($user_list[$value -> user_id]);
            }

        }
        $return_arr = ['code' => 1, 'message' => '可修改'];
        if(count($user_list) > 0){
            $user_work = new \App\Logic\UserWork();
            $date = date('Y-m-d',time());
            $last_next_time = $user_work -> getNextMonthLastDay($date);
            foreach($user_list as $item){
                //判断最后一天是否已离职
                if(!$item -> leve_time || $item -> leve_time > strtotime($last_next_time)){
                    $return_arr = ['code' => 0,'message' => '用户名为'.$item -> user_name.'的员工,在本月最后一天的上班数据未设置,请前去修改'];
                }
            }
        }
        return $return_arr;
    }


}