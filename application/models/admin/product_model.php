<?php
require_once ('application/core/MY_Model.php');

class product_model extends MY_Model
{

    public $table_name = "user_project";
    
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    up.`id`,
                    up.`user_id`,
                    up.`project_type`,
                    up.`project_num`,
                    up.`status`,
                    up.`add_time`,
                    ui.`username`,
                    ui.`mobile`,
                    ui.`user_id`
                FROM
                    `user_project` as up
                Left Join
                    `user_info` as ui
                On
                    up.user_id = ui.user_id
            " .$this->getSearch($search);
        
        $sql .= " order by  `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        return $result;
    }
    
    public function count($search){
         $sql = "
                SELECT
                    count(up.id) as total,
                    up.`id`,
                    up.`user_id`,
                    up.`project_type`,
                    up.`project_num`,
                    up.`add_time`,
                    up.`status`,
                    ui.`username`,
                    ui.`mobile`,
                    ui.`user_id`
                FROM
                    `user_project` as up
                Left Join
                    `user_info` as ui
                On
                    up.user_id = ui.user_id
            " .$this->getSearch($search);
        return $this->queryOne($sql);
    }
    
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['key'] != '' ){
                $where .= "and up.`project_num` like('%" . $search['key']."%') or ui.`username` like('%" . $search['key']."%')  or ui.`mobile` like('%" . $search['key']."%')"  ;
            }
            if($search['project_type'] != '' ){
                $where .= "and up.`project_type` = '".$search['project_type']."'" ;
            }
            $where = trim($where,'and');
            $where = $where?' WHERE ' .$where:'' ;
        }
        return $where;
    }
    
    public function delete($id){
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
        //echo $this->db->last_query();
    }
    public function getData(){
        $sql = "
            SELECT
                   u.`id`,
                    u.`status`,
                    user_project.`project_num`,
                    e.`id` as `eid`,
                    e.`ename`,
                    e.`ephone`,
                    e.`eaddress`,
                    e.`time`,
                    e.`exname`,
                    e.`exnum`,
                    e.`updatetime`,
                    e.`fang_status`,
                    e.`status`
                FROM
                     `express` as e
                LEFT JOIN 
                  user_project
                ON 
                  user_project.`id` = e.`pid`
                LEFT JOIN
                 `user` as u
                ON
                    u.`id` = e.`uid` 
                AND 
                   user_project.`user_id` = u.`id` 
                WHERE 
                    u.`status` = 4
            " ;
        $sql .= " order by `time` desc ";
        return  $this->query($sql);
    }
    /*
     * 导出
     * 2018-07-20
     */
    public function getExport($id){
        $sql = "
            SELECT
                    e.`ename`,
                    e.`ephone`,
                    user_project.`project_num`,
                    e.`eaddress`,
                    e.`time`,
                    e.`exname`,
                    e.`exnum`,
                    e.`fang_status`,
                    e.`status`
                FROM
                     `express` as e
                LEFT JOIN 
                  user_project
                ON 
                  user_project.`id` = e.`pid`
                WHERE 
                    e.`id` 
                 IN 
                    ($id)
            " ;
        $sql .= " order by `time` desc ";
        return  $this->query($sql);
    }
    //查询收件人信息
    public function userEx($eid){
        $sql = " 
                SELECT 
                    id,
                    ename,
                    ephone,
                    eaddress,
                    exname,
                    fang_status,
                    exnum
                FROM
                    `express`
                WHERE
                    id = $eid
            ";
        return $this->query($sql);
    }
    //用户
    public function editInfou($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('express',$data);
    }
    //快递
    public function editInfoe($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('express',$data);
    }
    //回访状态
    public function editFangs($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('express',$data);
    }
    //自动收货
    public function zdsh($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('express',$data);
    }
}