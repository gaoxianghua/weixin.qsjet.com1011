<?php
require_once ('application/core/MY_Model.php');

class user_info_model extends MY_Model
{

    public $table_name = "user_info";

    public function findOneByOpen()
    {
    }
    
    public function insert($userInfo)
    {
        return $this->db->insert($this->table_name,$userInfo);
    }
    public function findOneByUid($user_id)
    {
        $sql = " 
                SELECT 
                    user_id,
                    username,
                    gender,
                    mobile
                FROM
                    `user_info`
                WHERE
                    user_id = $user_id
            ";
        return $this->queryOne($sql);
    }
}