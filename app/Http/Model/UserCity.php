<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 15:53
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class UserCity {

    protected $table = 'user_city';

    /**
     *  新增用户城市
     */
    public function addUserCity ($data) {
        return DB::table($this->table)->insert($data);
    }

    /**
     * 根据员工的id和城市的id查询，城市是否存在
     * @param $user_id
     * @param $cityid
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */

    public function findUserCityIdFirst($user_id,$cityid) {
        return DB::table($this->table)->where(['user_id'=>$user_id,'city_id'=>$cityid])->first();
    }

    /**
     * 根据员工的id查询城市
     */
    public function findUserCityId($user_id) {
        return DB::table($this->table)->where(['user_id'=>$user_id])->get();
    }


    /**
     * 根据id删除用户城市
     */
    public function delUserCity ($id){
        return DB::table($this->table)->where(['id'=>$id])->delete();
    }

    /**
     * 根据城市id删除用户城市
     */
    public function delUserCityId ($cityid){
        return DB::table($this->table)->where(['city_id'=>$cityid])->delete();
    }


    /**
     * 根据员工id删除关联城市
     * @param $userid
     * @return int
     */
    public function delUserid ($userid){
        return DB::table($this->table)->where(['user_id'=>$userid])->delete();
    }



}