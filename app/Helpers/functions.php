<?php

/** 弹出内容信息
 * @param $message 弹框信息
 * @param $url 跳转地址,默认后台首页
 */
function reloactionUrlWithMsg($message,$url = '/admin/index'){
    echo '<script>alert("'.$message.'");parent.location.href = "'.$url.'";</script>';
    exit;
}

/**  将数组里面的键值,设定为指定的键值
 * @param $arr       数组
 * @param $field     返回数组的键值
 * @param string $arr_field  需要返回数组的value
 * @return array
 */
function arrayKeyValue($arr,$field,$arr_field = "*"){
    if(is_object($arr)){
        $arr = json_decode(json_encode($arr),true);
    }
    if(empty($arr)){
        reloactionUrlWithMsg('该数组不能为空');
    }
    $return_arr = [];
    foreach($arr as &$value){
        if(is_object($value)){
            $value = json_decode(json_encode($value),true);
        }
       if(!array_key_exists($field,$value)){
           reloactionUrlWithMsg('该数组没有该键'.$field);
       }
       //判断数组中的数据
        if($arr_field == '*'){
            $return_arr[$value[$field]] = $value;
        } else {
            if(!array_key_exists($arr_field,$value)){
                reloactionUrlWithMsg('该数组没有该键'.$arr_field);
            }
            if(is_object($value[$arr_field])){
                $return_arr[$value[$field]] = json_decode(json_encode($value[$arr_field]),true);
            } else {
                $return_arr[$value[$field]] = $value[$arr_field];
            }
        }
    }


    return $return_arr;
}

/** 判断数组是否是一维数组还是多维
 * @param $arr
 * @return bool
 */
 function checkIsManyArr($arr){
    if(count($arr) == count($arr,1)){
        //是
        return true;
    } else {
        //不是
        return false;
    }
}



