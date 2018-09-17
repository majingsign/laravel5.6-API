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
        return view('index.index.member',['recordslist'=>null]);
    }

    /**
     * 员工打卡
     * @param Request $request
     */
    public function memberLogin(Request $request){
        $username = trim($request->input('username'));
        if(empty($username) || $username == ''){
            return ['code'=>0,'msg'=>'请输入员工姓名'];
        }
        //员工登陆
        $member = new Member();
        $members = $member->loginUser($username);
        if($members) {
            Session::put('membername', $username);
            Session::put('memberid', $members->user_id);
            $records = new Records();
            //判断今天是否已考勤
            $times = time();
            $recordsData = $records->findClockDayRecords($members->user_id, $times);
            //已打卡
            if ($recordsData) {
                //下班打卡
                if ($recordsData->num < 2) {
                    $records->recordsAdd(['user_id' => $members->user_id, 'username' => $username, 'clock_time' => $times, 'com_id' => $members->com_id]);
                } else {
                    //今天不能打卡
                    return ['code' => 0, 'msg' => '今天打卡已完成'];
                }
            } else {
                //未打卡 上班打卡
                $records->recordsAdd(['user_id' => $members->user_id, 'username' => $username, 'clock_time' => $times, 'com_id' => $members->com_id]);
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
            return ['code'=>0,'msg'=>'打卡失败'];
        }
    }
}