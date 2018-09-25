<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 16:25
 */

namespace App\Http\Controllers\Index;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class IndexBaseController extends Controller {

    protected $qingjia= ['1'=>'事假','2'=>'病假','3'=>'婚假','4'=>'产假','5'=>'丧假','6'=>'其他'];
    /**
     * 检测员工是否登陆
     */
    public function checkMember(){
        $username = Session::get('membername');
        if($username == null || empty($username) || $username == NULL){
            return false;
        }else{
            return true;
        }
    }
}