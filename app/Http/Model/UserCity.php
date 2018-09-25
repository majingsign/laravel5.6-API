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

    /**
     * 删除用户关联的城市
     * @param $userid
     * @param $city 数组
     */
    public function delCityWhereIn($userid,$city) {
        return DB::table($this->table)->whereNotIn('city_id',$city)->where(['user_id'=>$userid])->delete();
    }

    /**
     * 判断生成轮班的排班前的判断
     */
    public function checkGenerateCity($city_list){
        $list = DB::table('user_city')
             -> groupBy('city_id')
             -> leftJoin('city','user_city.id','=','city.id')
             -> select(DB::Raw('count(*) as count'),'city_id','city.name')
             -> get()
             -> toArray();
        $return_arr = ['code' => 1,'message' => '可修改'];
       if(count(($list)) > 0){
           foreach($list as $value){
               if(array_key_exists($value -> city_id,$city_list)){
                   unset($city_list[$value ->city_id  ]);
               }
               if($value -> count <=1){
//                   $return_arr = ['code' => 0,'message' => '城市名字为'.$value -> name .'分配的员工数量为'.$value -> count.',小于2个人'];
                   break;
               }
           }
           if(!empty($city_list)){
             foreach($city_list as $city){
//                 $return_arr = ['code' => 0,'message' => '城市名字为'.$city-> name .'分配的员工数量为0,请重新分配'];
             }
           }
       }
       return $return_arr;
    }


    public function getUserCityArr(){
        $list = DB::table($this -> table) -> get() -> toArray();
        $return_arr = [];
        foreach($list as $value){
            $return_arr[$value -> user_id][] = $value -> city_id;
        }
        return $return_arr;
    }

    public function checkUserCity($user_list){
     $list = DB::table($this -> table) -> groupBy('user_id') -> get() -> toArray();
      if(count($list) > 0){
            foreach($list as $value){
                if(!array_key_exists($value -> user_id,$user_list)){
//                    return ['code' => 0,'message' => '请给所有的员工分配对应城市'];
                }
            }
        }
        return ['code' => 1,'message' => ''];
    }

}