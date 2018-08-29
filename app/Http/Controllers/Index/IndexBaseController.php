<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 16:25
 */

namespace App\Http\Controllers\Index;


use App\Http\Controllers\Controller;

class IndexBaseController extends Controller {


    public function returns (){
        echo "<script type='text/javascript'>alert('生成失败，请重新生成!');</script>";
        exit;
    }
}