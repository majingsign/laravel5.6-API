<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 9:51
 */

namespace App\Http\Model;

use Illuminate\Support\Facades\DB;

/**
 * 排班分类
 * Class Duty
 * @package App\Http\Model
 */
class Duty {

    protected $table = 'type';

    /**
     * 查看全部排班类型
     */
    public function dutyList () {
        return DB::table($this->table)->select(['id','name','duty_type','on_time'])->paginate(20);
    }

    /**
     * 新增排班类型
     * @param $data 新增的数据
     */
    public function dutyAdd ($data) {
        return DB::table($this->table)->insert($data);
    }

    /**
     * 根据dutyid查询数据
     * @param $dutyid
     */
    public function findDutyId($dutyid){
        return DB::table($this->table)->select(['id','name','duty_type','on_time'])->where(['id'=>$dutyid])->first();
    }

    /**
     * 根据名称查询
     * @param $dutyname
     */
    public function findDutyName ($dutyname){
        return DB::table($this->table)->where(['name'=>$dutyname])->first();
    }

    /**
     * 修改排班类型
     * @param $duty_id
     * @param $data  更新的数据
     */
    public function dutyEdit ($duty_id,$data) {
        return DB::table($this->table)->where(['id'=>$duty_id])->update($data);
    }

    /**
     * 删除排班类型
     * @param $duty_id
     */
    public function dutyDel ($duty_id) {
        return DB::table($this->table)->where(['id'=>$duty_id])->delete();
    }
}