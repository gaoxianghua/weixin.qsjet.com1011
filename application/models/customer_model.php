<?php
require_once ('application/core/MY_Model.php');

class customer_model extends MY_Model
{

    public $table_name = "customer";

    public function findOneByOpenId($open_id)
    {
        $sql = " SELECT
                    	`id`,
                    	`open_id`,
                    	`qc_code`,
                    	`add_time`,
                    	`doctor_id`,
                    	`dealer_id`,
                    	`username`,
                    	`mobile`
                    FROM
                        `$this->table_name` 
                  WHERE
                    	`open_id` =  '$open_id'
                ";
        $sql .= " order by `add_time` desc ";
        return $this->query($sql);
    }
    
    public function insert($data)
    {
        return $this->db->insert($this->table_name,$data);
    }
}