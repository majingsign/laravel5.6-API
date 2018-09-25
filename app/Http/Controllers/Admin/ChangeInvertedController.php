<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10
 * Time: 15:24
 */

namespace App\Http\Controllers\Admin;
use App\Http\Model\Member;
use App\Http\Model\Inverted;
use App\Logic\UserWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Logic\InvertedLogic;
use App\Logic\ExcelLogic;
use App\Csv\Csv;

/** 倒班的排班
 * Class ChangeInvertedController
 * @package App\Http\Controllers\Admin
 */
class ChangeInvertedController extends AdminBaseController
{
    public function index(InvertedLogic $invertedLogic,UserWork $userWork,Member $member){
        $time = $userWork -> getCurrentLastDay();
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        $list = DB::table($table_name) -> get() -> toArray();
        $user_list = $member -> getUserListByIdList(2);
        $list_arr = [];
        if(count($list) > 0){
            foreach($list as  $key => $value){
                if(array_key_exists($value -> user_id,$user_list)){
                    $list_arr[$value -> user_id]['work'] = json_decode($value -> scheduling);
                    $list_arr[$value -> user_id]['user_name'] = $user_list[$value -> user_id]->user_name;
                }

            }
        }
        $week_list = $userWork -> getMonthWeek(0);
        $type_list = DB::table('type') -> get() -> toArray();
        return view('admin.changeinverted.index',[
            'list'      => $list_arr,
            'week_list' => $week_list,
            'type_list' => $type_list
        ]);
    }

    /**
     * 查看下个月已经生成的倒班顺序
     */
    public function seeNextGenerate(InvertedLogic $invertedLogic,UserWork $userWork,Member $member){
        $time = $userWork -> getNextMonthLastDay(date('Y-m-d'));
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        $list = DB::table($table_name) -> get() -> toArray();
        $user_list = $member -> getUserListByIdList(2);
        $list_arr = [];
        if(count($list) > 0){
            foreach($list as $value){
                $list_arr[$value -> user_id]['work'] = json_decode($value -> scheduling);
                $list_arr[$value -> user_id]['user_name'] = $user_list[$value -> user_id]->user_name;
            }
        }
        //获取本月的日历和对应的星期几
        $week_list = $userWork -> getMonthWeek();
        $type_list = DB::table('type') -> get() -> toArray();
        return view('admin.changeinverted.work',[
            'list'       => $list_arr,
            'week_list' => $week_list,
            'type_list' => $type_list,
            'is_old' => 1
        ]);
    }






    public function checkGenerate(Member $member,Inverted $inverted){
        //>>1.判断下个月员工中最后一天所有的用户都有设置最后一天的上班情况
        $user_list = $member -> getUserListByIdList(2);
        $check_user = $inverted -> checkGenerateUser($user_list);
        if(!$check_user['code']){
            return json_encode($check_user);
        }
        //>>2.判断离职人员的离职时间
        $check_quit = $member -> getQuitStr(2);
        return json_encode(['code' => 1,'message' => $check_quit]);
    }

    /**
     * 生成下个月倒班的数据
     */
    public function shift(UserWork $userWork,Member $member){
        //>>1.判断那些员工能上班的时间段(离职的员工)
        $work_arr =  $userWork -> getUserWork(2);
        //>> 2. 判断每天是该上班还是休息
        $invertedLogic = new \App\Logic\InvertedLogic();
        $normal_user_list =  $invertedLogic -> normalWork($work_arr);
        //展示视图页面
        $user_list = $member -> getUserListByIdList(2);
        $return_arr = [];
        foreach($normal_user_list as  $k => $value){
            if(empty($value)) continue;
            $return_arr[$k] = [];
            $return_arr[$k]['work'] = [];
            if(array_key_exists($k,$user_list)){
                $return_arr[$k]['user_name'] = $user_list[$k] -> user_name;
            } else {
                $return_arr[$k]['user_name'] = '';
            }
            foreach($value as $keys){
              if(in_array($keys,[1,2,3,4])){
                  $return_arr[$k]['work'][] = 'C1';
              }
                if(in_array($keys,[8,9,10,11])){
                    $return_arr[$k]['work'][] = 'C2';
                }
                if(in_array($keys,[15,16,17,18])){
                    $return_arr[$k]['work'][] = 'C4';
                }
                if(in_array($keys,[22,23,24,25])){
                    $return_arr[$k]['work'][] = 'D1';
                }
                if(in_array($keys,[5,12,19,26])){
                    $return_arr[$k]['work'][] = 'E';
                }
                if(in_array($keys,[6,7,13,14,20,21,27,28])){
                    $return_arr[$k]['work'][] = '休';
                }
                if(!$keys){
                    $return_arr[$k]['work'][] = '';
                }
            }
        }
        //获取本月的日历和对应的星期几
        $week_list = $userWork -> getMonthWeek();
        $type_list = DB::table('type') -> get() -> toArray();
        return view('admin.changeinverted.work',[
            'list'       => $return_arr,
            'week_list' => $week_list,
            'type_list' => $type_list,
            'is_old'  => 0
        ]);

    }

    /**保存下个月/当前月的倒班排班系统
     * @return string
     */
    public function shiftSave(Request $request,UserWork $userWork,InvertedLogic $invertedLogic){
        $request_arr = $request -> post('list_arr','');
        $is_next = $request -> post('is_next','');
        $add_all = [];
        //判断下个月是否离职
        if($is_next){
            $time = $userWork -> getNextMonthLastDay(date('Y-m-d'));
        } else {
            $time = $userWork -> getCurrentLastDay();
        }

        $month_count =  date('t', strtotime($time)) ;
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        $last_shift_arr = [];
        foreach($request_arr as  $k => $value){
            if(empty($value)){continue;}
            $last_month_dat_type = $invertedLogic -> getShiftLastDay($value,$month_count);
            $add_all[] = [
                'user_id'     => $k,
                'scheduling'  => json_encode($value),
                'last_day'    =>  $last_month_dat_type,
                'create_time' => time(),
            ];
            $last_shift_arr[] = [
                'user_id' => $k,
                'last_month_dat_type' => $last_month_dat_type,
                'year_month' => date('Y-m',time())
            ];
        }
        //开启事务
        DB::beginTransaction();
        $rst1 = DB::table($table_name) -> where('id','>',0) -> delete();
        $rst2 = DB::table($table_name) -> insert($add_all);
        //最后一天上班的数据
        $rst3 = DB::table('inverted_last_month_day') -> where('year_month','=',date('Y-m',time())) -> delete();
        $rst4 = DB::table('inverted_last_month_day') -> insert($last_shift_arr);

        if($rst1 !== false  && $rst2 && $rst3 !== false && $rst4){
            DB::commit();
            return json_encode(['code' => 1,'message' => '保存成功']);
        }
        DB::Rollback();
        return json_encode(['code' => -1,'message' => '保存失败']);
    }

    /**
     * 导出本月倒班内容
     */
    public function currExcelPort(UserWork $userWork,InvertedLogic $invertedLogic,Member $member,ExcelLogic $excelLogic){
        $time = $userWork -> getCurrentLastDay();
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        $list = DB::table($table_name) -> get() -> toArray();
        $user_list = $member -> getUserListByIdList(2);
        $list_arr = $week_arr = [];
        if(count($list) > 0){
            foreach($list as $value){
                $list_arr[$value -> user_id] = json_decode($value -> scheduling);
                array_unshift($list_arr[$value -> user_id],$user_list[$value -> user_id]->user_name);
            }
        }
        $week_list = $userWork -> getMonthWeek(0);
        foreach($week_list as $value){
            $week_arr[] = $value['day'].'-'.$value['week'];
        }
        array_unshift($week_arr,'用户名');
        $excelLogic ->exportAll('本月倒班导出列表',$week_arr,array_values($list_arr));

    }

    /**
     * 导出下月倒班内容
     */
    public function nextrexcelport(UserWork $userWork,InvertedLogic $invertedLogic,Member $member,ExcelLogic $excelLogic){
        $time = $userWork -> getNextMonthLastDay(date('Y-m-d'));
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        $list = DB::table($table_name) -> get() -> toArray();
        $user_list = $member -> getUserListByIdList(2);
        $list_arr = $week_arr = [];
        if(count($list) > 0){
            foreach($list as $value){
                $list_arr[$value -> user_id] = json_decode($value -> scheduling);
                array_unshift($list_arr[$value -> user_id],$user_list[$value -> user_id]->user_name);
            }
            //获取本月的日历和对应的星期几
            $week_list = $userWork -> getMonthWeek();
            foreach($week_list as $value){
                $week_arr[] = $value['day'].'-'.$value['week'];
            }
            array_unshift($week_arr,'用户名');
            $excelLogic ->exportAll('下月倒班导出列表',$week_arr,array_values($list_arr));
        } else {
            echo '<script>alert("请先保存下月排班数据");</script>';
            return redirect()->action('\App\Http\Controllers\Admin\ChangeInvertedController@shift');
        }

    }

    /** 废除下个月的倒班排班数据
     * @param UserWork $userWork
     * @param InvertedLogic $invertedLogic
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function discardedGenerate(UserWork $userWork,InvertedLogic $invertedLogic){
        $time = $userWork -> getNextMonthLastDay(date('Y-m-d'));
        $table_name  =  $invertedLogic -> getTableName(date('Y_m',strtotime($time)));
        if($userWork -> dropTable($table_name)){
            return redirect('/admin/changeinverted/index');
        } else{
            return redirect('/admin/changeinverted/index') ->withErrors('废除失败');
        }
    }



    public function importList(){
        return view('admin.changeinverted.import');
    }




    /** 提交导入的数据信息
     * @param Request $request
     * @param ShiftLogic $shiftLogic
     * @return $this
     */
    public function importlistpost(Request $request,InvertedLogic $invertedLogic){
        try {
            //数据导入方式
            $max_day = date('t',time());
            ini_set("max_execution_time", "1800");
            $myfile = $request -> file('file');
            if (empty($myfile)) {
                echo '<script>alert("温馨提示：没有上传文件");parent.location.href = "/admin/changeinverted/index";</script>';
                exit;
            }
            //获取文件名称
            $fileName = $_FILES['file']['name'];
            $csv = new Csv();
            $data = $csv->import($myfile, $fileName,intval($max_day + 1));
            if ($data['code'] == -1) {
                exit('温馨提示：' . $data['message']);
            }
            $data = $data['data'];
            //去除空元素
            $newRes = [];
            foreach ($data as $key => $val) {
                if (array_filter($val)) {
                    $newRes[] = array_filter($val);
                }
            }
            //获取表名
            $table_name  =  $invertedLogic -> getTableName(date('Y_m',time()));
            DB::beginTransaction();
            $rst1 = DB::table($table_name) -> where('id','>',0) -> delete();
            $inser_data = [];
            foreach($newRes as $value){
                //判断是否有空的数据
                $user_name = trim($value[0]);
                if(intval($max_day + 1) != count($value)){
                    DB::rollBack();
                    $str = '用户名为'.$user_name.'的排班数据,有为空的数据,请核对后再进行添加';
                    echo '<script>alert("'.$str .'");parent.location.href = "/admin/changeinverted/index";</script>';
                    exit;
                }
                $user_id = DB::table('user')
                    -> where('user_name','=',$user_name)
                    -> where('duty_type','=',2)
                    -> value('user_id');
                if(!$user_id){
                    DB::rollBack();
                    $str = '用户名为'.$user_name.'的员工不存在, 请核对后再进行添加';
                    echo '<script>alert("'.$str .'");parent.location.href = "/admin/changeinverted/index";</script>';
                    exit;
                }
                unset($value[0]);
                $inser_data[] = [
                    'user_id'    => $user_id,
                    'scheduling'  => json_encode(array_values($value)),
                    'create_time' => time(),
                ];

            };
            $rst2 = DB::table($table_name) -> insert($inser_data);
            if($rst1 !== false && $rst2){
                DB::commit();
                echo '<script>alert("导入成功");parent.location.href = "/admin/changeinverted/index";</script>';
                exit;
            } else {
                DB::rollBack();
                echo '<script>alert("导入失败");parent.location.href = "/admin/changeinverted/index";</script>';
                exit;
            }

        } catch (\Exception $ex) {
            echo '<script>alert("'.$ex ->getMessage().'");parent.location.href = "/admin/changeinverted/index";</script>';
            exit;
        }
    }








}