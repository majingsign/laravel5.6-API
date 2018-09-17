<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 14:16
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class City {

    protected $table = 'city';

    /**
     * 城市列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function cityList () {
        return DB::table($this->table)->select(['id','name','pid','min_num'])->orderBy('id','desc')->paginate(35);
    }

    /**
     * 根据城市名称查询城市名称是否存在
     * @param $name
     */
    public function cityName ($name){
        return DB::table($this->table)->where(['name'=>$name])->first();
    }

    /**
     * 查询省份名称
     * @param $cityid
     */
    public function findCityId($cityid) {
        return DB::table($this->table)->select(['id','name','min_num'])->where(['id'=>$cityid])->first();
    }

    /**
     * 查询省份名称
     * @param $cityid
     */
    public function findCityNameId($cityid) {
        return DB::table($this->table)->where(['id'=>$cityid])->value('name');
    }

    /**
     * 新增城市
     * @param $data
     * @return bool
     */
    public function cityAdd ($data) {
        return DB::table($this->table)->insert($data);
    }

    /**
     * 删除城市
     * @param $id
     * @return int
     */
    public function cityDel ($id) {
        return DB::table($this->table)->where(['id'=>$id])->delete();
    }

    /**
     * 根据城市id,返回数据
     * @param $id
     */
    public function cityEdit($id) {
        return DB::table($this->table)->select(['id','name','min_num'])->where(['id'=>$id])->first();
    }

    /**
     * 保存城市
     * @param $id
     * @param $data
     * @return int
     */
    public function citySave ($id,$data) {
        return DB::table($this->table)->where(['id'=>$id])->update($data);
    }

    /**
     * 省份总数
     * @return int
     */
    public function citySum () {
        return DB::table($this->table)->count();
    }

    public function getCityByIdList(){
        $list = DB::table($this -> table) -> get() -> toArray();
        $return_arr = [];
        if(count($list) > 0){
            foreach($list as $value){
                $return_arr[$value -> id] = $value;
            }
        }
        return $return_arr;
    }

    /** 获取所有的城市id
     * @return array
     */
    public function getCityIdArr(){
        $list = DB::table($this -> table) -> select('id','min_num') -> orderBy('id','asc') -> get() -> toArray();
        $city_arr = [];
        $min_arr = [];
       if(count($list) > 0){
           foreach($list as $value){
               $city_arr[] = $value -> id;
               $min_arr[$value -> id] = $value -> min_num;
           }
       }
       return [$city_arr,$min_arr];
    }


}