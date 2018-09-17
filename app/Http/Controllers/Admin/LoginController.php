<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 13:42
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Model\Admin;
use App\Http\Model\Company;
use App\Http\Model\Depart;
use App\Http\Model\Member;
use App\Http\Model\Records;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController  extends Controller {

    /**
     * 管理员登陆
     */
    public function login (){
        return view('admin.login.login');
    }


    /**
     * 用户登陆
     */
    public function loginAction (Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        $messages = [
            'username'    => '用户名不能为空!',
            'password'    => '密码不能为空!',
        ];
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ],$messages);
        if ($validator->fails()) {
            return ['code'=>0,'msg'=>'用户名或者密码错误'];
        }
        $admin = new Admin();
        $password = md5($password);
        //管理员登陆
        $rest = $admin->login($username);
        if(!empty($rest)){
            if($rest->admin_password != $password){
                return ['code'=>0,'msg'=>'登陆密码错误'];
            }
            $depart = new Depart();
            $com = $depart->findDepartId($rest->depart_id);
            if(empty($com)){
                return ['code'=>0,'msg'=>'管理员未设置公司'];
            }
            //判断是否是公司负责人
            $coms = new Company();
            $comslist = $coms->findCompanyAdmin($com->com_id,$rest->id);
            if(!empty($comslist)){
                Session::put('comAdmin',$comslist->id);
            }else{
                //不是企业负责人
                Session::put('comAdmin','');
            }
            Session::put('username',$username);
            Session::put('adminid',$rest->id);
            Session::put('departid',$rest->depart_id);
            Session::put('comid',$com->com_id);
            return ['code'=>200,'msg'=>'登陆成功'];
        }else{
            return ['code'=>0,'msg'=>'管理员不存在'];
        }
    }


    //退出系统
    public function loginout() {
        Session::flush();
        return redirect(route('admin.login'));
    }

}