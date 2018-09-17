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
    return view('index.index.member');
});
Route::get('/user/index','\App\Http\Controllers\IndexController@index');


//员工打卡首页
Route::get('/index/index/index','\App\Http\Controllers\Index\IndexController@index')->name('index.index.index');
Route::post('/index/index/memberLogin','\App\Http\Controllers\Index\IndexController@memberLogin')->name('index.index.memberLogin');


/*********************************后台路由开始***********************************************/
Route::group(['middleware' => 'admin.CheckMenu'],function() {
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
    Route::any('/admin/admin/ajaxDepart','\App\Http\Controllers\Admin\AdminController@ajaxDepart')->name('admin.admin.ajaxDepart');
//保存管理员
    Route::any('/admin/admin/addAdmin','\App\Http\Controllers\Admin\AdminController@addAdmin')->name('admin.admin.addAdmin');
    Route::any('/admin/admin/del','\App\Http\Controllers\Admin\AdminController@del')->name('admin.admin.del');

//错误页面
    Route::get('/admin/error/index', function () {
        return view('admin.error.index');
    })->name('admin.error.index');

//菜单权限管理
    Route::any('/admin/menu/list','\App\Http\Controllers\Admin\MenuController@list')->name('admin.menu.list');
    Route::any('/admin/menu/add','\App\Http\Controllers\Admin\MenuController@add')->name('admin.menu.add');
    Route::any('/admin/menu/edit','\App\Http\Controllers\Admin\MenuController@edit')->name('admin.menu.edit');
    Route::any('/admin/menu/del','\App\Http\Controllers\Admin\MenuController@del')->name('admin.menu.del');
    Route::any('/admin/menu/addMenu','\App\Http\Controllers\Admin\MenuController@addMenu')->name('admin.menu.addMenu');
    Route::any('/admin/menu/saveMenu','\App\Http\Controllers\Admin\MenuController@saveMenu')->name('admin.menu.saveMenu');

//部门管理
    Route::any('/admin/depart/list','\App\Http\Controllers\Admin\DepartController@list')->name('admin.depart.list');
    Route::any('/admin/depart/add','\App\Http\Controllers\Admin\DepartController@add')->name('admin.depart.add');
    Route::any('/admin/depart/edit','\App\Http\Controllers\Admin\DepartController@edit')->name('admin.depart.edit');
    Route::any('/admin/depart/del','\App\Http\Controllers\Admin\DepartController@del')->name('admin.depart.del');
    Route::any('/admin/depart/addDepart','\App\Http\Controllers\Admin\DepartController@addDepart')->name('admin.depart.addDepart');
    Route::any('/admin/depart/saveDepart','\App\Http\Controllers\Admin\DepartController@saveDepart')->name('admin.depart.saveDepart');
    Route::any('/admin/depart/show','\App\Http\Controllers\Admin\DepartController@show')->name('admin.depart.show');


//员工管理
    Route::any('/admin/member/list','\App\Http\Controllers\Admin\MemberController@list')->name('admin.member.list');
    Route::any('/admin/member/add','\App\Http\Controllers\Admin\MemberController@add')->name('admin.member.add');
    Route::any('/admin/member/edit','\App\Http\Controllers\Admin\MemberController@edit')->name('admin.member.edit');
    Route::any('/admin/member/del','\App\Http\Controllers\Admin\MemberController@del')->name('admin.member.del');
    Route::any('/admin/member/addMember','\App\Http\Controllers\Admin\MemberController@addMember')->name('admin.member.addMember');
    Route::any('/admin/member/editMember','\App\Http\Controllers\Admin\MemberController@editMember')->name('admin.member.editMember');
    Route::any('/admin/member/ajaxMemberDepart','\App\Http\Controllers\Admin\MemberController@ajaxMemberDepart')->name('admin.member.ajaxMemberDepart');
    Route::any('/admin/member/records','\App\Http\Controllers\Admin\MemberController@records')->name('admin.member.records');
    Route::any('/admin/member/recordsList','\App\Http\Controllers\Admin\MemberController@recordsList')->name('admin.member.recordsList');

    //公司管理
    Route::any('/admin/company/list','\App\Http\Controllers\Admin\CompanyController@list')->name('admin.company.list');
    Route::any('/admin/company/add','\App\Http\Controllers\Admin\CompanyController@add')->name('admin.company.add');
    Route::any('/admin/company/edit','\App\Http\Controllers\Admin\CompanyController@edit')->name('admin.company.edit');
    Route::any('/admin/company/del','\App\Http\Controllers\Admin\CompanyController@del')->name('admin.company.del');
    Route::any('/admin/company/addCompany','\App\Http\Controllers\Admin\CompanyController@addCompany')->name('admin.company.addCompany');
    Route::any('/admin/company/saveCompany','\App\Http\Controllers\Admin\CompanyController@saveCompany')->name('admin.company.saveCompany');

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
    //生成本月的轮班最后一天内容
    Route::any('/admin/shift/syssetting','\App\Http\Controllers\Admin\ShiftController@sysSetting')->name('admin.shift.syssetting');
//轮休当月排班列表
    Route::get('admin/rotation/index','\App\Http\Controllers\Admin\RotationController@index') ->name('admin.rotation.index');
   //判断生成下个月轮休排班系统的逻辑
    Route::get('admin/rotation/checkgenerate','\App\Http\Controllers\Admin\RotationController@checkGenerate') -> name('admin.rotation.checkgenerate');
    //生成下个月的排班系统内容
    Route::get('admin/rotation/shift','\App\Http\Controllers\Admin\RotationController@shift') ->name('admin.rotation.shift');
    //保存下个月/当前月的轮休排班系统
    Route::post('admin/rotation/shiftsave','\App\Http\Controllers\Admin\RotationController@shiftSave') ->name('admin.rotation.shiftsave');
    //查看下个月的轮休内容
    Route::get('admin/rotation/seenextgenerate','\App\Http\Controllers\Admin\RotationController@seeNextGenerate') -> name('admin.rotation.seenextgenerate');
    //导出本月轮班内容
    Route::get('admin/rotation/currexcelport','\App\Http\Controllers\Admin\RotationController@currExcelPort') ->name('admin.rotation.currexcelport');
    //导出下月轮班内容
    Route::get('admin/rotation/nextrexcelport','\App\Http\Controllers\Admin\RotationController@nextRexcelPort') ->name('admin.rotation.nextrexcelport');
    //废除下个月的轮休排班数据
    Route::get('admin/rotation/discardedgenerate','\App\Http\Controllers\Admin\RotationController@discardedGenerate') ->name('admin.rotation.discardedgenerate');



    /**
     * 倒班管理
     */
    //倒班最后一天的设置内容(列表)
    Route::get('/admin/inverted/setting','\App\Http\Controllers\Admin\InvertedController@index')->name('admin.inverted.setting');
    //倒班最后一天内容设置(修改)
    Route::get('/admin/inverted/edit','\App\Http\Controllers\Admin\InvertedController@edit')->name('admin.inverted.edit');
    //倒班最后一天内容设置(修改)
    Route::any('/admin/inverted/editpost','\App\Http\Controllers\Admin\InvertedController@editPost')->name('admin.inverted.editpost');
    //生成本月的倒班最后一天内容
    Route::any('/admin/inverted/syssetting','\App\Http\Controllers\Admin\InvertedController@sysSetting')->name('admin.inverted.syssetting');
    //倒班当月排班列表
    Route::get('admin/changeinverted/index','\App\Http\Controllers\Admin\ChangeInvertedController@index') ->name('admin.changeinverted.index');
    //判断生成下个月倒班排班系统的逻辑
    Route::get('admin/changeinverted/checkgenerate','\App\Http\Controllers\Admin\ChangeInvertedController@checkGenerate') -> name('admin.changeinverted.checkgenerate');
    //生成下个月的倒班系统内容
    Route::get('admin/changeinverted/shift','\App\Http\Controllers\Admin\ChangeInvertedController@shift') ->name('admin.changeinverted.shift');
    //保存下个月/当前月的倒班排班系统
    Route::post('admin/changeinverted/shiftsave','\App\Http\Controllers\Admin\ChangeInvertedController@shiftSave') ->name('admin.changeinverted.shiftsave');
    //查看下个月已经生成的倒班
    Route::get('admin/changeinverted/seenextgenerate','\App\Http\Controllers\Admin\ChangeInvertedController@seeNextGenerate') -> name('admin.changeinverted.seenextgenerate');
    //导出本月倒班内容
    Route::get('admin/changeinverted/currexcelport','\App\Http\Controllers\Admin\ChangeInvertedController@currExcelPort') ->name('admin.changeinverted.currexcelport');
    //导出下月倒班内容
    Route::get('admin/changeinverted/nextrexcelport','\App\Http\Controllers\Admin\ChangeInvertedController@nextRexcelPort') ->name('admin.changeinverted.nextrexcelport');
    //废除下个月的倒班排班数据
    Route::get('admin/changeinverted/discardedgenerate','\App\Http\Controllers\Admin\ChangeInvertedController@discardedGenerate') ->name('admin.changeinverted.discardedgenerate');
    /*********************************后台路由结束***********************************************/
});



