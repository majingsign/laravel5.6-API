<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 13:50
 */

namespace App\Http\Controllers\Index;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class IndexController extends IndexBaseController {

        //计算算法  计算每个人的这个月的排班情况
        public function index (Request $request) {
            // 1、计算每个人每天的排班   要计算周日和周六上班情况
           $day_start  = date('Y-m');   //当年年份
           $day_end    = date("t");   //当月有多少天
           $start_time = $day_start . '-1';
           $end_time   = $day_start . '-' . $day_end;
           $data_more  = $this->getDateFromRange($start_time,$end_time);
           $day_worker = count($data_more['weeker_worker']);//工作天数
           //计算工作日  周六   周日 上班人数
           //取出所有倒班人员
           $users = DB::table('user')->select(['user_id','user_name'])->where(['duty_type'=>2])->get()->toArray();
           $userscount = Db::table('user')->select(['user_id','user_name'])->where(['duty_type'=>2])->count();
           //取出全部的工作时间段
            $workers = DB::table('type')->select(['id','name','on_time'])->get()->toArray();
            //根据日期全部写入临时表
            $this->trnucates('temp');
            foreach ($data_more['date'] as $v) {
                foreach ($users as $value) {
                    foreach ($workers as $val) {
                        DB::table('temp')->insert(['userid' => $value->user_id, 'username' => $value->user_name, 'riqi' => $v,'duty_id'=>$val->id,'duty_name'=>$val->name]);
                    }
                }
            }

            //根据日期随机取出不同的人数
              //工作日  工作日是2个C1+2个C2+2个C4+2个D1+2个E班
                $user_worker_all = array();
            foreach ($data_more['weeker_worker'] as $value) {
                $user_worker_C1 = $this->getWorkerdate($value,'C1');
                $user_worker_C2 = $this->getWorkerdate($value,'C2',$user_worker_C1);
                $user_worker_C1_C2 = array_merge($user_worker_C1,$user_worker_C2);
                $user_worker_C4 = $this->getWorkerdate($value,'C4',$user_worker_C1_C2);
                $user_worker_C1_C2_C4 = array_merge($user_worker_C1_C2,$user_worker_C4);
                $user_worker_D1 = $this->getWorkerdate($value,'D1',$user_worker_C1_C2_C4);
                $user_worker_C1_C2_C4_D1 = array_merge($user_worker_C1_C2_C4,$user_worker_D1);
                $user_worker_E  = $this->getWorkerdate($value,'E',$user_worker_C1_C2_C4_D1);
                if($user_worker_E){
                    $user_worker_E = $this->addArray($user_worker_E);
                }
                $user_worker_all [] = array_merge($user_worker_C1_C2_C4_D1,$user_worker_E);
            }
              //周六 周六是2个C1+2个C2+2个D1+2个E班，没有C4
              $user_worker_six = array();
              foreach ($data_more['weeker_six'] as $value) {
                  $user_worker_C1 = $this->getWorkerdate($value,'C1');
                  $user_worker_C2 = $this->getWorkerdate($value,'C2',$user_worker_C1);
                  $user_worker_C1_C2 = array_merge($user_worker_C1,$user_worker_C2);
                  $user_worker_D1 = $this->getWorkerdate($value,'D1',$user_worker_C1_C2);
                  $user_worker_C1_C2_D1 = array_merge($user_worker_C1_C2,$user_worker_D1);
                  $user_worker_E  = $this->getWorkerdate($value,'E',$user_worker_C1_C2_D1);
                  if($user_worker_E){
                      $user_worker_E = $this->addArray($user_worker_E);
                  }
                  $user_worker_six [] = array_merge($user_worker_C1_C2_D1,$user_worker_E);
              }

              //周日 星期天是两个C1+两个D1+两个E班
              $user_worker_seven = array();
              foreach ($data_more['weeker_seven'] as $value) {
                  $user_worker_C1 = $this->getWorkerdate($value,'C1');
                  $user_worker_D1 = $this->getWorkerdate($value,'D1',$user_worker_C1);
                  $user_worker_C1_D1 = array_merge($user_worker_C1,$user_worker_D1);
                  $user_worker_E     = $this->getWorkerdate($value,'E',$user_worker_C1_D1);
                  if($user_worker_E){
                      $user_worker_E = $this->addArray($user_worker_E);
                  }
                  $user_worker_seven [] = array_merge($user_worker_C1_D1,$user_worker_E);
              }
              $all = array_merge($user_worker_all,$user_worker_six,$user_worker_seven);


              //计算每个人每天工作，除开休息天数
             $this->trnucates('temps');
              foreach ($all as $key => $value ){
                  if(is_array($value) || is_object($value)){
                      foreach ($value as $k => $val ){
                          DB::table('temps')->insert(['username'=>$val->username,'riqi'=>$val->riqi,'type'=>$val->duty_name,'userid'=>$val->userid]);
                      }
                  }
              }

              /**************重新计算部分*****************/
              //重新计算上班天数
              $user_rest = array();
              foreach ($users as $key => $val) {
                  $user_rest [] = DB::table('temps')->select(['userid','username','riqi','type'])->where(['username'=>$val->user_name])->orderBy('riqi','asc')->get()->toArray();
              }

              //删除记录
//              $temp_dates = array(); //记录休息的时间
//              foreach ($user_rest as $value ) {
//                  if(is_array($value) || is_object($value)){
//                      $value = $this->arraySort($value,'riqi','asc');
//                      foreach ($value as $val) {
//                          //排除E的休息天数
//                          if($val->type == 'E'){
//                              $temp_dates [$val->userid][] = date('Y-m-d',strtotime("$val->riqi + 1 day"));
//                              $temp_dates [$val->userid][] = date('Y-m-d',strtotime("$val->riqi + 2 day"));
//                              DB::table('temps')->where(['userid'=>$val->userid])->whereBetween('riqi',[date('Y-m-d',strtotime("$val->riqi + 1 day")),date('Y-m-d',strtotime("$val->riqi + 2 day"))])->delete();
//                          }
//                      }
//                  }
//              }

//              $temp_users_day = array();
//              foreach ($users as $user) {
//                  $temp_users_day [] = DB::table('temps')->select(['userid','username','riqi','type'])->where(['username'=>$user->user_name])->get()->toArray();
//              }
//
//
//            $temp_dates_worker = array();
//            foreach ($temp_users_day as $key => $value) {
//                $value = $this->arraySort($value, 'riqi');
//                if (is_array($value) || is_object($value)) {
//                    $userid = array_unique(array_column($value, "userid"))[0];
//                    $riqi   = array_column((array)$value, 'riqi');
//                    foreach ($riqi as $v) {
//                        static $i  = 1;
//                        $next_riqi = next($riqi);
//                        if (strtotime($next_riqi) - strtotime($v) == 86400) {
//                            $i++;
//                            if ($i == 6) {
//                                $temp_dates_worker [$userid][] = date('Y-m-d', strtotime("$v + 1 day"));
//                                $temp_dates_worker [$userid][] = date('Y-m-d', strtotime("$v + 2 day"));
//                                DB::table('temps')->where(['userid' => $userid])->where('riqi',date('Y-m-d', strtotime("$v + 1 day")))->delete();
//                                DB::table('temps')->where(['userid' => $userid])->where('riqi',date('Y-m-d', strtotime("$v + 2 day")))->delete();
//                                $i = 1;
//                            }
//                        }else{
//                            $i = 1;
//                        }
//                    }
//                }
//            }
//
////              计算每人每天工作日要上班
//              $all_gt = array();$all_lt = array();$all_eq = array();
//              foreach ($temp_users_day as $key => $value){
//                  $num  = count($value);
//                  if($num > $day_worker){
//                      $keys  = array_rand((array)$value,$day_worker);
//                      foreach ($keys as $k => $ks){
//                          $all_gt [$key][] = $value[$ks];
//                      }
//                  }else if($num < $day_worker){
//                      $sum_day = $day_worker - count($value);
//                      //排除自己全部休息时间
//                      $query = DB::table('temp')->select('userid', 'username', 'riqi', 'duty_id', 'duty_name as type')
//                          ->where(function ($query) {
//                              $query->orWhere(['duty_name' => 'C1'])->orWhere(['duty_name' => 'C2'])->orWhere(['duty_name' => 'C4'])->orWhere(['duty_name' => 'D1'])->orWhere(['duty_name' => 'E']);
//                          })
//                          ->whereIn('userid',array_unique(array_column($value,'userid')));
//                            if(count($temp_dates) != $userscount){
//                               $this->returns();
//                            }else {
//                                if(isset($temp_dates[array_unique(array_column($value, 'userid'))[0]])){
//                                    $query->whereNotIn('riqi', array_merge(array_column($value, 'riqi'), $temp_dates[array_unique(array_column($value, 'userid'))[0]]));
//                                }else{
//                                    $query->whereNotIn('riqi', array_column($value, 'riqi'));
//                                }
//                                if(isset($temp_dates_worker[array_unique(array_column($value,'userid'))[0]])){
//                                     $query->whereNotIn('riqi', $temp_dates_worker[array_unique(array_column($value, 'userid'))[0]]);
//                                }
////                                $query->whereNotIn('riqi', array_merge(array_column($value, 'riqi'), $temp_dates[array_unique(array_column($value, 'userid'))[0]]));
//                            }
//                          $all_lt_add = $query->groupBy(['riqi'])
//                              ->limit($sum_day)
//                              ->inRandomOrder()
//                              ->get()
//                              ->toArray();
//                      //写入数据库
//                      foreach ($all_lt_add as $values) {
//                          $data = [
//                              'userid'   => $values->userid,
//                              'username' => $values->username,
//                              'riqi'     => $values->riqi,
//                              'type'     => $values->type,
//                          ];
//                          DB::table('temps')->insert($data);
//                      }
//                      $all_lt_temp   = array_merge($value,$all_lt_add);
//                      $all_lt [$key] = $all_lt_temp;
//                  }else {
//                      $all_eq [$key] = $value;
//                  }
//              }
//            //排除E最终上班结果
//            $all_last = array_merge($all_gt,$all_lt,$all_eq);

            $all_last = array();
            foreach ($users as $key => $val) {
                $all_last [] = DB::table('temps')->select(['userid','username','riqi','type'])->where(['username'=>$val->user_name])->orderBy('riqi','asc')->get()->toArray();
            }

            //2、计算每个人最后一天的上班情况
            $everyone = array();
            $time_temp = array(); //工作天数
             foreach ($all_last as $value){
                 if(is_array($value) || is_object($value)){
                     $userid    = array_unique(array_column($value,"userid"))[0];
                     $data = array();
                     $value = $this->arraySort($value,'riqi','asc');
                     foreach ($value as $val ) {
                         if($val->userid == $userid){
                             $data [$userid]['user_id']   = $userid;
                             $data [$userid]['user_name'] = $val->username;
                             $data [$userid]['duty_time'] = $day_start;
                             //计算最后一天
                             if($val->riqi == $end_time){
                                 $data [$userid]['last_day'] = $val->type;
                             }else if ($val->riqi == date('Y-m-d',strtotime("$end_time - 1 day"))){
                                 $data [$userid]['last_day'] = $val->type;
                             }else if ($val->riqi == date('Y-m-d',strtotime("$end_time - 2 day"))){
                                 $data [$userid]['last_day'] = $val->type;
                             }else{
                                 $data [$userid]['last_day'] = "C1";
                             }
                             $time_temp [$userid] [] = $val->riqi;
                             $data [$userid]['scheduling'][] = ['riqi'=>$val->riqi,'type'=>$val->type];
                         }
                     }
                     $everyone [$userid] = json_encode($data);
                 }
             }
            //3、生成每个人每月的排班情况，json格式储存  写入数据库
            $times  = time();
            $everyones = array();
            $this->trnucates('user_scheduling');
            foreach ($everyone as $ke => $val) {
                $val = json_decode($val,true);
                    foreach ($val as $k => $v ) {
                        //计算休息月份
                        $time_temps  = array_diff($data_more['date'],$time_temp[$v['user_id']]);
                        $temp_dbs    = array_merge($v['scheduling'],$time_temps);
                        foreach ($temp_dbs as $ks => $vs) {
                            if(!is_array($vs)){
                                $temp_dbs [$ks] = ['riqi'=>$vs,'type'=>''];
                            }
                        }
                        //排序
                        $temp_dbs = $this->arraySort($temp_dbs,'riqi');
                        $data = [
                            'user_name'   => $v['user_name'],
                            'duty_time'   => $day_start,
                            'scheduling'  => $temp_dbs,
                        ];
                        $everyones [$ke] = json_encode($data);
                        DB::table('user_scheduling')->insert([
                            'user_id'     => $v['user_id'],
                            'user_name'   => $v['user_name'],
                            'duty_time'   => $day_start,
                            'last_day'    => $v['last_day'],
                            'scheduling'  => json_encode($temp_dbs),
                            'create_time' => $times,
                        ]);
                    }
            }

            //导出数据
            $ret = $this->exportAll($request , $everyones,$day_start,$data_more);
            print_r($ret);
        }

    /**
     * 连续E将去掉E
     * @param $arr
     * @return int
     */
    public function addArray($arr){
        $sum = array();
        foreach ($arr as $key => $value) {
            if($value->duty_name == "E" && $value->userid){
                if(DB::table('temp')->select(['userid','username','riqi','duty_id','duty_name'])
                    ->whereBetween('riqi',[$value->riqi,date('Y-m-d',strtotime("$value->riqi -2 day"))])
                    ->where(['duty_name'=>"E",'userid'=>$value->userid])->get()->toArray()){
                    $sum [$key] = $this->getWorkerdateDigui($value->riqi,"E",$value->userid);
                }else{
                    $sum [$key] = $value;
                }
            }else{
                $sum [$key] = $value;
            }
        }
        return $sum;
    }

    /**
     * 清除表
     */
        public function trnucates($table){
            DB::select('TRUNCATE TABLE yxkj_' . $table);
        }

        /**
         * @param $arr
         * @param $keys  传递排序字段 time
         * @param string $type  desc  asc
         * @return array
         */
        public function arraySort($arr, $keys, $type = 'asc') {
            $keysvalue = $new_array = array();
            if(is_object($arr)){
                $arr = (array)$arr;
            }
            foreach ($arr as $k => $v) {
                $v = (array)$v;
                $keysvalue[$k] = $v[$keys];
            }
            $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
            reset($keysvalue);
            foreach ($keysvalue as $k => $v) {
                $new_array[$k] = $arr[$k];
            }
            return $new_array;
        }

        //计算每月1日到30/31的天数
        public function getDateFromRange($startdate, $enddate){
            $stimestamp = strtotime($startdate);
            $etimestamp = strtotime($enddate);
            $days = ($etimestamp-$stimestamp)/86400+1;
            $date = array();
            $weeker_six    = array();
            $weeker_seven  = array();
            $weeker_worker = array();
            for($i=0; $i<$days; $i++){
                //周六
                if(date('N',$stimestamp+(86400*$i)) == 6){
                    $weeker_six [] = date('Y-m-d', $stimestamp+(86400*$i));
                }
                //周日
                if(date('N',$stimestamp+(86400*$i)) == 7){
                    $weeker_seven [] = date('Y-m-d', $stimestamp+(86400*$i));
                }
                //工作日
                if(date('N',$stimestamp+(86400*$i)) != 6 && date('N',$stimestamp+(86400*$i)) != 7){
                    $weeker_worker [] = date('Y-m-d', $stimestamp+(86400*$i));
                }
                //全部天数
                $date[] = date('Y-m-d', $stimestamp+(86400*$i));
            }
            return ['date'=>$date,'weeker_six'=>$weeker_six,'weeker_seven'=>$weeker_seven,'weeker_worker'=>$weeker_worker];
        }

    /**
     *
     * @param $date
     * @param $type
     * @param int $uid
     */
     public function getWorkerdate ($date,$type,$uid = array()) {
         return  DB::table('temp')->select(['userid','username','riqi','duty_id','duty_name'])
                ->where(['riqi'=>$date,'duty_name'=>$type])
                ->limit(2)
                ->whereNotIn('userid',array_column($uid,'userid'))
                ->inRandomOrder()
                ->get()
                ->toArray();
     }
     /**
     *  递归使用
     * @param $date
     * @param $type
     * @param int $uid
     */
     public function getWorkerdateDigui ($date,$type,$uid = array()) {
         return  DB::table('temp')->select(['userid','username','riqi','duty_id','duty_name'])
                ->where(['riqi'=>$date,'userid'=>$uid])
                ->whereNotIn('duty_name',$type)
                ->limit(1)
                ->inRandomOrder()
                ->get()->toArray();
     }

    /**
     * 导出
     * @param Request $request
     * @param $data   需要导出的数据
     * @param $day    月份
     * @return $this
     */
    public function exportAll(Request $request,$data,$day,$data_more){
        $path = str_replace('\\','/',app_path());
        include_once($path .'/Http/PHPExcel/PHPExcel.php');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('忆享-' . $day. '月班表');
        $arrChar = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG');
        //设置默认对齐方式
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置默认行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight('20');
        //设置列宽A~Z
        $arrCharWidth = array('8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8','8');
        //importsample
        foreach ($arrChar as $k => $v) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth($arrCharWidth[$k]);
        }
        $style_obj = new \PHPExcel_Style();
        // 设置首行单元格格式
        $style_array = array(
            'borders'    => array(
                'top'    => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'left'   => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'right'  => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        );
//        $style_obj->applyFromArray($style_array);
//        $objPHPExcel->getActiveSheet()->setSharedStyle($style_obj, "A1:I1");
        //设置首行标题填充的样式和背景色
//        $objFillLine1 = $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFill();
//        $objFillLine1->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
//        $objFillLine1->getStartColor()->setARGB('FFC4D79B');
        $i=0;
        $objPHPExcel->getActiveSheet()->setCellValue($arrChar[$i++].'1','姓名');
        foreach ($data_more['date'] as $value ) {
            $objPHPExcel->getActiveSheet()->setCellValue($arrChar[$i++].'1',date('m月d日',strtotime($value)));
        }
        $i=2;
        $j = 0;
        foreach($data as $k => $v) {
            $val = json_decode($v, true);
            if (is_array($val['scheduling']) || is_object($val['scheduling'])) {
                $objPHPExcel->getActiveSheet()->setCellValue($arrChar[$j++] . $i, $val['user_name']);
                foreach ($val['scheduling'] as $key => $value) {
                    $objPHPExcel->getActiveSheet()->setCellValue($arrChar[$j++] . $i, $value['type']);
                }
                $i++;
                $j = 0;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('忆享-' . $day. '月班表');
        $objPHPExcel->setActiveSheetIndex(0);
        $filename= '忆享-' . $day. '月班表' . '.xlsx';
        $filename = iconv('utf-8', 'gb2312', $filename);
        //生成xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter=\PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

}