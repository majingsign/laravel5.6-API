<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 16:01
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class ApiBaseController extends Controller {


    /**
     * 返回成功
     * @param int $code
     * @param array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSuccess ($code = 200 , $msg = '',$data = array()) {
        return $this->resposeJson($code,$msg,$data);
    }

    /**
     * 返回失败
     * @param int $code
     * @param array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiFail ($code = 0, $msg = '', $data = array()){
        return $this->resposeJson($code,$msg,$data);
    }

    /**
     * 返回json格式类
     * @param $code
     * @param $array
     * @return \Illuminate\Http\JsonResponse
     */
    protected function resposeJson ($code , $msg ,$data = array()) {
        $arrJson = array('code'=>$code,'msg'=>'系统错误');
        if($msg != null || !empty($msg)){
            $arrJson ['msg'] = $msg;
        }
        $arrJson ['data'] = $data;
        return response()->json($arrJson);
    }
}