<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 13:35
 */

namespace App\Http\Controllers\Admin;

use App\Http\Model\City;
use App\Http\Model\Member;

class IndexController extends AdminBaseController {


    //后台管理首页
    public function index() {
        //检测管理员
        if(!$this->checkUser()){
            return redirect(route('admin.login'));
        }
        return view('admin.index.index');
    }


    /**
     * 显示统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function welcome (){
        $member = new Member();
        $city   = new City();
        $sum_shouqian = $member->memberSum(1);//轮休总数
        $sum_beian    = $member->memberSum(2);//倒班总数
        $sum_del      = $member->memberSum(3);//被删除的总数
        $sum_worker   = $member->memberSum(4);//在职的总数
        $citySum      = $city->citySum();
        return view('admin.index.welcome',['sum_shouqian'=>$sum_shouqian,'sum_beian'=>$sum_beian,'sum_del'=>$sum_del,'sum_worker'=>$sum_worker,'citySum'=>$citySum]);
    }

}