<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 13:32
 */

namespace App\Http\Model;


use function foo\func;
use Illuminate\Support\Facades\DB;

class Member {

    protected  $table = 'user';


    /**
     * openid登陆
     * @param $openid
     * @return bool|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function openidLogin($openid) {
        if($openid){
           return DB::table($this->table)->select(['user_id','user_name','openid'])->where(['openid'=>$openid])->first();
        }else{
            return false;
        }
    }

    /**
     * 登陆姓名和密码
     * @param $username
     */
    public function loginUser($username){
        return DB::table($this->table)->select(['user_id','leve_time','password','duty_type','user_name','input_time','is_del','depar_id','com_id'])->where(['user_name'=>$username,'is_del'=>0])->first();
    }

    /**
     * 全部员工
     * @param string $key 搜索关键词
     * @param string $userstatus  值班状态
     * @param int $departid  部门id
     * @param int $com_id    公司id
     * @return bool|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function memberList ($key = '',$userstatus = '0',$departid = 0,$com_id = 0) {
        if($departid == 0){
            $query = DB::table($this->table)->select('user_id','leve_time','duty_type','user_name','input_time','is_del','depar_id','com_id');
        }else{
            if(!empty($com_id) || $com_id != 0){
                $query = DB::table($this->table)->select(['user_id','leve_time','duty_type','user_name','input_time','is_del','depar_id','com_id'])->where(['com_id'=>$com_id]);
            }else{
                $query = DB::table($this->table)->select(['user_id','leve_time','duty_type','user_name','input_time','is_del','depar_id','com_id'])->where(['depar_id'=>$departid]);
            }
        }
        if(!empty($key) && !empty($userstatus)){
            if($userstatus == 1){
                //在职员工
                $query->where('user_name','like','%'.$key.'%')->where(['duty_type'=>1]);
                //离职员工
            }else if($userstatus == 2){
                $query->where('user_name','like','%'.$key.'%')->where(['duty_type'=>2]);
            }
        }
        if(!empty($key) || !empty($userstatus)){
            if(!empty($key)){
                $query->where('user_name','like','%'.$key.'%');
            }
            if(!empty($userstatus)){
                if($userstatus == 1){
                    $query->where(['duty_type'=>1]);//轮休员工
                }else if($userstatus == 2){
                    $query->where(['duty_type'=>2]); //倒班员工
                }
            }
        }
        $userlist = $query->orderBy('user_id','asc')->orderBy('is_del','asc')->paginate();
        if(!empty($userlist)){
            $userid_arr = array();
            $departid_arr = array();
            foreach ($userlist as $key => $value) {
                $userid_arr   [] = $value->user_id;
                $departid_arr [] = $value->depar_id;
            }
            $departList = DB::table('department')->select('id','name')->whereIn('id',$departid_arr)->get()->toArray();
            $cityList   = DB::table('user_city')->select(['city.name as city_name','user_city.user_id'])->leftJoin('city','city.id','=','user_city.city_id')->whereIn('user_id',$userid_arr)->get();
            $qingjiaList= DB::table('qingjia')->select(['is_pass','userid'])->whereIn('userid',$userid_arr)->get();
            foreach ($userlist as $key => $value) {
                foreach ($departList as $val) {
                    if($value->depar_id == $val->id){
                        $value->depart = $val->name;
                    }
                }
                foreach ($cityList as $val) {
                    if($value->user_id == $val->user_id){
                        $value->city[]['city_name'] = $val->city_name;
                    }
                }
               foreach ($qingjiaList as $v){
                   if($value->user_id == $v->userid){
                       $value->is_pass = $v->is_pass;
                   }
               }
            }
        }else{
            return false;
        }
        return $userlist;
    }

    /**
     * 离职员工  软删除
     * @param $user_id
     */
    public function memberDel($user_id){
        return DB::table($this->table)->where(['user_id'=>$user_id])->delete();
    }

    /**
     * 新增员工
     * @param $data
     */
    public function memberAdd($data){
        return DB::table($this->table)->insert($data);
    }

    /**
     * 修改密码
     * @param $userid
     * @param $data
     */
    public function editPassword($userid,$data){
        return DB::table($this->table)->where(['user_id'=>$userid])->update($data);
    }

    /**
     * 新增员工返回id
     */
    public function memberAddId($data){
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * 修改员工
     * @param $user_id
     * @param $data
     */
    public function memberEdit($user_id,$data) {
       return DB::table($this->table)->where(['user_id'=>$user_id])->update($data);
    }

    /**
     * 根据用户id查找用户信息
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function memberFindId($user_id) {
        return DB::table($this->table)->select(['user_id','duty_type','user_name','input_time','is_del','leve_time','depar_id','com_id'])->where(['user_id'=>$user_id])->first();
    }


    /**
     * 根据部门id删除员工
     * @param $departid
     */
    public function delMemberDepartId($departid){
        return DB::table($this->table)->where(['depar_id'=>$departid])->delete();
    }

    /**
     * 统计用户
     * @param $status
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function memberSum($status) {
        if($status == 1){
            $sum = DB::table($this->table)->where(['duty_type'=>1])->count();  //轮休总数
        }elseif($status == 2){
            $sum = DB::table($this->table)->where(['duty_type'=>2])->count();  //倒班总数
        }elseif($status == 3){
            $sum = DB::table($this->table)->where(['is_del'=>1])->count();  //删除的人数
        }elseif($status == 4){
            $sum = DB::table($this->table)->where(['is_del'=>0])->count(); //在职人数
        }
        return $sum;
    }


    public function getUserListByIdList($type = 1){
        if($type == 'all'){
            $list = DB::table($this -> table) ->  get() -> toArray();
        } else {
            $list = DB::table($this -> table) -> where(['duty_type' => $type]) -> get() -> toArray();
        }

      $return_arr = [];
      foreach($list as $value){
          $return_arr[$value -> user_id] = $value;
      }
      return $return_arr;
    }


    public function getQuitStr($type = 1){
       $last_month_time = strtotime(date('Y-m-1',strtotime('+1 month')));
       $check_list = DB::table($this -> table)
                   -> where('leve_time','>=',$last_month_time)
                   -> where('duty_type','=',$type)
                   -> get()
                   -> toArray();
        $return_arr = [];
       if(count($check_list) > 0){
           foreach($check_list as $k =>  $value){
               $return_arr[$k]['user_name'] = $value -> user_name;
               $return_arr[$k]['leve_time'] = date('Y-m-d',$value -> leve_time);

           }
       }
       return $return_arr;
    }

}