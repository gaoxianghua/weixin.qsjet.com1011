<?php
require_once ('application/core/MY_Model.php');

class project_model extends MY_Model
{

    public $table_name = "project";

    public function findAll($page,$per_page)
    {
       $sql = "
                SELECT
                    `id`,
                    `name`,
                    `remark`,
                    `images` as img,
                    `add_time`,
                    `project_url`,
                    `status`
                FROM
                    `project`
            " ;
       $sql .= " order by `add_time` desc ";
       $sql .= $this->getLimitStr($page, $per_page);
        return $this->query($sql);
    }
    
    public function count($where = array(), $group = "", $select = '', $table_name = '')
    {
        $sql = "
                SELECT
                    `id`,
                     count(id) as total
                FROM
                    `project`
            " ;
        return $this->queryOne($sql);
    }
    //确认收货
    public function conGoods($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('express',$data);
    }
}