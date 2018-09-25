<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:25
 */

namespace App\Http\Model;


use Illuminate\Support\Facades\DB;

class Qingjia {

    private  $table = 'qingjia';


    /**
     * 查看全部的请假
     * @param int $com_id  公司id
     * @param int $depart_id  部门id
     */
    public function qjList($com_id = 0 ,$depart_id = 0,$userid = 0){
        $day_end    = date("t");   //当月有多少天
        $start_time = date('Y-m') . '-1';
        $end_time   = date('Y-m') . '-' . $day_end;
        $query = DB::table($this->table)->select(['id','userid','username','bindid','comid','departid','type','start_time','end_time','long_time','desc','is_pass','admin_id','pass_time','create_at']);
        if($com_id != 0){
            $query->where(['comid'=>$com_id]);
        }else{
            if($depart_id != 0){
                $query->where(['departid'=>$depart_id]);
            }
        }
        if($userid != 0){
            $query->where(['userid'=>$userid]);
            $lists = $this->common($userid);
            if($lists) {
                $ids = array();
                foreach ($lists as $value) {
                    $ids [] = $value->bindid;
                }
                $query->whereIn('id',$ids);
            }
        }
        $list = $query->whereBetween('create_at',[strtotime($start_time),strtotime($end_time)])->orderBy('id','desc')->get()->toArray();
        if($list){
            foreach ($list as $key => $val) {
                $list[$key]->end_time = DB::table($this->table)->where(['bindid'=>$val->id])->orderBy('id','desc')->value('end_time');
            }
        }
        return $list;
    }

    /**
     * 查看全部未处理的通知
     * @param int $com_id  公司id
     * @param int $depart_id  部门id
     */
    public function qjListNoPass($com_id = 0 ,$depart_id = 0){
        $day_end    = date("t");   //当月有多少天
        $start_time = date('Y-m') . '-1';
        $end_time   = date('Y-m') . '-' . $day_end;
        $query = DB::table($this->table)->select(['id','userid','username','comid','departid','type','start_time','end_time','long_time','desc','is_pass','admin_id','pass_time','create_at']);
        if($com_id != 0){
            $query->where(['comid'=>$com_id]);
        }else{
            if($depart_id != 0){
                $query->where(['departid'=>$depart_id]);
            }
        }
        $lists = $this->common();
        if($lists) {
            $ids = array();
            foreach ($lists as $value) {
                $ids [] = $value->bindid;
            }
            $query->whereIn('id',$ids);
        }
        $list = $query->whereBetween('create_at',[strtotime($start_time),strtotime($end_time)])->where(['is_pass'=>0])->orderBy('id','desc')->get()->toArray();
        if($list){
            foreach ($list as $value){
                if($value->is_pass == 0){
                    $value->com_name = DB::table('company')->where(['id'=>$value->comid])->value('name');
                    $value->depart_name = DB::table('department')->where(['id'=>$value->departid])->value('name');
                    $value->end_time = DB::table($this->table)->where(['bindid'=>$value->id])->orderBy('id','desc')->value('end_time');
                }
            }
        }else{
            return null;
        }
        return $list;
    }


    /**
     * 根据id更新
     * @param $id
     * @param $data
     * @return int
     */
    public function updateQjId($id,$data){
        return DB::table($this->table)->where(['id'=>$id])->update($data);
    }


    /**
     * 同意请假   更新bindid
     * @param $id
     * @param $data
     */
    public function updateQj($id,$data){
        return DB::table($this->table)->where(['bindid'=>$id])->update($data);
    }

    /**
     * 申请请假
     * @param $data
     */
    public function qjAdd($data){
        return DB::table($this->table)->insertGetId($data);
    }



    /**
     * 根据id查看请假情况
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function qjPassId($id){
        return DB::table($this->table)->select(['id','userid','username','comid','departid','type','start_time','end_time','long_time','desc','is_pass','admin_id','pass_time','create_at'])->where(['id'=>$id])->first();
    }

    /**
     * 根据id查看请假情况
     * @param $userid
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function qjPassUser($userid){
        $list = $this->common($userid);
        if($list){
            $ids = array();
            foreach ($list as $value){
                $ids [] = $value->bindid;
            }
            $qingjia = DB::table($this->table)->select(['id','userid','username','comid','departid','type','start_time','end_time','long_time','desc','is_pass','admin_id','pass_time','create_at'])
                ->where(['userid'=>$userid])->whereIn('id',$ids)->orderBy('id','desc')->get()->toArray();
            if($qingjia){
                foreach ($qingjia as $key => $val) {
                    $qingjia[$key]->end_time = DB::table($this->table)->where(['bindid'=>$val->id])->orderBy('id','desc')->value('end_time');
                }
            }
            return $qingjia;
        }else{
            return null;
        }
    }

    /**
     * 查询公共的方法
     * @param $userid
     */
    public function common($userid = 0){
        if($userid == 0){
            $list = DB::select("SELECT  DISTINCT `bindid` FROM yxkj_qingjia");
        }else{
            $list = DB::select("SELECT  DISTINCT `bindid` FROM yxkj_qingjia WHERE userid = " . $userid);
        }
        return $list;
    }


}