<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/3
 * Time: 14:37
 */

namespace App\Http\Middleware;

use App\Http\Model\Depart;
use App\Http\Model\Menu;
use Illuminate\Support\Facades\Session;
use Closure;

class CheckMenu {
    public function handle($request, Closure $next, $guard = null) {
        $common_data = [
            'admin/index','admin/welcome','admin/login','admin/loginout','admin/loginAction','admin/error/index','admin/admin/ajaxDepart','admin/member/ajaxMemberDepart',
            'admin/admin/savepwd','admin/admin/addAdmin','admin/menu/addMenu','admin/menu/saveMenu','admin/depart/addDepart','admin/depart/saveDepart','admin/member/addMember',
            'admin/member/editMember','admin/member/ajaxMemberDepart','admin/company/addCompany','admin/company/saveCompany','admin/city/addCity','admin/city/saveCity',
            'admin/duty/addDuty','admin/duty/saveDuty','admin/rotation/shiftsave','admin/member/qingjiasave','admin/member/qingjiasaveUser','admin/member/ajaxPass'
        ];
        $data = $this->menuCheck(1);
        if(!empty($data)) {
            $data_str = '';
            foreach ($data as $v) {
                $data_str .= $v->mm . '/' . $v->mc . '/' . $v->ma;
                $data_str .= ',';
            }
            $data_arr  = array_filter(explode(',', $data_str));
            $datas     = array_merge($common_data, $data_arr);
            $route     = request()->route()->getAction()['as'];
            $str_route = str_replace('.', '/', $route);
            if (!in_array($str_route, $datas)) {
                if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest"){
                    return response()->json(['code' => 0,'msg'=>'您无权限操作.']);
                }else{
                    return redirect('/admin/error/index');
                }
            }
        }
        return $next($request);
    }

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


    protected function menuLists ($data,$flg = 0) {
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