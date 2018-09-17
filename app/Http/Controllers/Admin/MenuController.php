<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 14:24
 */

namespace App\Http\Controllers\Admin;
use App\Http\Model\Menu;
use App\Http\Model\Tips;
use Illuminate\Http\Request;

/**
 * 菜单列表
 * Class MenuController
 * @package App\Http\Controllers\Admin
 */
class MenuController extends AdminBaseController {

    /**
     * 全部菜单
     */
    public function list () {
        $menu = new Menu();
        $list = $menu->menuList();
        $lists = $menu->menuLists();
        return view('admin.menu.list',['list'=>$list,'lists'=>$lists]);
    }

    /**
     * 新增菜单
     */
    public function add () {
        return view('admin.menu.add');
    }

    /**
     * 编辑菜单
     */
    public function edit (Request $request) {
        $menuid = $request->input('menuid');
        $menu = new Menu();
        $list = $menu->findMenuId($menuid);
        $lists = $menu->menuLists();
        return view('admin.menu.edit',['list'=>$list,'lists'=>$lists]);
    }

    /**
     * 删除菜单
     * @param Request $request
     */
    public function del (Request $request) {
        $menuid = intval($request->input('id'));
        if(empty($menuid) || $menuid == '' || $menuid == 0){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $menu = new Menu();
        if($menu->menuDel($menuid)) {
            return ['code'=>200,'msg'=>'删除成功'];
        }else{
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

    /**
     * 新增保存菜单
     * @param Request $request
     */
    public function addMenu (Request $request) {
        $cateid      = $request->input('cateid');
        $menu_name   = $request->input('menu_name');
        $contro_name = $request->input('contro_name');
        $action_name = $request->input('action_name');
        $pid   = 0;
        $levle = 1;
        $menu = new Menu();
        if($cateid != 0){
            $catepid = $menu->findMenuId($cateid);
            if($catepid && $catepid->pid == 0){
                $levle  = 2;
            }else{
                $levle  = 3;
            }
            $pid    = $cateid;
        }
        if($menu->menuAdd(['name'=>$menu_name,'mm'=>'admin','mc'=>$contro_name,'ma'=>$action_name,'pid'=>$pid,'levle'=>$levle])){
            return ['code'=>200,'msg'=>'新增成功'];
        }else{
            return ['code'=>0,'msg'=>'新增失败'];
        }
    }

    /**
     * 编辑保存菜单
     * @param Request $request
     */
    public function saveMenu (Request $request) {
        $id          = $request->input('id');
        $cateid      = $request->input('cateid');
        $menu_name   = $request->input('menu_name');
        $contro_name = $request->input('contro_name');
        $action_name = $request->input('action_name');
        $pid   = 0;
        $levle = 1;
        $menu = new Menu();
        if($cateid != 0){
            $catepid = $menu->findMenuId($cateid);
            if($catepid && $catepid->pid == 0){
                $levle  = 2;
            }else{
                $levle  = 3;
            }
            $pid    = $cateid;
        }
        if($menu->menuEdit($id,['name'=>$menu_name,'mc'=>$contro_name,'ma'=>$action_name,'pid'=>$pid,'levle'=>$levle])){
            return ['code'=>200,'msg'=>'编辑成功'];
        }else{
            return ['code'=>0,'msg'=>'编辑失败'];
        }
    }
}