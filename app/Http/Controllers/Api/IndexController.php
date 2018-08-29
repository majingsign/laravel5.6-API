<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 15:28
 */

namespace App\Http\Controllers\Api;

class IndexController extends ApiBaseController
{
    public function index () {
        return $this->apiSuccess(200,'成功');
    }

    public function fail () {
        return $this->apiFail(0,'失败');
    }
}