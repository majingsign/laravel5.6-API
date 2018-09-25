<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 13:35
 */

namespace App\Http\Controllers\Admin;

use App\Http\Model\Admin;
use App\Http\Model\City;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Qingjia;
use Illuminate\Support\Facades\Session;

class IndexController extends AdminBaseController {

    //后台管理首页
    public function index() {
        //检测管理员
        $list = $this->menuCheck();
        if(!$this->checkUser()){
            return redirect(route('admin.login'));
        }
        if($list == null || empty($list)){
            return redirect(route('admin.login'));
        }
        return view('admin.index.index',['menus'=>$list]);
    }

    /**
     * 显示统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function welcome (){
        //查询是否有未审批的通知
        $qingjia = new Qingjia();
        $act = $this->checkDepart(); //判断是超级管理员  不是超级管理员就是部门负责人
        $comAdmin = Session::get('comAdmin');  //企业负责人
        if($act == false){
            $list = $qingjia->qjListNoPass();
        }else{
            //判断是否是企业负责人
            if(!empty($comAdmin) || $comAdmin != ''){
                $list = $qingjia->qjListNoPass($comAdmin,$act);
            }else{
                $list = $qingjia->qjListNoPass(0,$act);
            }
        }

        $member = new Member();
        $city   = new City();
        $depart = new Depart();
        $company= new Company();
        $admin  = new Admin();
        $sum_shouqian = $member->memberSum(1);//轮休总数
        $sum_beian    = $member->memberSum(2);//倒班总数
        $sum_del      = $member->memberSum(3);//被删除的总数
        $sum_worker   = $member->memberSum(4);//在职的总数
        $departSum    = $depart->departaSum();//部门的总数
        $citySum      = $city->citySum();
        $companySum   = $company->companySum();
        $adminSum     = $admin->adminSum();
        return view('admin.index.welcome',['list'=>$list,'sum_shouqian'=>$sum_shouqian,'sum_beian'=>$sum_beian,'sum_del'=>$sum_del,'sum_worker'=>$sum_worker,'citySum'=>$citySum,'departSum'=>$departSum,'companySum'=>$companySum,'adminSum'=>$adminSum]);
    }

}