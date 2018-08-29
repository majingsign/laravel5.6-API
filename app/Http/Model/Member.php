<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 13:32
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class Member {

    protected  $table = 'user';

    /**
     * 全部员工
     * @param string $key 搜索关键词
     * @param string $userstatus  用户状态
     * @return bool|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function memberList ($key = '',$userstatus = '0') {
        $query = DB::table($this->table)->select(['user_id','leve_time','duty_type','user_name','input_time','is_del']);
        if(!empty($key) && !empty($userstatus)){
            if($userstatus == 1){
                //在职员工
                $query->where('user_name','like','%'.$key.'%')->where(['is_del'=>0]);
                //离职员工
            }else if($userstatus == 2){
                $query->where('user_name','like','%'.$key.'%')->where(['is_del'=>1]);
            }
        }
        if(!empty($key) || !empty($userstatus)){
            if(!empty($key)){
                $query->where('user_name','like','%'.$key.'%');
            }
            if(!empty($userstatus)){
                if($userstatus == 1){
                    $query->where(['is_del'=>0]);//在职员工
                }else if($userstatus == 2){
                    $query->where(['is_del'=>1]); //离职员工
                }
            }
        }
        $userlist = $query->orderBy('is_del','asc')->paginate();
        if(!empty($userlist)){
            foreach ($userlist as $key => $value) {
                $value->city = DB::table('user_city')->select(['city_name'])->where(['user_id'=>$value->user_id])->get();
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
        return DB::table($this->table)->where(['user_id'=>$user_id])->update(['is_del'=>1]);
    }

    /**
     * 新增员工
     * @param $data
     */
    public function memberAdd($data){
        return DB::table($this->table)->insert($data);
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
        return DB::table($this->table)->select(['user_id','duty_type','user_name','input_time','is_del','leve_time'])->where(['user_id'=>$user_id])->first();
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

}