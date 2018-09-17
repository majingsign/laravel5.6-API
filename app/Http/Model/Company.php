<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 15:06
 */

namespace App\Http\Model;
use Illuminate\Support\Facades\DB;

/**
 * 公司
 * Class Company
 * @package App\Http\Model
 */
class Company {

    private $table = 'company';


    /**
     * 新增公司
     * @param $data
     */
    public function companyAdd($data) {
        return DB::table($this->table)->insert($data);
    }

    /**
     * 查询全部的公司
     * @return array
     */
    public function companyList () {
        return DB::table($this->table)->select(['id','name','desc','admin_id','admin_name'])->paginate();
    }

    /**
     * 作为选项使用
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function companyLists () {
        return DB::table($this->table)->select(['id','name'])->get()->toArray();
    }

    /**
     * 作为选项使用
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function companyListsId ($com_id) {
        return DB::table($this->table)->select(['id','name'])->where(['id'=>$com_id])->get()->toArray();
    }

    /**
     * 修改公司信息
     * @param $companyid
     * @param $data
     * @return int
     */
    public function companyEdit ($companyid,$data){
        return DB::table($this->table)->where(['id'=>$companyid])->update($data);
    }

    /**
     * 删除公司
     * @param $companyid
     * @return int
     */
    public function companyDel ($companyid) {
        return DB::table($this->table)->where(['id'=>$companyid])->delete();
    }

    /**
     * 根据公司名字查询
     * @param $companyName
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findCompanyName($companyName){
        return DB::table($this->table)->select(['id','name','desc','admin_id','admin_name'])->where('name','like',"%".$companyName."%")->first();
    }

    /**
     * 根据公司的id查询
     * @param $companyId
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findCompanyId($companyId) {
        return DB::table($this->table)->select(['id','name','desc','admin_id','admin_name'])->where(['id'=>$companyId])->first();
    }

    /**
     * 判断是否是公司负责人
     * @param $com_id
     * @param $admin_id
     */
    public function findCompanyAdmin($com_id,$admin_id){
        return DB::table($this->table)->select(['id'])->where(['admin_id'=>$admin_id,'id'=>$com_id])->first();
    }

    /**
     * 公司总数
     * @return int
     */
    public function companySum(){
        return DB::table($this->table)->count();
    }

}