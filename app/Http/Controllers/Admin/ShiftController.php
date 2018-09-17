<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 16:19
 */

namespace App\Http\Controllers\Admin;
use App\Http\Model\Member;
use App\Http\Model\Shift;
use Illuminate\Http\Request;
use App\Logic\ShiftLogic;
use App\Logic\UserWork;
use Illuminate\Support\Facades\DB;

/**  轮班最后一天内容设置
 * Class ShiftController
 * @package App\Http\Controllers\Admin
 */
class ShiftController extends AdminBaseController{
    /**  列表
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Shift $shift){
      $list = $shift -> getList();
      return view('admin.shift.index',[
          'list' => $list
      ]);
    }

    public function edit(Request $request){
        $change_id = $request -> get('id',0);
        $user_name = $request -> get('user_name');
        $type = $request -> get('type');
        $user_id = $request -> get('user_id');
        return view('admin.shift.edit',[
            'change_id' => $change_id,
            'user_name' => $user_name,
            'type' => $type,
            'user_id' => $user_id
        ]);
    }

    public function editPost(Request $request,Shift $shift,Member $member,UserWork $userWork)
    {
        $change_id = $request->post('change_id');
        $type = $request->post('type');
        $user_id = $request->post('user_id');
        //判断最后一天是否离职
        $list = $member -> memberFindId($user_id);
        $time = $userWork -> getCurrentLastDay();
        if($list -> leve_time <= strtotime($time) && $list -> leve_time != 0 ){
            $str = $list -> user_name . '在本月最后一天前离职,不能进行修改';
            echo '<script>alert("'.$str.'");parent.location.href = "/admin/shift/setting";</script>';
            exit;
        }
        if($shift->checkRst($change_id, $type, $user_id)){
           echo '<script>alert("修改成功");parent.location.href = "/admin/shift/setting";</script>';
         exit;
       } else {
           return redirect("/admin/shift/setting")-> withErrors('修改失败');
       }


    }

    /**
     * 生成本月的轮班最后一天内容
     */
    public function sysSetting(ShiftLogic $shiftLogic){
        $table_name  =  $shiftLogic -> getTableName(date('Y_m',time()));
        $list = DB::table($table_name) -> get() -> toArray();
        $next_month = date('Y-m',time());
        $add_all = [];
        if(count($list) > 0){
            foreach($list as $value){
                $add_all[$value -> user_id]['last_month_dat_type'] = $value -> last_day;
                $add_all[$value -> user_id]['user_id'] = $value -> user_id;
                $add_all[$value -> user_id]['year_month'] = $next_month;
            }
        } else {
            die(json_encode(['code' => 0 , 'message' => '本月的排班数据为空,无法自动更新']));
        }
        DB::beginTransaction();
        $rst1 = DB::table('shift_last_month_day') -> where('year_month','=',date('Y-m',time())) -> delete();
        $rst2 = DB::table('shift_last_month_day') -> insert($add_all);

        if($rst1 !== false  && $rst2){
            DB::commit();
          die(json_encode(['code' => 1, 'message' => '数据已更新']));
        }
        DB::Rollback();
        die(json_encode(['code' => 0 , 'message' => '系统更新数据失败']));
    }


}
