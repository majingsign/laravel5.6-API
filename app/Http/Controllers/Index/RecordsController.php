<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 10:55
 */

namespace App\Http\Controllers\Index;


use App\Http\Model\Records;
use Illuminate\Support\Facades\Session;

class RecordsController {


    /**
     * 考勤列表
     */
    public function records(){
        $userid = Session::get('memberid');
        if(empty($userid)){
            return redirect(route('index.login.login'));
        }
        $records = new Records();
        $recordList = $records->getRecordsUsersAll($userid);
        return view('index.records.records',['list'=>$recordList]);
    }

}