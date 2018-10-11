<?php
require_once ('application/core/MY_Model.php');

class cmef_model extends MY_Model
{
    public $table_name = "cmef";
    public function userstate($openid)
    {
        $sql = "
                SELECT
                    `id`,
                    `phone`
                FROM
                    `cmef`
             WHERE 
                        `open_id` = $openid
                ";

        return $this->query($sql);

    }
}