<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 14:13
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\City;
use App\Http\Model\UserCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends AdminBaseController {

    /**
     * 城市列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function list () {
        $city = new City();
        $list = $city->cityList();
        return view('admin.city.list',['list'=>$list]);
    }

    /**
     * 新增城市模板
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add () {
        return view('admin.city.add');
    }

    /**
     * 保存城市
     * @param Request $request
     * @return array
     */
    public function addCity (Request $request) {
        $cityname  = $request->input('cityname');
        $workernum = $request->input('workernum');
        if($cityname == '' || empty($cityname)){
            return ['code'=>0,'msg'=>'省份不能为空!'];
        }
        if($workernum == '' || empty($workernum)){
            return ['code'=>0,'msg'=>'值班人数不能为空!'];
        }
        $city = new City();
        if($city->cityName($cityname)){
            return ['code'=>0,'msg'=>'省份已存在!'];
        }
        if($city->cityAdd(['name'=>$cityname,'pid'=>0,'min_num'=>$workernum])){
            return ['code'=>200,'msg'=>'省份添加成功!'];
        }else{
            return ['code'=>0,'msg'=>'省份添加失败!'];
        }
    }

    /**
     * 编辑省份
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit (Request $request) {
        $id = $request->input('id');
        $city = new City();
        $citys = $city->cityEdit($id);
        return view('admin.city.edit',['citys'=>$citys]);
    }

    /**
     * 保存城市
     * @param Request $request
     * @return array
     */
    public function saveCity (Request $request) {
        $id        = $request->input('id');
        $cityname  = $request->input('cityname');
        $workernum = $request->input('workernum');
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误!'];
        }
        if($cityname == '' || empty($cityname)){
            return ['code'=>0,'msg'=>'省份不能为空!'];
        }
        if($workernum == '' || empty($workernum)){
            return ['code'=>0,'msg'=>'值班人数不能为空!'];
        }
        $city = new City();
        //判断城市名称是否更改
        $cityNames = $city->cityEdit($id);
        if($cityNames->name == $cityname){
            if($city->citySave($id,['name'=>$cityname,'min_num'=>$workernum])){
                return ['code'=>200,'msg'=>'修改成功!'];
            }else{
                return ['code'=>0,'msg'=>'修改失败!'];
            }
        }else{
            if($city->cityName($cityname)){
                return ['code'=>0,'msg'=>'省份已存在'];
            }
        }
    }


    /**
     * 删除省份
     * @param Request $request
     * @return array
     */
    public function del (Request $request) {
        $id = $request->input('id');
        if($id == '' || empty($id)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        $city = new City();
        DB::beginTransaction();
        if(!$city->cityDel($id)) {
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        //删除相关连的用户城市
        $user_city = new UserCity();
        if(!$user_city->delUserCityId($id)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        DB::commit();
        return ['code'=>200,'msg'=>'已删除'];
    }


}