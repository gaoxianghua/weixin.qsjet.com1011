<?php
require_once ('application/core/MY_Controller.php');

/**
 * 用户优惠
 *
 *2018-06-22
 */
class mydiscount extends MY_Controller_Site
{
    //用户优惠信息展示
    public function getList()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $query = $this->db->query(
                "  SELECT
                    c.username,
                    c.`mobile`,     
                    y.id,
                    y.con_code_s,
                    y.con_code_m,
                    y.status_s,
                    y.status_m,
                    y.overtime
                 FROM
                     customer as c
                Left Join       
                    coupon as y
                 ON 
                 c.open_id  =  y.open_id
                WHERE 
                c.open_id = '$open_id'
            "
            );
            $row = $query->result_array();
            var_dump($row);
        }
    }

}