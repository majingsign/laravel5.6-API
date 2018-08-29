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
       return DB::table($this->table)->select(['id','admin_name','admin_password'])->where(['admin_name'=>$username])->first();
    }

    /**
     * 查看全部管理员列表
     * @return \Illuminate\Support\Collection
     */
    public function adminList () {
        return DB::table($this->table)->select(['id','admin_name'])->orderBy('id','desc')->paginate();
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
        return DB::table($this->table)->insert($data);
    }


}