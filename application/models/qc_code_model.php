<?php
require_once ('application/core/MY_Model.php');

class qc_code_model extends MY_Model
{

    public $table_name = "qc_code";
    
    public function findOneByName($name){
        $sql = " select id,qc_code_name,add_time,status,dealer_id,code  from  $this->table_name where qc_code_name='$name'";
        return $this->queryOne($sql);
    }
    
    public function findOneById($id){
        $sql = "
        SELECT
        `id`,
        `qc_code_name`,
        `add_time`,
        `status`,
        `dealer_id`
        FROM
        `$this->table_name`
        WHERE
        `id` = $id
        ";
        return $this->queryOne($sql);
    }
    
    public function update($id,$data){
        $this->db->where('id',$id);
        $re = $this->db->update($this->table_name,$data);
        return $re;
    }

}