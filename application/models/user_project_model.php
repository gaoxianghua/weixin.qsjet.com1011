<?php
require_once ('application/core/MY_Model.php');

class user_project_model extends MY_Model
{

    public $table_name = "user_project";

    public function findAll($user_id,$page,$per_page)
    {
        $sql = "
                SELECT
                    `id`,
                    `user_id`,
                    `project_type`,
                    `project_num`,
                    `add_time`,
                    `status`
                FROM
                    `user_project`
                WHERE
                    `user_id` = $user_id
            " ;
        $sql .= " order by `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        return $this->query($sql);
    }
    
    public function count($user_id)
    {
        $sql = "
                SELECT
                    `id`,
                     count(id) as total,
                     user_id
                FROM
                    `user_project`
                WHERE
                    `user_id` = $user_id
            " ;
        return $this->queryOne($sql);
    }
    
    public function findProjectOne($project_num)
    {
        $sql = "
                SELECT
                    `id`,
                    `project_type`,
                    `project_num`,
                    `status`,
                    `user_id`
                FROM
                    `user_project`
                WHERE
                    `project_num` = '$project_num'
        " ;
        return $this->queryOne($sql);
    }
    
    public function insert($project_type,$project_num,$user_id,$status)
    {
        $data = array(
            'status'=>$status,
            'project_type'=>$project_type,
            'project_num'=>$project_num,
            'user_id'=>$user_id,
            'add_time'=>date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->table_name,$data);
        return $this->db->insert_id();
    }
}