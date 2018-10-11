<?php
require_once ('application/controllers/admin/common.php');

/**
 * 优惠券管理
 * Enter description here .
 *
 */
class coupon extends MY_Admin_Site
{
    //优惠券列表
    public function getList()
    {
        if(empty($_GET['dealer_id'])){
            $this->load->model('admin/coupon_model');
            $result = $this->coupon_model->getData();
            $data['data'] = $row = $result->result();
            $this->load->view('admin/coupon.html',$data);
        } else{
            $dealer_id = $_GET['dealer_id'];
            $this->load->model('admin/coupon_model');
            $result = $this->coupon_model->getDatas($dealer_id);
            $data['data'] = $row = $result->result();
            $this->load->view('admin/coupon.html',$data);
        }

    }
    //删除方法
    public function delEx()
    {
        var_dump($_POST);
    }


}




