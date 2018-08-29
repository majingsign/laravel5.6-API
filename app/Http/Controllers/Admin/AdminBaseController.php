<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 13:31
 */

namespace  App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/**
 *
 * Class AdminBaseController
 * @package App\Http\Controllers\Admin
 */
class AdminBaseController extends Controller {

    protected $type   = ['1'=>'轮休','2'=>'倒班'];
    protected $status = ['1'=>'在职','2'=>'离职'];

    /**
     * 检测用户是否登陆
     */
    public function checkUser () {
        $username = Session::get('username');
        if($username == null || empty($username) || $username == NULL){
            return false;
        }else{
            return true;
        }
    }

}