<?php
require_once ('application/core/MY_Model.php');

class article_model extends MY_Model
{

    public $table_name = "article";

    public function findAll($page, $per_page){
            $sql = "SELECT
                    	`id`,
                    	`title`,
                    	`url`,
                    	`remark`,
                    	`images`,
                    	`add_time`,
                    	`status`
                    FROM
                    	`article`
                ";
            $sql .= $this->getLimitStr($page, $per_page);
           return $this->query($sql);
    }
    
    public function findOne($id){
        $sql = "SELECT
                    	`id`,
                    	`title`,
                    	`url`,
                    	`remark`,
                    	`images`,
                    	`add_time`,
                    	`status`
                    FROM
                    	`article`
                    WHERE `id` = $id
                ";
        return $this->queryOne($sql);
    }
    
    public function count()
    {
        $sql = "
                SELECT
                    `id`,
                     count(id) as total
                FROM
                    `article`
            " ;
        return $this->queryOne($sql);
    }
}