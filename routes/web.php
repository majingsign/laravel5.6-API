<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});
Route::get('/user/index','\App\Http\Controllers\IndexController@index');

Route::get('/index/index','\App\Http\Controllers\Index\IndexController@index');


/*********************************后台路由开始***********************************************/
//首页
Route::get('/admin/index','\App\Http\Controllers\Admin\IndexController@index')->name('admin.index');
//欢迎页
Route::any('/admin/welcome','\App\Http\Controllers\Admin\IndexController@welcome')->name('admin.welcome');
//登陆
Route::any('/admin/login','\App\Http\Controllers\Admin\LoginController@login')->name('admin.login');
//退出
Route::any('/admin/loginout','\App\Http\Controllers\Admin\LoginController@loginout')->name('admin.loginout');
//登陆控制器
Route::post('/admin/loginAction','\App\Http\Controllers\Admin\LoginController@loginAction')->name('admin.loginAction');
//管理员列表
Route::get('/admin/admin/list','\App\Http\Controllers\Admin\AdminController@list')->name('admin.admin.list');
//修改密码
Route::any('/admin/admin/editpwd','\App\Http\Controllers\Admin\AdminController@editpwd')->name('admin.admin.editpwd');
//保存密码
Route::any('/admin/admin/savepwd','\App\Http\Controllers\Admin\AdminController@savepwd')->name('admin.admin.savepwd');
//添加管理员
Route::any('/admin/admin/add','\App\Http\Controllers\Admin\AdminController@add')->name('admin.admin.add');
//保存管理员
Route::any('/admin/admin/addAdmin','\App\Http\Controllers\Admin\AdminController@addAdmin')->name('admin.admin.addAdmin');
Route::any('/admin/admin/del','\App\Http\Controllers\Admin\AdminController@del')->name('admin.admin.del');


//员工管理
Route::any('/admin/member/list','\App\Http\Controllers\Admin\MemberControler@list')->name('admin.member.list');
Route::any('/admin/member/add','\App\Http\Controllers\Admin\MemberControler@add')->name('admin.member.add');
Route::any('/admin/member/edit','\App\Http\Controllers\Admin\MemberControler@edit')->name('admin.member.edit');
Route::any('/admin/member/del','\App\Http\Controllers\Admin\MemberControler@del')->name('admin.member.del');
Route::any('/admin/member/addMember','\App\Http\Controllers\Admin\MemberControler@addMember')->name('admin.member.addMember');
Route::any('/admin/member/editMember','\App\Http\Controllers\Admin\MemberControler@editMember')->name('admin.member.editMember');


//城市管理
Route::any('/admin/city/list','\App\Http\Controllers\Admin\CityController@list')->name('admin.city.list');
Route::any('/admin/city/add','\App\Http\Controllers\Admin\CityController@add')->name('admin.city.add');
Route::any('/admin/city/edit','\App\Http\Controllers\Admin\CityController@edit')->name('admin.city.edit');
Route::any('/admin/city/del','\App\Http\Controllers\Admin\CityController@del')->name('admin.city.del');
Route::any('/admin/city/addCity','\App\Http\Controllers\Admin\CityController@addCity')->name('admin.city.addCity');
Route::any('/admin/city/saveCity','\App\Http\Controllers\Admin\CityController@saveCity')->name('admin.city.saveCity');

//排班类型管理
Route::any('/admin/duty/list','\App\Http\Controllers\Admin\DutyController@list')->name('admin.duty.list');
Route::any('/admin/duty/add','\App\Http\Controllers\Admin\DutyController@add')->name('admin.duty.add');
Route::any('/admin/duty/edit','\App\Http\Controllers\Admin\DutyController@edit')->name('admin.duty.edit');
Route::any('/admin/duty/del','\App\Http\Controllers\Admin\DutyController@del')->name('admin.duty.del');
Route::any('/admin/duty/addDuty','\App\Http\Controllers\Admin\DutyController@addDuty')->name('admin.duty.addDuty');
Route::any('/admin/duty/saveDuty','\App\Http\Controllers\Admin\DutyController@saveDuty')->name('admin.duty.saveDuty');


/**
 * 排班管理
 */
//轮班最后一天内容设置(列表)
Route::get('/admin/shift/setting','\App\Http\Controllers\Admin\ShiftController@index')->name('admin.shift.setting');
//轮班最后一天内容设置(修改)
Route::get('/admin/shift/edit','\App\Http\Controllers\Admin\ShiftController@edit')->name('admin.shift.edit');
//轮班最后一天内容设置(修改)
Route::any('/admin/shift/editpost','\App\Http\Controllers\Admin\ShiftController@editPost')->name('admin.shift.editpost');


/*********************************后台路由结束***********************************************/
