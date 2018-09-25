<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 9:43
 */

namespace App\Http\Controllers\Admin;

use App\Http\Model\UserCity;
use App\Http\Model\City;
use App\Http\Model\Member;
use App\Http\Model\Shift;
use App\Logic\ShiftLogic;
use App\Logic\UserWork;
use App\Logic\ExcelLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Csv\Csv;

class RotationController extends AdminBaseController
{


    /**
     * 当月轮休排班列表
     */
    public function index(ShiftLogic $shiftLogic, UserWork $userWork, Member $member)
    {
        $table_name = $shiftLogic->getTableName(date('Y_m', time()));
        $list = DB::table($table_name)->get()->toArray();
        $user_list = $member->getUserListByIdList();
        $list_arr = [];
        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                if (array_key_exists($value->user_id, $user_list)) {
                    $list_arr[$value->user_id]['work'] = json_decode($value->scheduling);
                    $list_arr[$value->user_id]['user_name'] = $user_list[$value->user_id]->user_name;
                }

            }
        }
        $week_list = $userWork->getMonthWeek(0);
        $type_list = DB::table('type')->get()->toArray();

        return view('admin.rotation.index', [
            'list' => $list_arr,
            'week_list' => $week_list,
            'type_list' => $type_list
        ]);
    }


    /**
     * 判断生成下个月的轮班系统
     */
    public function checkGenerate(UserCity $userCity, City $city, Member $member, Shift $shift)
    {
        //>>1.而且该城市只有一个人在管理 判断城市中是否有属于空的,
        $city_list = $city->getCityByIdList();
        $check_city = $userCity->checkGenerateCity($city_list);

        if (!$check_city['code']) {
            return json_encode($check_city);
        }
        //>>2.判断下个月员工中最后一天所有的用户都有设置最后一天的上班情况
        $user_list = $member->getUserListByIdList();
        $check_user = $shift->checkGenerateUser($user_list);
        if (!$check_user['code']) {
            return json_encode($check_user);
        }

        //>>3. 员工一个城市都没有分配
        $check_user_city = $userCity->checkUserCity($user_list);
        if (!$check_user_city['code']) {
            return json_encode($check_user_city);
        }
        //>>4.判断离职人员的离职时间
        $check_quit = $member->getQuitStr();
        return json_encode(['code' => 1, 'message' => $check_quit]);
    }


    public function shift(City $city, UserCity $userCity, Member $member)
    {
        $userWork = new UserWork();
        //>>1.判断那些员工能上班的时间段(离职的员工)
        $work_arr = $userWork->getUserWork(1);
        //>>2. 判断每天该休息还是是上班
        $shiftLogic = new \App\Logic\ShiftLogic();
        $normal_user_list = $shiftLogic->normalWork($work_arr);
        //>> 3. 判断每个城市都有人,并且满足最小值
        list($city_arr, $min_city_arr) = $city->getCityIdArr();
        $user_city_arr = $userCity->getUserCityArr();
        $list = $normal_user_list;
//       $list = $shiftLogic -> checkBestType($city_arr,$min_city_arr,$user_city_arr,$normal_user_list);
        //展示视图页面
        $user_list = $member->getUserListByIdList();
        $return_arr = [];

        foreach ($list as $k => $value) {
            if (empty($value)) continue;
            $return_arr[$k] = [];
            $return_arr[$k]['work'] = [];
            if (array_key_exists($k, $user_list)) {
                $return_arr[$k]['user_name'] = $user_list[$k]->user_name;
            } else {
                $return_arr[$k]['user_name'] = '';
            }
            foreach ($value as $keys) {
                if (in_array(intval($keys), [1, 2, 3, 4, 5])) {
                    $return_arr[$k]['work'][] = 'T';
                } elseif (in_array(intval($keys), [6, 7])) {
                    $return_arr[$k]['work'][] = '休';
                } else {
                    $return_arr[$k]['work'][] = '';
                }

            }
        }
        //获取本月的日历和对应的星期几
        $week_list = $userWork->getMonthWeek();
        $type_list = DB::table('type')->get()->toArray();
        return view('admin.rotation.work', [
            'list' => $return_arr,
            'week_list' => $week_list,
            'type_list' => $type_list,
            'is_old' => 0
        ]);


    }

    /** 保存下个月/当前月的轮休排班系统
     * @param Request $request
     */
    public function shiftSave(Request $request, ShiftLogic $shiftLogic, UserWork $userWork)
    {
        $request_arr = $request->post('list_arr', '');
        $is_next = $request->post('is_next', '');
        $add_all = [];
        //判断下个月是否离职
        if ($is_next) {
            $time = $userWork->getNextMonthLastDay(date('Y-m-d'));
        } else {
            $time = $userWork->getCurrentLastDay();
        }

        $month_count = date('t', strtotime($time));
        $table_name = $shiftLogic->getTableName(date('Y_m', strtotime($time)));
        $last_shift_arr = [];
        foreach ($request_arr as $k => $value) {
            if (empty($value)) {
                continue;
            }
            $last_month_dat_type = $shiftLogic->getShiftLastDay($value, $month_count);
            $add_all[] = [
                'user_id' => $k,
                'scheduling' => json_encode($value),
                'last_day' => $last_month_dat_type,
                'create_time' => time(),
            ];
            $last_shift_arr[] = [
                'user_id' => $k,
                'last_month_dat_type' => $last_month_dat_type,
                'year_month' => date('Y-m', time())
            ];
        }
        //开启事务
        DB::beginTransaction();
        $rst1 = DB::table($table_name)->where('id', '>', 0)->delete();
        $rst2 = DB::table($table_name)->insert($add_all);
        //最后一天上班的数据
        $rst3 = DB::table('shift_last_month_day')->where('year_month', '=', date('Y-m', time()))->delete();
        $rst4 = DB::table('shift_last_month_day')->insert($last_shift_arr);
        if ($rst1 !== false && $rst2 && $rst3 !== false && $rst4) {
            DB::commit();
            return json_encode(['code' => 1, 'message' => '保存成功']);
        }
        DB::Rollback();
        return json_encode(['code' => -1, 'message' => '保存失败']);
    }


    /**
     * 查看下个月的轮休内容
     */
    public function seeNextGenerate(ShiftLogic $shiftLogic, Member $member, UserWork $userWork)
    {
        $time = $userWork->getNextMonthLastDay(date('Y-m-d'));
        $table_name = $shiftLogic->getTableName(date('Y_m', strtotime($time)));
        $list = DB::table($table_name)->get()->toArray();
        $user_list = $member->getUserListByIdList();
        $list_arr = [];
        if (count($list) > 0) {
            foreach ($list as $value) {
                $list_arr[$value->user_id]['work'] = json_decode($value->scheduling);
                $list_arr[$value->user_id]['user_name'] = $user_list[$value->user_id]->user_name;
            }
        }
        $week_list = $userWork->getMonthWeek(1);
        $type_list = DB::table('type')->get()->toArray();
        return view('admin.rotation.work', [
            'list' => $list_arr,
            'week_list' => $week_list,
            'type_list' => $type_list,
            'is_old' => 1,
        ]);
    }


    /**
     * 导出本月的轮休排班
     */
    public function currExcelPort(ShiftLogic $shiftLogic, Member $member, UserWork $userWork, ExcelLogic $excelLogic)
    {
        $table_name = $shiftLogic->getTableName(date('Y_m', time()));
        $list = DB::table($table_name)->get()->toArray();
        $user_list = $member->getUserListByIdList();
        $list_arr = $week_arr = [];
        if (count($list) > 0) {
            foreach ($list as $value) {
                $list_arr[$value->user_id] = json_decode($value->scheduling);
                array_unshift($list_arr[$value->user_id], $user_list[$value->user_id]->user_name);
            }
        }
        $week_list = $userWork->getMonthWeek(0);
        foreach ($week_list as $value) {
            $week_arr[] = $value['day'] . '-' . $value['week'];
        }
        array_unshift($week_arr, '用户名');
        $excelLogic->exportAll('本月轮班导出列表', $week_arr, array_values($list_arr));


    }


    /**
     * 导出下月的轮班排班
     */
    public function nextRexcelPort(UserWork $userWork, ShiftLogic $shiftLogic, Member $member, ExcelLogic $excelLogic)
    {
        $time = $userWork->getNextMonthLastDay(date('Y-m-d'));
        $table_name = $shiftLogic->getTableName(date('Y_m', strtotime($time)));
        $list = DB::table($table_name)->get()->toArray();
        $user_list = $member->getUserListByIdList();
        $list_arr = $week_arr = [];
        if (count($list) > 0) {
            foreach ($list as $value) {
                $list_arr[$value->user_id] = json_decode($value->scheduling);
                array_unshift($list_arr[$value->user_id], $user_list[$value->user_id]->user_name);
            }
        }
        $week_list = $userWork->getMonthWeek(1);
        foreach ($week_list as $value) {
            $week_arr[] = $value['day'] . '-' . $value['week'];
        }
        array_unshift($week_arr, '用户名');
        $excelLogic->exportAll('下月轮班导出列表', $week_arr, array_values($list_arr));
    }


    public function discardedGenerate(UserWork $userWork, ShiftLogic $shiftLogic)
    {
        $time = $userWork->getNextMonthLastDay(date('Y-m-d'));
        $table_name = $shiftLogic->getTableName(date('Y_m', strtotime($time)));
        if ($userWork->dropTable($table_name)) {
            return redirect('/admin/rotation/index');
        } else {
            return redirect('/admin/rotation/index')->withErrors('废除失败');
        }
    }


    public function importList()
    {
        return view('admin.rotation.import');
    }


    /** 提交导入的数据信息
     * @param Request $request
     * @param ShiftLogic $shiftLogic
     * @return $this
     */
    public function importlistpost(Request $request, ShiftLogic $shiftLogic)
    {
        try {
            //数据导入方式
            $max_day = date('t', time());
            ini_set("max_execution_time", "1800");
            $myfile = $request->file('file');
            if (empty($myfile)) {
                echo '<script>alert("温馨提示：没有上传文件");parent.location.href = "/admin/rotation/index";</script>';
                exit;
            }
            //获取文件名称
            $fileName = $_FILES['file']['name'];
            $csv = new Csv();
            $data = $csv->import($myfile, $fileName, intval($max_day + 1));
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
            $table_name = $shiftLogic->getTableName(date('Y_m', time()));
            DB::beginTransaction();
            $rst1 = DB::table($table_name)->where('id', '>', 0)->delete();
            $inser_data = [];
            foreach ($newRes as $value) {
                //判断是否有空的数据
                $user_name = trim($value[0]);
                if (intval($max_day + 1) != count($value)) {
                    DB::rollBack();
                    $str = '用户名为' . $user_name . '的排班数据,有为空的数据,请核对后再进行添加';
                    echo '<script>alert("' . $str . '");parent.location.href = "/admin/rotation/index";</script>';
                    exit;
                }
                $user_id = DB::table('user')
                    ->where('user_name', '=', $user_name)
                    ->where('duty_type', '=', 1)
                    ->value('user_id');
                if (!$user_id) {
                    DB::rollBack();
                    $str = '用户名为' . $user_name . '的员工不存在, 请核对后再进行添加';
                    echo '<script>alert("' . $str . '");parent.location.href = "/admin/rotation/index";</script>';
                    exit;
                }
                unset($value[0]);
                $inser_data[] = [
                    'user_id' => $user_id,
                    'scheduling' => json_encode(array_values($value)),
                    'create_time' => time(),
                ];

            };
            $rst2 = DB::table($table_name)->insert($inser_data);
            if ($rst1 !== false && $rst2) {
                DB::commit();
                echo '<script>alert("导入成功");parent.location.href = "/admin/rotation/index";</script>';
                exit;
            } else {
                DB::rollBack();
                echo '<script>alert("导入失败");parent.location.href = "/admin/rotation/index";</script>';
                exit;
            }

        } catch (\Exception $ex) {
            echo '<script>alert("' . $ex->getMessage() . '");parent.location.href = "/admin/rotation/index";</script>';
            exit;
        }
    }


}