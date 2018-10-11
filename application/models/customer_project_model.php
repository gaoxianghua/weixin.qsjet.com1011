<?php
require_once ('application/core/MY_Model.php');

class customer_project_model extends MY_Model
{

    public $table_name = "customer_project";

    public function findAll($open_id,$page,$per_page)
    {
        $sql = "
                SELECT
                    `id`,
                    `open_id`,
                    `project_type`,
                    `project_num`,
                    `add_time`,
                    `status`
                FROM
                    `customer_project`
                WHERE
                    `open_id` = $open_id
            " ;
        $sql .= " order by `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        return $this->query($sql);
    }
    
    public function count($open_id)
    {
        $sql = "
                SELECT
                    `id`,
                     count(id) as total,
                     open_id
                FROM
                    `customer_project`
                WHERE
                    `open_id` = $open_id
            " ;
        return $this->queryOne($sql);
    }
    
    
    public function findProjectOne($project_type,$project_number,$id)
    {
        $sql = "
                SELECT
                    `id`,
                    `project_type`,
                    `project_number`,
                    `open_id`
                FROM
                    `customer_project`
                WHERE
                    `open_id` = $id
                AND 
                    `project_type` = '$project_type'
                AND 
                    `project_number` = '$project_number'
            " ;
        return $this->queryOne($sql);
    }
    
    public function insert($project_type,$project_number,$open_id)
    {
        $data = array(
            'project_type'=>$project_type,
            'project_number'=>$project_number,
            'open_id'=>$open_id,
            'add_time'=>date('Y-m-d H:i:s'),
        );
        return $this->db->insert($this->table_name,$data);
    }
}