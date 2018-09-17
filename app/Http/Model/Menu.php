<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 14:24
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class Menu {

    protected $table = 'system_menu';

    /**
     * 全部的菜单
     */
    public function menuList () {
        $list = DB::table($this->table)->select(['id','name','mm','mc','ma','tip','pid'])->paginate(30);
        if(empty($list)){
            return false;
        }else{
            return $list;
        }
    }

    /**
     * 递归分类
     * @param $arr
     * @return array
     */
    private function treeIteor($arr){
        $refer = array();$tree = array();
        foreach($arr as $k => $v){
            $refer[$v->id] = & $arr[$k]; //创建主键的数组引用
        }
        foreach($arr as $k => $v){
            $pid = $v->pid;  //获取当前分类的父级id
            if($pid == 0){
                $tree[] = & $arr[$k];  //顶级栏目
            }else{
                if(isset($refer[$pid])){
                    $refer[$pid]->submenu[] = & $arr[$k]; //如果存在父级栏目，则添加进父级栏目的子栏目数组中
                }
            }
        }
        return $tree;
    }

    /**
     * 全部的菜单  选择使用
     * @param int $flg
     * @return array|bool
     */
    public function menuLists ($flg = 0) {
        $list = DB::table($this->table)->select(['id','name','mm','mc','ma','tip','pid'])->get()->toArray();
        if(empty($list)){
            return false;
        }else{
            if($flg == 1){
                return $list;
            }else{
                return $this->treeIteor($list);
            }
        }
    }
    /**
     * 新增菜单
     * @param $data
     */
    public function menuAdd ($data){
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * 删除菜单
     * @param $menu_id
     */
    public function menuDel($menu_id){
        return DB::table($this->table)->where(['id'=>$menu_id])->delete();
    }

    /**
     * 修改菜单
     * @param $menu_id
     * @param $data
     */
    public function menuEdit ($menu_id,$data){
        return DB::table($this->table)->where(['id'=>$menu_id])->update($data);
    }

    /**
     * 根据菜单名字查询
     * @param $menuname
     */
    public function findMenuName($menuname) {
        return DB::table($this->table)->select(['id','name','mm','mc','ma','tip','pid'])->where(['name'=>$menuname])->first();
    }

    /**
     * 根据id查询
     * @param $menuid
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findMenuId($menuid) {
        return DB::table($this->table)->select(['id','name','mm','mc','ma','tip','pid'])->where(['id'=>$menuid])->first();
    }


    /**
     * 根据id查询所有的菜单
     * @param $data  在范围内的值
     */
    public function getMenuids ($data,$flg = 0) {
        if(gettype($data) == 'string'){
            $data = explode(',',$data);
        }
        $list = DB::table($this->table)->select(['id','name','mm','mc','ma','tip','pid'])->whereIn('id',$data)->get()->toArray();
        if(empty($list)){
            return false;
        }else{
            if($flg == 1){
                return $list;
            }else{
                return $this->treeIteor($list);
            }
        }
    }
}