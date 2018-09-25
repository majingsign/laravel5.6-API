<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 13:50
 */

namespace App\Http\Controllers\Index;


use App\Http\Model\Member;
use App\Http\Model\Records;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IndexController extends IndexBaseController {

    /**
     * 员工打卡展示页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index (Request $request) {
        if(!$this->checkMember()){
            return redirect(route('index.login.login'));
        }

//        $code = \App\Logic\Qrcodes::getQrcode('http://www.baidu.com');
//        var_dump(public_path('login.png'));
//        $wc = new Wechat();
//        echo $wc->GetOpenid();
//        exit;
//        $userid = Session::get('memberid');
//        if($userid){
//            $records = new Records();
//            $recordslist = $records->getRecordsUsers($userid);
//            //已打卡
//            if($recordslist){
//                return view('index.index.member',['recordslist'=>$recordslist]);
//            }
//        }
        //未打卡
        return view('index.index.index',['recordslist'=>null]);
    }

    /**
     * 修改密码
     * @param Request $request
     */
    public function pwd(Request $request){
        return view('index.index.pwd');
    }

    /**
     * 修改密码
     * @param Request $request
     */
    public function savepwd(Request $request){
        $pass    = $request->input('pass');
        $newpass = $request->input('newpass');
        $repass  = $request->input('repass');

        if(empty($newpass) || empty($repass) || empty($pass)){
            return ['code'=>0,'msg'=>'信息不完整'];
        }
        if($newpass != $repass){
            return ['code'=>0,'msg'=>'2次密码不一致'];
        }
        $memberid = Session::get('memberid');
        if(empty($memberid)){
            return ['code'=>0,'msg'=>'退出请重新登陆'];
        }
        $member = new Member();
        $users = $member->loginUser(Session::get('membername'));
        if($users && ($users->password != md5($pass))){
            return ['code'=>0,'msg'=>'原密码错误'];
        }

        if(!$member->editPassword($memberid,['password'=>md5($newpass)])){
            return ['code'=>0,'msg'=>'修改失败'];
        }
        return ['code'=>200,'msg'=>'修改成功'];
    }

    //员工打卡页面
    public function member(){
        $url = 'http://wx.uktong.cn/wx_att/';
        $check = time().rand(1000,9999);
        return view('index.index.member',['recordslist'=>null,'check'=>$check,'url'=>$url]);
    }

    /**
     * 微信扫码打卡
     */
    public function weixinLogin(Request $request){
        $openid = $request->input('openid');
        $mem = new Member();
        $users = $mem->openidLogin($openid);
        if($users){
            Session::put('membername', $users->user_name);
            Session::put('memberid', $users->user_id);
            return ['code'=>200,'msg'=>'登陆成功'];
        }else{
            return ['code'=>0,'msg'=>'未绑定，请联系管理员绑定用户信息'];
        }
    }

    /**
     * 请求微信服务器获取openid
     * @param Request $request
     */
    public function checkWxPost(Request $request) {
        $check = $request->input('check');
        $url = "http://wx.uktong.cn/wx_att/get.php?Ac=CheckWxLogin&check={$check}";
        $rest =  $this->ReadWxInfoX($url);
        return $rest;
    }

    public function ReadWxInfoX($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        echo $output;
    }

    /**
     * 员工手动打卡
     * @param Request $request
     */
    public function memberLogin(Request $request){
        $username  = trim($request->input('username'));
        $starttime = trim($request->input('starttime'));
        if(empty($username) || $username == ''){
            return ['code'=>0,'msg'=>'请输入员工姓名'];
        }
        //员工登陆
        $member = new Member();
        $members = $member->loginUser($username);
        if($members) {
            $records = new Records();
            //判断今天是否已考勤
            if($starttime){
                $times = strtotime($starttime);
            }else{
                $times = time();
            }
            $recordsData = $records->findClockDayRecords($members->user_id, $times);
            //已打卡
            if ($recordsData) {
                //下班打卡
                if ($recordsData->num < 2) {
                    $records->recordsAdd(['user_id' => $members->user_id, 'username' => $username, 'clock_time' => $times,'date_time'=>date('Y-m-d',$times), 'com_id' => $members->com_id]);
                } else {
                    //今天不能打卡
                    return ['code' => 0, 'msg' => '今天打卡已完成'];
                }
            } else {
                //未打卡 上班打卡
                $records->recordsAdd(['user_id' => $members->user_id, 'username' => $username, 'clock_time' => $times,'date_time'=>date('Y-m-d',$times), 'com_id' => $members->com_id]);
            }
        }else{
            return ['code'=>0,'msg'=>'员工不存在'];
        }
        $recordslist = $records->getRecordsUsers($members->user_id);
        //已打卡
        if($recordslist){
            foreach ($recordslist as $key =>$value){
                $recordslist[$key]->clock_time = date('Y-m-d H:i:s',$value->clock_time);
            }
            return ['code'=>200,'msg'=>'打卡成功','data'=>$recordslist];
        }else{
            return ['code'=>200,'msg'=>'打卡成功'];
        }
    }
}