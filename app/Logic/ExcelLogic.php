<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 15:59
 */

namespace app\Logic;


class ExcelLogic
{

    /**  导出数据
     * @param $title  导出的标题
     * @param $tableheader  导出的头信息(键值从0开始)
     * @param $data  导出的内容(键值从0开始)
     */
    public function exportAll($title,$tableheader,$data){
        $path = str_replace('\\','/',app_path());
        include_once($path .'/Http/PHPExcel/PHPExcel.php');
        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        $excel->getActiveSheet()->setTitle($title);

        //创建Excel输入对象
        $objWriter=\PHPExcel_IOFactory::createWriter($excel,'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition: attachment;filename="'.$title.'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
        exit;
    }


}
