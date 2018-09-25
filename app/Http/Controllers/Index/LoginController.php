<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 10:01
 */

namespace App\Http\Controllers\Index;


//登陆页面
use App\Http\Model\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController {


    public function login(){
        $url = 'http://wx.uktong.cn/wx_att/';
        $check = time().rand(1000,9999);
        return view('index.login.login',['recordslist'=>null,'check'=>$check,'url'=>$url]);
    }

    public function loginAction(Request $request){
        $username = $request->input('username','');
        $userpwd  = $request->input('password','');
        if(empty($username) || empty($userpwd)){
            return ['code'=>0,'msg'=>'用户名或密码必填'];
        }
        $member = new Member();
        $users = $member->loginUser($username);
        if($users && ($users->password == md5($userpwd))){
            Session::put('membername', $username);
            Session::put('memberid', $users->user_id);
            return ['code'=>200,'msg'=>'登陆成功'];
        }
        return ['code'=>0,'msg'=>'登陆失败'];
    }

    public function loginOut(){
        Session::flush();
        return redirect(route('index.login.login'));
    }
}