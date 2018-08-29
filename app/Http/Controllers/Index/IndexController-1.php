<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 13:50
 */

namespace App\Http\Controllers\Index;


use Illuminate\Support\Facades\DB;

class IndexController extends IndexBaseController {

        //计算算法  计算每个人的这个月的排班情况
        public function index () {
            // 1、计算每个人每天的排班   要计算周日和周六上班情况
           $day_start  = date('Y-m');   //当年年份
           $day_end    = date("t");   //当月有多少天
           $start_time = $day_start . '-1';
           $end_time   = $day_start . '-' . $day_end;
           $data_more = $this->getDateFromRange($start_time,$end_time);
           //计算工作日  周六   周日 上班人数
           //取出所有值班人员
           $users = DB::table('user')->select(['user_id','user_name'])->get()->toArray();
           //取出全部的工作时间段
//            $workers = DB::table('type')->select(['id','name','on_time'])->get()->toArray();
            //根据日期全部写入临时表
//            foreach ($data_more['date'] as $v) {
//                foreach ($users as $value) {
//                    foreach ($workers as $val) {
//                        DB::table('temp')->insert(['userid' => $value->user_id, 'username' => $value->user_name, 'riqi' => $v,'duty_id'=>$val->id,'duty_name'=>$val->name]);
//                    }
//                }
//            }
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
                  $user_worker_six [] = array_merge($user_worker_C1_C2_D1,$user_worker_E);
              }
              //周日 星期天是两个C1+两个D1+两个E班
              $user_worker_seven = array();
              foreach ($data_more['weeker_seven'] as $value) {
                  $user_worker_C1 = $this->getWorkerdate($value,'C1');
                  $user_worker_D1 = $this->getWorkerdate($value,'D1',$user_worker_C1);
                  $user_worker_C1_D1 = array_merge($user_worker_C1,$user_worker_D1);
                  $user_worker_E     = $this->getWorkerdate($value,'E',$user_worker_C1_D1);
                  $user_worker_seven [] = array_merge($user_worker_C1_D1,$user_worker_E);
              }
              $all = array_merge($user_worker_all,$user_worker_six,$user_worker_seven);
              //计算每个人每天工作，除开休息天数
              $day_user = array();
              $user_temp = array();
              foreach ($all as $key => $value ){
                  if(is_array($value) || is_object($value)){
                      foreach ($value as $k => $val ){
//                          $day_user [$k][$key]['userid']    = $val->userid;
//                          $day_user [$k][$key]['username']  = $val->username;
//                          $day_user [$k][$key]['riqi']      = $val->riqi;
//                          $day_user [$k][$key]['duty_name'] = $val->duty_name;

//                          DB::table('temps')->insert(['username'=>$val->username,'riqi'=>$val->riqi,'type'=>$val->duty_name]);
                      }
                  }
              }

              $temp = array();
              foreach ($users as $val) {
                  $temp [] = DB::table('temps')->select(['username','riqi','type'])->where(['username'=>$val->user_name])->get()->toArray();
              }
//
              print_r($temp);
            //2、计算每个人最后一天的上班情况

            //3、生成每个人每月的排班情况，json格式储存

        }

        //计算每月1日到30/31的天数
        public function getDateFromRange($startdate, $enddate){
            $stimestamp = strtotime($startdate);
            $etimestamp = strtotime($enddate);
            $days = ($etimestamp-$stimestamp)/86400+1;
            $date = array();
            $weeker_six = array();
            $weeker_seven = array();
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
        return  DB::table('temp')->select(['userid','username','riqi','duty_id','duty_name'])->where(['riqi'=>$date,'duty_name'=>$type])
            ->limit(2)->whereNotIn('userid',array_column($uid,'userid'))->inRandomOrder()->get()->toArray();
     }

    /**
     * 导出
     */
    public function exportAll(Request $request,$id = 0){
        $path = str_replace('\\','/',app_path());
        include_once($path .'/Http/PHPExcel/PHPExcel.php');
        $id = $request->input('id');//获取期数
        if(empty($id)){
            return redirect()->back()->withInput()->withErrors('参数错误!');
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('全部学员成绩名单');
        $arrChar = array('A','B','C','D','E','F','G','H','I');
        //设置默认对齐方式
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置默认行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight('20');

        //设置列宽A~Z
        $arrCharWidth = array('10','40','20','40','30','30','10','40','20');
        //importsample
        foreach ($arrChar as $k => $v) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth($arrCharWidth[$k]);
        }
        $style_obj = new \PHPExcel_Style();
        // 设置首行单元格格式
        $style_array = array(
            'borders' => array(
                'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
            ),
            'alignment' => array(
                'horizontal' =>\ PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        );
        $style_obj->applyFromArray($style_array);
        $objPHPExcel->getActiveSheet()->setSharedStyle($style_obj, "A1:I1");
        //设置首行标题填充的样式和背景色
        $objFillLine1 = $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFill();
        $objFillLine1->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objFillLine1->getStartColor()->setARGB('FFC4D79B');
        $i=0;
        $objPHPExcel->getActiveSheet()
            ->setCellValue($arrChar[$i++].'1','姓名')
            ->setCellValue($arrChar[$i++].'1','身份证号码')
            ->setCellValue($arrChar[$i++].'1','电话号码')
            ->setCellValue($arrChar[$i++].'1','学习岗位')
            ->setCellValue($arrChar[$i++].'1','培训机构')
            ->setCellValue($arrChar[$i++].'1','工作单位')
            ->setCellValue($arrChar[$i++].'1','学习状态')
            ->setCellValue($arrChar[$i++].'1','学习课程')
            ->setCellValue($arrChar[$i++].'1','期数');
        $i=2; //第二行开始fill data
        $users = DB::table('users')->select('id','realname','idcard','tel','gangwei','peixunjigou','danwei','status','course_list','qishu')->where('qishu',$id)->get();
        if($users->isEmpty()){
            return redirect()->back()->withInput()->withErrors('没有这期学员！');
        }
        foreach ($users as $key=>$value){
            $gangweis = '';
            $course_lists = '';
            //组装岗位
            if(strpos($value->gangwei,',')){
                $gangwei = DB::table('jobs')->select('name')->whereIn('id',explode(',',$value->gangwei))->get();
                foreach ($gangwei as $v){
                    if(is_object($v) || is_array($v)){
                        $gangweis .= $v->name . ',';
                    }
                }
                $gangweis = implode(',', array_unique(array_filter(explode(',', $gangweis))));
                $users[$key]->gangwei = $gangweis;
            } else{
                $gangweiss  = DB::table('jobs')->select('name')->where('id',$value->gangwei)->first();
                $users[$key]->gangwei = $gangweiss->name;
            }
            //组装课程
            if(strpos($value->course_list,',')){
                $course_list = DB::table('course')->select('name')->whereIn('id',explode(',',$value->course_list))->get();
                foreach ($course_list as $val){
                    if(is_object($val) || is_array($val)){
                        $course_lists .= $val->name . ',';
                    }
                }
                $course_list = implode(',', array_unique(array_filter(explode(',', $course_lists))));
                $users[$key]->course_list = $course_list;
            }else{
                $course_listss  = DB::table('course')->select('name')->where('id',$value->course_list)->first();
                $users[$key]->course_list = $course_listss->name;
            }
            if($value->status == 2){
                $users[$key]->status = '已完成';
            }else{
                $users[$key]->status = '学习中';
            }
            $users[$key]->qishu = '第 ' . $value->qishu . ' 期';
        }
        $j = 0;
        if($users->isEmpty()){
            return redirect()->back()->withInput()->withErrors('没有这期学员！');
        }
        foreach($users as $k=>$v){
            $objPHPExcel->getActiveSheet()
                ->setCellValue($arrChar[$j++].$i, $v->realname)
                ->setCellValue($arrChar[$j++].$i, $v->idcard . "\t")
                ->setCellValue($arrChar[$j++].$i, $v->tel)
                ->setCellValue($arrChar[$j++].$i, $v->gangwei)
                ->setCellValue($arrChar[$j++].$i, $v->peixunjigou)
                ->setCellValue($arrChar[$j++].$i, $v->danwei)
                ->setCellValue($arrChar[$j++].$i, $v->status)
                ->setCellValue($arrChar[$j++].$i, $v->course_list)
                ->setCellValue($arrChar[$j++].$i, $v->qishu);
            $i++;
            $j = 0;
        }
        $objPHPExcel->getActiveSheet()->setTitle('全部学员成绩名单');
        $objPHPExcel->setActiveSheetIndex(0);
        $filename= '第'.$id.'期学员成绩名单-' . date('Y-m-d') . '.xlsx';
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