<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 10:02
 */

namespace App\Http\Controllers\Admin;
use App\Logic\InvertedLogic;
use App\Logic\ShiftLogic;
use Illuminate\Support\Facades\DB;
use App\Logic\UserWork;
use App\Logic\RecordLogic;
use App\Http\Model\Member;
use App\Logic\ExcelLogic;
class RecordsController extends AdminBaseController
{
    /**
     * 获取上个月的打卡内容信息
     */
    public function getLastMonthlsit(RecordLogic $recordLogic,Member $member,UserWork $userWork,ExcelLogic $excelLogic){
        set_time_limit(0);
    //获取正常上班每个人每天的时间段
     list($scheduling_list,$inverted_name_list) =   $this -> checkIsGetList();
     $normal_user_list  = $recordLogic -> getList($scheduling_list,$inverted_name_list);
     //获取打卡记录列表
      $record_list = $recordLogic -> getRecordList();
        $list = $recordLogic -> checkRst($normal_user_list,$record_list);
      //组装导出数据
        $user_list = $member -> getUserListByIdList('all');
        $list_arr = $week_arr = [];
        if(count($list) > 0){
            foreach($list as $key =>  $value){
                $list_arr[$key] = $value;
                array_unshift($list_arr[$key],$user_list[$key]->user_name);

            }
        }
         $head_arr = $userWork -> getLastDay();
         array_unshift($head_arr,'用户名');

        $excelLogic ->exportAll('上月考勤列表',$head_arr,array_values($list_arr));


    }


    /**
     * 判断是否可以获取上个月的数据
     */
    private function checkIsGetList(){
      $date = date('d',time());
      if($date < 2){
          reloactionUrlWithMsg('请在本月2号以前生成上月的数据',"/admin/member/records");
      }

      $shiftLogic = new ShiftLogic();
      $inLogic = new InvertedLogic();

      $scheduling_table = $shiftLogic -> getTableName(date('Y_m',strtotime("-1 month")));
      $inverted_name_list = $inLogic -> getTableName(date('Y_m',strtotime("-1 month")));

        $scheduling_list = DB::table($scheduling_table) -> get() -> toArray();
        $inverted_name_list = DB::table($inverted_name_list) -> get() -> toArray();
        if(!count($scheduling_list) ||  !count($inverted_name_list)){
            reloactionUrlWithMsg('上个月的排班数据和轮班数据不能为空',"/admin/member/records");
        }

        return [$scheduling_list,$inverted_name_list];
    }






}