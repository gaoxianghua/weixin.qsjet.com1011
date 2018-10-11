<?php
require_once ('application/core/MY_Controller.php');

/**
 * 用户优惠
 *
 *2018-06-19
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
                    c.mobile,     
                    ex.dealer_tell,     
                    ex.coupon_value,     
                    y.id,
                    y.con_code_m,
                    y.status_m,
                    y.addtime,
                    y.overtime
                 FROM
                     customer as c
                Left Join       
                    coupon as y
                 ON 
                 c.open_id  =  y.open_id
                 Left Join       
                    ex_account as ex
                 ON 
                 y.ex_account_id  =  ex.id
                WHERE 
                c.open_id = '$open_id'
            "
            );
            $row = $query->result_array();
            $count_row = count($row);
            if(!empty($row)){
                $this->page_data['result'] = $row[$count_row-1];
                $this->loadView('mydiscount.html', $this->page_data);
            }else{
                //echo '暂无优惠信息';
                $this->page_data['error_msg'] = '暂无优惠信息';
                $this->loadView('coupon_faild.html', $this->page_data);
            }


        }else{
            echo '请在微信公众号查看';
        }
    }

}