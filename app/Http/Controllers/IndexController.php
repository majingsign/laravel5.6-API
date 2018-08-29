<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 13:50
 */

namespace App\Http\Controllers;


class IndexController extends Controller
{
        public function index () {
            echo bcrypt(1111);
        }
}