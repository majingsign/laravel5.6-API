<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 16:26
 */

namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shift extends Model
{
    protected  $table = 'shift_last_month_day';

    public function getList(){
     return DB::table('user')
           -> leftJoin('shift_last_month_day','user.user_id','=','shift_last_month_day.user_id')
           -> where('duty_type','=',1)
           -> where('is_del','=',0)
          -> select('user.user_name','shift_last_month_day.last_month_dat_type','user.user_id','shift_last_month_day.id')
           -> paginate(10);
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

}