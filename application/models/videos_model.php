<?php
require_once ('application/core/MY_Model.php');

class videos_model extends MY_Model
{

    public $table_name = "videos";

    public function findAll(){
            $sql = "SELECT
                    	`id`,
                    	`title`,
                    	`url`,
                    	`remark`,
                    	`images`,
                    	`add_time`,
                    	`status`,
                        `type_id`
                    FROM
                    	`videos`
                ";
           return $this->query($sql);
    }
    
    public function findByType($type_id,$page, $per_page){
        $sql = "SELECT
                    	`id`,
                    	`title`,
                    	`url`,
                    	`remark`,
                    	`images`,
                    	`add_time`,
                    	`status`,
                        `type_id`
                    FROM
                    	`videos`
                    WHERE 
                        `type_id` = $type_id
                ";
        $sql .= $this->getLimitStr($page, $per_page);
        
        return $this->query($sql);
    }
    //
    public function count($type_id)
    {
        $sql = "
                SELECT
                    `id`,
                     count(id) as total
                FROM
                    `videos`
                WHERE 
                        `type_id` = $type_id
            " ;
        return $this->queryOne($sql);
    }
}