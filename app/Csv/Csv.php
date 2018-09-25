<?php
namespace App\Csv;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20
 * Time: 15:32
 * CSV 文件处理类
 *
 * 导出数据到 csv
    $param_arr = array(
    'nav'=>array('用户名','密码','邮箱'),
    array(0=>array('xiaohai1','123456','xiaohai1@zhongsou.com'),
    1=>array('xiaohai2','213456','xiaohai2@zhongsou.com'),
    2=>array('xiaohai3','123456','xiaohai3@zhongsou.com')
    ));
    $column = 3;
    $csv = new Csv($param_arr, $column);
    $csv->export();
 *
 * 从csv导入数据
    $csv = new Csv();
    $path = 'C:\Documents and Settings\Administrator\Temp\txxx.csv';
    $import_arr = $csv->import($path,3);
    var_dump($import_arr);
 */
class Csv{
    public $csv_array; //csv数组数据
    public $column; //导入列数
    public function __construct($param_arr='', $column=''){
        $this->csv_array = $param_arr;
        $this->column = $column;
    }
    /**
     * 导出
     * */
    public function export(){
        if(empty($this->csv_array) || empty($this->column)){
            return false;
        }
        $param_arr = $this->csv_array;
        unset($this->csv_array);
        $export_str = implode(',',$param_arr['nav'])."\n";
        unset($param_arr['nav']);
        //组装数据
        foreach($param_arr as $k=>$v){
            foreach($v as $k1=>$v1){
                $export_str .= implode(',',$v1)."\n";
            }
        }
        //将$export_str导出
        header( "Cache-Control: public" );
        header( "Pragma: public" );
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=txxx.csv");
        header('Content-Type:APPLICATION/OCTET-STREAM');
        ob_start();
        // $file_str= iconv("utf-8",'gbk',$export_str);
        ob_end_clean();
        echo $export_str;
    }

    /**
     * 导入
     * @param $path 文件路径
     * @param $fileName  文件名称
     * @param int $column  导入列数
     * @return array
     */
    public function import($path, $fileName, $column = 3){
        $filesize = 20; //20MB
        $maxsize = $filesize * 1024 * 1024;
        $max_column = 10000; //最大行数

        //检测文件是否存在
        if(!file_exists($path)){
            return array('code' => -1, 'message' => '文件不存在');
        }
        //检测文件格式
        $ext = strtolower(trim(explode('.', $fileName)[1]));
        if($ext != 'csv'){
            return array('code' => -1, 'message' => '只能导入CSV格式文件');
        }
        //检测文件大小
        if(filesize($path)>$maxsize){
            return array('code' => -1, 'message' => '导入的文件不得超过'.$filesize.'MB文件');
        }
        //读取文件
        $row = 0;
        $handle = fopen($path,'r');
        $dataArray = array();
        while($data = fgetcsv($handle,$max_column,",")){
            for($i = 0; $i < $column; $i++){
                if($row == 0){
                    break;
                }
                if (!@$data[$i]) {
                    continue;
                }
                //组建数据
                $dataArray[$row][$i] = mb_convert_encoding($data[$i], "UTF-8", "GBK");
            }
            $row++;
        }
        return array('code' => 1, 'data' => $dataArray);
    }
}
?>