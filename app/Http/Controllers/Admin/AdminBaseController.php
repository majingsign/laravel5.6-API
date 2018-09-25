<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 13:31
 */

namespace  App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Model\Depart;
use App\Http\Model\Menu;
use Illuminate\Support\Facades\Session;

/**
 *
 * Class AdminBaseController
 * @package App\Http\Controllers\Admin
 */
class AdminBaseController extends Controller {

    protected $type   = ['1'=>'轮休','2'=>'倒班'];
    protected $status = ['1'=>'在职','2'=>'离职'];
    protected $qingjia= ['1'=>'事假','2'=>'病假','3'=>'婚假','4'=>'产假','5'=>'丧假','6'=>'其他'];

    /**
     * 检测用户是否登陆
     */
    public function checkUser () {
        $username = Session::get('username');
        if($username == null || empty($username) || $username == NULL){
            return false;
        }else{
            return true;
        }
    }


    /**
     * 检查部门权限
     */
    public function checkDepart () {
        $departid = Session::get('departid');
        if(isset($departid) && !empty($departid)){
            $depart = new Depart();
            $act = $depart->findDepartMenuList($departid);
            if($act == "*"){
                return false;
            }else{
                return $departid;
            }
        }else{
            return false;
        }
    }


    /**
     * 菜单检测并获取菜单
     */
    protected function menuCheck ($flg = 0) {
        // 部门关联id
        $departid = Session::get('departid');
        if(empty($departid) || $departid == ''){
            return null;
        }else{
            $depart = new Depart();
            $list = $depart->findDepartMenuList($departid);
            if($list == '' || empty($list)){
                return null;
            }
            $menus = $this->menuLists($list,$flg);
            if($menus == null){
                return null;
            }
            return $menus;
        }
    }

    /**
     * 获取全部的菜单
     * @param $data 菜单列表
     * @param $flg  1 无需循环  0 需要循环
     * @return array|bool|null
     */
    public function menuLists ($data,$flg = 0) {
        //超级管理员
        $menu = new Menu();
        if($data == '*'){
            $menus = $menu->menuLists($flg);
        }else{
            $menus = $menu->getMenuids($data,$flg);
        }
        if($menus == '' || empty($menus)){
            return null;
        }
        return $menus;
    }

}