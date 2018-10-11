<?php
require_once ('application/core/MY_Model.php');

class videos_type_model extends MY_Model
{

    public $table_name = "videos_type";

    public function findAll(){
            $sql = "SELECT
                    	`id`,
                        `name`
                    FROM
                    	`videos_type`
                ";
           return $this->query($sql);
    }
}