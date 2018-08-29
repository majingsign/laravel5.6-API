<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 13:26
 */

namespace App\Http\Controllers\Admin;


use App\Http\Model\City;
use App\Http\Model\Member;
use App\Http\Model\UserCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberControler extends AdminBaseController {


    /**
     * 全部员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function list (Request $request){
        $username   = $request->input('username');
        $userstatus = $request->input('userstatus');
        $member = new Member();
        $list = $member->memberList($username,$userstatus);
        return view('admin.user.list',['list'=>$list,'username'=>$username,'status'=>$this->status,'userstatus'=>$userstatus]);
    }

    /**
     * 新增员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add () {
        $city = new City();
        $list = $city->cityList();
        return view('admin.user.add',['list'=>$list,'type'=>$this->type]);
    }

    /**
     * 保存新增员工
     * @param Request $request
     */
    public function addMember (Request $request) {
        $username = $request->input('username');
        $type = $request->input('type');
        $cityid = $request->input('city');
        if($username == '' || empty($username)){
            return ['code'=>0,'msg'=>'昵称为空'];
        }
        if($type == '' || empty($type)){
            return ['code'=>0,'msg'=>'排班类型未选择'];
        }
        if($cityid == '' || empty($cityid)){
            return ['code'=>0,'msg'=>'省份未选择'];
        }
        $times = time();
        $member = new Member();
        //城市的名字
        $citys = new City();
        $cityname = $citys->findCityId($cityid);
        if(empty($cityname)){
            return ['code'=>0,'msg'=>'省份不存在'];
        }
        //员工的姓名
        $userid = $member->memberAddId(['user_name'=>$username,'input_time'=>$times,'create_time'=>$times,'duty_type'=>$type,'is_del'=>0]);
        $usercity = new UserCity();
        if($usercity->addUserCity(['user_id'=>$userid,'city_id'=>$cityid,'user_name'=>$username,'city_name'=>$cityname->name])){
            return ['code'=>200,'msg'=>'新增成功'];
        }else{
            return ['code'=>0,'msg'=>'新增失败'];
        }
    }

    /**
     * 编辑员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit(Request $request) {
        $userid = $request->input('userid');
        $member = new Member();
        $user = $member->memberFindId($userid);
        $city = new City();
        $list = $city->cityList();
        $usercity = new UserCity();
        $usercy = $usercity->findUserCityId($userid);
        $usercys = array();
        if(!empty($usercy)){
            foreach ($usercy as $value){
                $usercys [] = $value->city_id;
            }
        }
        $usercys = array_unique($usercys);
        return view('admin.user.edit',['list'=>$list,'type'=>$this->type,'user'=>$user,'usercy'=>$usercys]);
    }

    /**
     * 保存编辑员工
     * @param Request $request
     */
    public function editMember (Request $request) {
        $user_id       = $request->input('id');
        $username = $request->input('username');
        $type     = $request->input('type');
        $cityid   = $request->input('city');
        $leve_time= $request->input('leve_time');
        if($username == '' || empty($username)){
            return ['code'=>0,'msg'=>'昵称为空'];
        }
        if($type == '' || empty($type)){
            return ['code'=>0,'msg'=>'排班类型未选择'];
        }
        if($cityid == '' || empty($cityid)){
            return ['code'=>0,'msg'=>'省份未选择'];
        }

        $member = new Member();
        $citys = new City();
        //员工的姓名
        if(empty($leve_time)){
            $member->memberEdit($user_id,['user_name'=>$username,'duty_type'=>$type,'is_del'=>0]);
        }else{
            $member->memberEdit($user_id,['user_name'=>$username,'duty_type'=>$type,'is_del'=>0,'leve_time'=>strtotime($leve_time)]);
        }
        $usercity = new UserCity();
        foreach ($cityid as $v) {
            if(!$usercity->findUserCityIdFirst($user_id,$v)){
                $usercity->addUserCity(['user_id'=>$user_id,'city_id'=>$v,'user_name'=>$username,'city_name'=>$citys->findCityNameId($v)]);
            }
        }
        return ['code'=>200,'msg'=>'修改成功'];
    }

    /**
     * 删除员工
     * @param Request $request
     */
    public function del (Request $request) {
        $userid = $request->input('id');
        if(empty($userid)){
            return ['code'=>0,'msg'=>'参数错误'];
        }
        DB::beginTransaction();
        $member = new Member();
        if(!$member->memberDel($userid)){
            DB::rollBack();
            return ['code'=>0,'msg'=>'删除失败'];
        }
        $usercity = new UserCity();
        $usercity->delUserid($userid);
        DB::commit();
        return ['code'=>200,'msg'=>'删除成功'];
    }
}