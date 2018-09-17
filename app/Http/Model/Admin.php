<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 17:31
 */
namespace App\Http\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 管理Model
 * Class Admin
 */
class Admin {

    protected $table = 'admin';

    /**
     * 管理员登陆
     */
    public function login($username){
       return DB::table($this->table)->select(['id','admin_name','admin_password','depart_id'])->where(['admin_name'=>$username])->first();
    }

    /**
     *  管理员总数
     * @return int
     */
    public function adminSum(){
        return DB::table($this->table)->count();
    }
    /**
     * 查看全部管理员列表
     * @return \Illuminate\Support\Collection
     */
    public function adminList ($com_id = 0,$key = '') {
        $query = DB::table($this->table)->select(['id','admin_name','depart_id']);
        if(!empty($key)){
            $query->orWhere('admin_name','like','%'.$key.'%');
        }
        if($com_id != 0){
            //查询部门
            $departids = array();
            $departlist = DB::table('department')->select('id')->where(['com_id'=>$com_id])->get()->toArray();
            if($departlist){
                foreach ($departlist as $v){
                    $departids [] = $v->id;
                }
                $query->whereIn('depart_id',$departids);
            }
        }
        $list =  $query->orderBy('id','desc')->paginate();
        return $list;
    }

    //查询全部的部门负责人
    public function adminDepartName(){
        return DB::table('admin')->select(['admin.id','admin_name','act_list'])->leftJoin('department','admin.depart_id','=','department.id')->orderBy('id','desc')
            ->get()->toArray();
    }

    /**
     * 根据管理员id查询管理员姓名
     * @param $adminid
     */
    public function findAdminId($adminid){
        return DB::table($this->table)->where(['id'=>$adminid])->value('admin_name');
    }

    /**
     * 修改管理员密码
     * @param $id  id
     * @param $newpassword 新密码
     * @return int
     */
    public function adminEditPwd ($id,$newpassword) {
        return DB::table($this->table)->where(['id'=>$id])->update(['admin_password'=>$newpassword]);
    }

    /**
     * 根据管理员姓名查询名字是否重复
     * @param $name
     */
    public function adminName ($name){
        return DB::table($this->table)->where(['admin_name'=>$name])->first();
    }

    /**
     * 删除管理员
     * @param $adminid
     */
    public function adminDel ($adminid) {
        return DB::table($this->table)->where(['id'=>$adminid])->delete();
    }

    /**
     * 添加管理员
     * @param $data
     */
    public function adminAdd ($data) {
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * 部门选择使用
     */
    public function adminLists (){
        return DB::table($this->table)->select(['id','admin_name'])->orderBy('id','desc')->get()->toArray();
    }

    /**
     *  根据管理员id更新部门id
     * @param $adminid
     * @param $departid
     * @return int
     */
    public function adminEditDepartId($adminid,$departid){
        return DB::table($this->table)->where(['id'=>$adminid])->update(['depart_id'=>$departid]);
    }

    /**
     * 根据部门的id删除管理员
     * @param $companyid
     */
    public function delAdminDepartId($departid) {
        return DB::table($this->table)->where(['depart_id'=>$departid])->delete();
    }

}