<?php
require_once ('application/controllers/admin/common.php');

/**
 * 兑换账号管理
 * Enter description here .
 *
 */
class ex_account extends MY_Admin_Site
{
    //兑换账号列表
    public function getList()
    {
        if(empty($_GET['dealer_id'])){
            $this->load->model('admin/ex_account_model');
            $result = $this->ex_account_model->getData();
            $data['data'] = $row = $result->result();
            $this->load->view('admin/ex_account.html',$data);
        } else{
            $dealer_id = $_GET['dealer_id'];
            $this->load->model('admin/ex_account_model');
            $result = $this->ex_account_model->getDatas($dealer_id);
            $data['data'] = $row = $result->result();
            $data['dealer_id'] = $dealer_id;
            //var_dump($data['data']);
            $this->load->view('admin/ex_account.html',$data);

        }

    }
    //添加兑换账号
    public function addEx_account()
    {
        if(!empty($_GET['dealer_id'])){
            $dealer_id = $_GET['dealer_id'];
            $data['dealer_id'] = $dealer_id;
            $this->load->view('admin/add_ex_account.html',$data);
        }

    }
    //添加处理
    public function addInfo()
    {
        if(!empty($_POST)){
            $dealer_id = $_POST['dealer_id'];
            if($_POST['account'] == '' || $_POST['account_name'] == ''){
                echo "<script language=javascript> alert('请输入完整信息！'); location.href='/admin/ex_account/getList?dealer_id=$dealer_id';</script>";
                exit;
            }
            //检验账号信息
            $account = $_POST['account'];
            $query_code = $this->db->query("SELECT id FROM ex_account WHERE account = '$account'")->row_array();
            if(!empty($query_code)){
                echo "<script language=javascript> alert('该账号已被占用！'); location.href='/admin/ex_account/getList?dealer_id=$dealer_id';</script>";exit;
            }
            $query_admin = $this->db->query("SELECT id FROM admin_user WHERE account = '$account'")->row_array();
            if(!empty($query_admin)){
                echo "<script language=javascript> alert('该账号已被占用！'); location.href='/admin/ex_account/getList?dealer_id=$dealer_id';</script>";exit;
            }
            $this->load->model('admin/ex_account_model');
            //admin
            $data_admin['account'] = $_POST['account'];
            $data_admin['admin_name'] = $_POST['account_name'];
            $data_admin['password'] = md5('123456');
            $data_admin['is_login'] = 1;
            $data_admin['login_ip'] = '';
            $data_admin['type'] = 3;
            $data_admin['add_time'] = date('Y-m-d H:i:s',time());
            $admin_id = $this->ex_account_model->add_admin($data_admin);
            //ex_account
            $data['admin_id'] = $admin_id;
            $data['account'] = $_POST['account'];
            $data['account_name'] = $_POST['account_name'];
            $data['addtime'] = time();
            $data['dealer_id'] = $dealer_id;
            $data['open_id'] = '';
            $data['password'] = md5('123456');
            $result = $this->ex_account_model->add($data);
            if($result){
                echo "<script language=javascript> alert('添加成功！'); location.href='/admin/ex_account/getList?dealer_id=$dealer_id';</script>";
            }else{
                echo "<script language=javascript> alert('添加失败！'); location.href='/admin/ex_account/getList?dealer_id=$dealer_id';</script>";
            }
        }
    }
    //修改
    public function editEx()
    {
        if(!empty($_GET['eid'])){
            $eid = $_GET['eid'];
            $data['eid'] = $eid;
            $this->load->model('admin/ex_account_model');
            $result = $this->ex_account_model->accountInfo($eid);
            $info = $row = $result->result();
            $data['data'] = $info[0];
            $this->load->view('admin/edit_ex_account.html',$data);
        }
    }
    //修改处理
    public function editInfo()
    {
        if(!empty($_POST)){
            $id = $_POST['eid'];
            $array['account'] = $_POST['account'];
            $array['account_name'] = $_POST['account_name'];
            $this->load->model('admin/ex_account_model');
            $result = $this->ex_account_model->update($id,$array);
            if($result){
                echo "<script language=javascript> alert('修改成功！'); history.back(-1);</script>";
            }else{
                echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
            }
        }
    }
    //删除方法
    public function delEx()
    {
        if(!empty($_POST['id'])){
            $id = $_POST['id'];
            $this->db->where_in('id',$id);
            $re = $this->db->delete('ex_account');
            if($re){
                echo "<script language=javascript> alert('删除成功！');history.back();</script>";
            } else{
                echo "<script language=javascript> alert('删除失败！');history.back();</script>";
            }
        } else {
            echo "<script language=javascript> alert('未选择任何数据！');history.back();</script>";
        }
    }
    //兑换账号info
    public function exInfo()
    {
        $admin_id = $_GET['admin_id'];
        $query_info = $this->db->query("SELECT * FROM ex_account WHERE admin_id = $admin_id")->row_array();
        $dealer_id = $query_info['dealer_id'];
        $query_dealer = $this->db->query("SELECT dealer_name FROM dealer WHERE id = $dealer_id")->row_array();
        $this->page_data['dealer_name'] = $query_dealer['dealer_name'];
        $this->page_data['account_id'] = $query_info['id'];
        $this->page_data['account'] = $query_info['account'];
        $this->page_data['account_name'] = $query_info['account_name'];
        $this->page_data['dealer_tell'] = $query_info['dealer_tell'];
        $this->page_data['coupon_value'] = $query_info['coupon_value'];
        $this->page_data['addtime'] = $query_info['addtime'];
        $this->load->view('admin/account_info.html',$this->page_data);
    }
    //账号信息修改
    public function editInfo_account()
    {
        if(!empty($_GET['account_id'])){
            $account_id = $_GET['account_id'];
            $query_info = $this->db->query("SELECT * FROM ex_account WHERE id = $account_id")->row_array();
            $this->page_data['account_id'] = $query_info['id'];
            $this->page_data['account_name'] = $query_info['account_name'];
            $this->page_data['dealer_tell'] = $query_info['dealer_tell'];
            $this->page_data['coupon_value'] = $query_info['coupon_value'];
            $this->load->view('admin/edit_account_info.html',$this->page_data);
        }
    }
    //修改处理
    public function check_account_info()
    {
        if(!empty($_POST)){
            $id = $_POST['account_id'];
            $array['account_name'] = $_POST['account_name'];
            $array['dealer_tell'] = $_POST['dealer_tell'];
            $array['coupon_value'] = $_POST['coupon_value'];
            if( $array['coupon_value'] <= 0 || $array['coupon_value'] > 5000){
                echo "<script language=javascript> alert('请输入正确的优惠金额！'); history.back();</script>";exit;
            }
            $this->load->model('admin/ex_account_model');
            $result = $this->ex_account_model->update($id,$array);
            if($result){
                echo "<script language=javascript> alert('修改成功！'); history.back(-1);</script>";
            }else{
                echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
            }
        }
    }

}




