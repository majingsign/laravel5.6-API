<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 13:55
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class Depart {

    protected  $table = 'department';

    /**
     *
     * 查询全部的部门
     * @param int $departid
     * @param int $comid
     * @param string $key
     * @return bool|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function departList ($departid = 0,$comid = 0,$key = '') {
        $query =  DB::table($this->table)->select(['id','name','admin_id','create_time','desc','act_list','com_name','com_id']);
        if($departid == 0){
            if(!empty($key)) {
                $query->where(function ($query) use ($key) {
                    $query->orWhere('name', 'like', '%' . $key . '%')->orWhere('com_name', 'like', '%' . $key . '%');
                });
            }
        }else{
            if(!empty($comid) || $comid != 0){
                if(!empty($key)) {
                    $query->where(['com_id'=>$comid])->where(function ($query) use ($key) {
                        $query->orWhere('name', 'like', '%' . $key . '%')->orWhere('com_name', 'like', '%' . $key . '%');
                    });
                }
            }else{
                if(!empty($key)) {
                   $query->where(['id'=>$departid])->where(function ($query) use ($key) {
                        $query->orWhere('name', 'like', '%' . $key . '%')->orWhere('com_name', 'like', '%' . $key . '%');
                    });
                }
            }
        }
        $list = $query->paginate(20);
        if(!empty($list)){
            foreach ($list as $value) {
                if(isset($value->admin_id) && !empty($value->admin_id)){
                   $value->admin_name = DB::table('admin')->where(['id'=>$value->admin_id])->value('admin_name');
                }else{
                    $value->admin_name = '未设置';
                }
                if(isset($value->act_list) && !empty($value->act_list)){
                    $value->menu = DB::table('system_menu')->select(['name'])->whereIn('id',explode(',',$value->act_list))->get();
                }else{
                    $value->menu = '未设置';
                }
            }
            return $list;
        }else{
            return false;
        }
    }

    /**
     * 查询选择使用
     * @return array|bool
     */
    public function departLists () {
        $list =  DB::table($this->table)->select(['id','name','admin_id','create_time','desc','act_list','com_name','com_id'])->get()->toArray();
        if(!empty($list)){
            foreach ($list as $value) {
                if(isset($value->admin_id) && !empty($value->admin_id)){
                    $value->admin_name = DB::table('admin')->where(['id'=>$value->admin_id])->value('admin_name');
                }
            }
            return $list;
        }else{
            return false;
        }
    }

    /**
     * 新增部门
     * @param  $data 加入的部门数据
     */
    public function departAdd ($data){
        return DB::table($this->table)->insert($data);
    }

    /**
     * 修改部门
     * @param $departid
     * @param $data
     */
    public function departEdit($departid,$data) {
        return DB::table($this->table)->where(['id'=>$departid])->update($data);
    }

    /**
     * 删除部门
     * @param $departid
     */
    public function departDel ($departid){
        return DB::table($this->table)->where(['id'=>$departid])->delete();
    }

    /**
     * 根据公司的id查询下面全部的部门
     * @param $com_id
     */
    public function getDepartList ($com_id) {
        return DB::table($this->table)->select(['id','name'])->where(['com_id'=>$com_id])->get()->toArray();
    }

    /**
     * 根据部门id查询
     * @param $departid
     */
    public function findDepartId ($departid){
        return DB::table($this->table)->select(['id','name','admin_id','create_time','desc','act_list','com_name','com_id'])->where(['id'=>$departid])->first();
    }
    /**
         * 根据部门id查询
         * @param $departid
         */
    public function getDepartId ($departid){
        return DB::table($this->table)->select(['id','name','admin_id','create_time','desc','act_list','com_name','com_id'])->where(['id'=>$departid])->get()->toArray();
    }

    /**
     * 查询管理员的是否设置成其他部门负责人
     * @param $admin_id
     */
    public function findDepartAdminId($admin_id){
        return DB::table($this->table)->select(['admin_id','name'])->where(['admin_id'=>$admin_id])->first();
    }

    /**
     * 根据名字和公司id查询部门
     * @param $departname  部门名称
     * @param $com_id 公司的id
     */
    public function findDepartName($departname,$com_id){
        return DB::table($this->table)->select(['name'])->where(['name'=>$departname,'com_id'=>$com_id])->first();
    }

    /**
     * 根据部门id查询菜单
     * @param $departid
     */
    public function findDepartMenuList($departid) {
        return DB::table($this->table)->where(['id'=>$departid])->value('act_list');
    }

    /**
     * 总计部门总数
     * @return int
     */
    public function departaSum() {
        return DB::table($this->table)->count();
    }

    /**
     *  根据公司的id查询所有部门的id
     * @param $com_id
     * @return array
     */
    public function findDepartCompanyId($com_id){
        return DB::table($this->table)->select(['id'])->where(['com_id'=>$com_id])->get()->toArray();
    }

    /**
     * 根据公司的删除所有的部门
     * @param $companyid
     */
    public function delDepartCompanyId($companyid){
        return DB::table($this->table)->where(['com_id'=>$companyid])->delete();
    }

    /**
     * 根据管理员 删除相关的部门
     * @param $adminid
     */
    public function delAdminDepartId ($adminid) {
        return DB::table($this->table)->where(['admin_id'=>$adminid])->delete();
    }


}