<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 16:19
 */

namespace App\Http\Controllers\Admin;
use App\Http\Model\Shift;
use Illuminate\Http\Request;

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

    public function editPost(Request $request,Shift $shift)
    {
        $change_id = $request->post('change_id');
        $type = $request->post('type');
        $user_id = $request->post('user_id');
       if($shift->checkRst($change_id, $type, $user_id)){
           echo '<script>alert("修改成功");parent.location.href = "/admin/shift/setting";</script>';
         exit;
       } else {
           return redirect("/admin/shift/setting")-> withErrors('修改失败');
       }


    }

}
