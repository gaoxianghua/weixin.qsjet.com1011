<?php
require_once ('application/core/MY_Model.php');

class auto_msg_model extends MY_Model
{

    public $table_name = "auto_msg";

    public function findOneByType($type)
    {
        $sql = " SELECT
                    	`type`,
                        `card_id`
                    FROM
                        `auto_msg`
                WHERE
                        `type` = '$type'
                ";
        return $this->queryOne($sql);
    }
}