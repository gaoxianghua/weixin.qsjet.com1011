<?php
require_once ('application/core/MY_Controller.php');

/**
 * 客户
 * Enter description here .
 *
 *
 *
 *
 * @author
 *
 * @property
 *
 */
class customer extends MY_Controller_Site
{

    public $user_id = '';

    public $open_id = '';

    public $userInfo = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('userinfo');
        $this->load->model('user_model');
        $this->page_data['open_id'] = $this->open_id = $this->input->get('open_id');
        $this->page_data['qc_code'] = $this->qc_code = $this->input->get('qc_code');
        $this->page_data['doctor_id'] = $this->doctor_id = $this->input->get('doctor_id');
        $this->page_data['dealer_id'] = $this->dealer_id = $this->input->get('dealer_id');
    }

                                /**********************  展示部分  ************************/
    public function index()
    {
        $re = $this->checkInfo();
        if( $re['result_code'] == 200 ){
            //展示添加信息页面
            // redirect(base_url('customer/showInsertCustomer?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
            //优惠券展示
            redirect(base_url('customer/showCoupon?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
        }
        if( $re['result_code'] == 201 ){
            //判断qc_code
            $re_qc = $this->db->query("SELECT id FROM customer WHERE open_id = '$this->open_id' AND qc_code = '$this->qc_code'")->row_array();
            if(!empty($re_qc['id'])){
                redirect(base_url('customer/showCustomer?open_id='.$this->open_id.'&qc_code='.$re['info']['qc_code'].'&doctor_id='.$re['info']['doctor_id'].'&dealer_id='.$re['info']['dealer_id']));
            }else{
                $data = array('status_c' => 2);
                $this->db->where('open_id', "$this->open_id");
                $this->db->update('customer', $data);
                $this->db->query("UPDATE coupon SET status = 2 WHERE open_id = '$this->open_id'");
                //redirect(base_url('customer/showInsertCustomer?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
                redirect(base_url('customer/showCoupon?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
            }
        }
        redirect(base_url('customer/showFaild?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
    }

    /*
     * 客户信息添加展示 -- 扫医师二维码
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function showInsertCustomer()
    {
        $re = $this->checkInfo();
        $qc_code = $_GET['qc_code'];
        $this->page_data['account_id'] = $_GET['account_id'];
        $query_qc = $this->db->query("SELECT account_id FROM qc_code WHERE qc_code_name = '$qc_code'")->row_array();
        if(!empty($query_qc)){
            $aid = $query_qc['account_id'];
        } else{
            exit;
        }
        $query_de = $this->db->query("SELECT id,dealer_tell FROM ex_account WHERE id = $aid");
        $row_de = $query_de->row_array();
        $this->page_data['dealer_tell'] = $row_de['dealer_tell'];
        if( $re['result_code'] == 200 || $re['result_code'] == 201 ){
            $this->loadView('add_user_insert_doctor.html', $this->page_data);return;
        }
        redirect(base_url('customer?status='.$re['error_msg'].'&open_id='.$this->open_id.'&qc_code='.$this->qc_code.'&doctor_id='.$this->doctor_id.'&dealer_id='.$this->dealer_id));
    }
    /*
     * 展示优惠券信息
     * 2018-07-24
     * 传入参数：
     * 无；
     *
     */
    public function showCoupon()
    {
        if(!empty($_GET)){
            $this->page_data['status'] = $_GET['status'];
            $this->page_data['open_id'] = $_GET['open_id'];
            $this->page_data['qc_code'] = $_GET['qc_code'];
            $this->page_data['doctor_id'] = $_GET['doctor_id'];
            $this->page_data['dealer_id'] = $_GET['dealer_id'];
            //判断是否绑定兑换账号
            $qc_code = $this->page_data['qc_code'];
            $query_code = $this->db->query("SELECT account_id FROM qc_code WHERE qc_code_name = '$qc_code'")->row_array();
            if($query_code['account_id'] == ''){
                echo "<script language=javascript> alert('该二维码无效！');window.close();</script>";exit;
            }else{
                $cou_data['ex_account_id'] = $query_code['account_id'];
                $aid = $cou_data['ex_account_id'];
                $this->page_data['account_id'] = $aid;
                //echo $this->result_lib->setErrorsJson('该二维码无效！');exit;
            }
            //经销商电话
            $tell = $this->db->query("SELECT dealer_tell FROM ex_account WHERE id = $aid")->row_array();
            if(!empty($tell)){
                $this->page_data['dealer_tell'] = $tell['dealer_tell'];
            }else{
                $this->page_data['dealer_tell'] = '';
            }
            //优惠金额
            $value = $this->db->query("SELECT coupon_value FROM ex_account WHERE id = $aid")->row_array();
            if(!empty($value)){
                $this->page_data['value'] =  $value['coupon_value'];
            } else{
                $this->page_data['value'] =  '';
            }

            $this->loadView('show_coupon.html',$this->page_data);
        }

        //$this->load->view('show_coupon.html', $this->page_data);
    }

                                        /***************** 入库部分  *********************/
    /*
     * 用户信息添加处理
     * liting
     * 2016/06/02 18:30:00
     * 传入参数：
     * 无；
     *
     */
    public function doCustomerInsert()
    {
        $re = $this->checkInfo();
        if( $re['result_code'] == 200 || $re['result_code'] == 201 ){
            //信息构建
            $data = $this->createData();
            $data['gender'] = '';
            $data['status_c'] = 1;
            $data['medical_history'] = '';
            $data['insulin'] = '';
            $data['is_scleroma'] = '0';
            if( $data ){
                $this->load->model('customer_model');
                $this->db->trans_start();
                $this->db->insert('customer',$data);
                $customer_id = $this->db->insert_id();
                if($customer_id){
                    $this->db->trans_commit();
                    //添加优惠券信息 1、type = 1
                    $cou_data['open_id'] = $data['open_id'];
                    $cou_data['doctor_id'] = $data['doctor_id'];
                    $cou_data['dealer_id'] = $data['dealer_id'];
                    //兑换账号id
                    $qc_code = $data['qc_code'];
                    $query_code = $this->db->query("SELECT account_id FROM qc_code WHERE qc_code_name = '$qc_code'")->row_array();
                    if(!empty($query_code)){
                        $cou_data['ex_account_id'] = $query_code['account_id'];
                    }else{
                        echo $this->result_lib->setErrorsJson('该二维码无效！');exit;
                    }
                    //优惠码
                    //$str = 'ABCDEFGH4JKMNOPQRSTUVWXYZ123456789987654321';
                    //$string = substr(str_shuffle($str) , 0 , 10);
                    $cou_data['con_code_s'] = '';
                    //添加时间
                    $cou_data['addtime'] = time();
                    //过期时间
                    $nowtime = strtotime(date('Y-m-d',time()));
                    $overtime = strtotime("+3 month",$nowtime);
                    $cou_data['overtime'] = $overtime;
                    //状态
                    $cou_data['status_s'] = 1;
                    $cou_data['status_m'] = 1;
                    $cou_data['status'] = 1;
                    //兑换时间
                    $cou_data['extime_s'] = 0;
                    $cou_data['extime_m'] = 0;
                    //2、type = 2
                    $strs = 'ABCDEFGH6JKMNOPQRSTUVWXYZ123456789987654321';
                    $strings = substr(str_shuffle($strs) , 0 , 10);
                    $cou_data['con_code_m'] = $strings;
                    $this->db->insert('coupon',$cou_data);
                    echo $this->result_lib->setInfoJson('绑定成功');exit;
                }
            }
            $this->db->trans_rollback();
            echo $this->result_lib->setErrorsJson('绑定失败');exit;
        }
        echo json_encode($re['error_msg']);exit;
    }
    
    /*
     * 信息构建
     * liting
     * 2016/06/04 11:30:00
     * 传入参数：
     * 无；
     *
     */
    public function createData()
    {
        $data = $this->input->post();
        if(  !preg_match("/^[\s\x{4e00}-\x{9fa5}A-Za-z0-9]{1,30}$/u",$data['username']) ){
            return false;
        }

        $data += array(
            'status'=>'3',
            'open_id'=>$this->open_id,
            'qc_code'=>$this->qc_code,
            'doctor_id'=>$this->doctor_id,
            'dealer_id'=>$this->dealer_id,
            'add_time'=>date('Y-m-d H:i:s')
        );
        return $data;
    }
                                      /***************** 检测部分  *********************/
    /*
     * 检测信息
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function checkInfo()
    {
        $re = $this->result_lib->setInfo();
        if(!$this->open_id || !$this->qc_code || !$this->doctor_id ){
            $re = $this->result_lib->setErrors('绑定信息有误');
        }
        
        //检测此用户是否已存在,此用户已绑定
        if($customerInfo = $this->checkBinding()){  
            $re = array('result_code'=>201,'info'=>$customerInfo);
        }
        //检测二维码信息是否正确
        if(!$this->checkQc()){
            $re = $this->result_lib->setErrors('二维码信息不正确');
        }
        //检测手机号是否存在
        /*$mobile = $this->createData();
        $num = $mobile['mobile'];
        $check = $this->db->query("SELECT id FROM customer WHERE mobile = '$num'")->row_array();
        if(!empty($check)){
            echo $this->result_lib->setErrorsJson('此手机号已存在');exit;
        }*/
        return $re;
    }
    /*
     * 检测open_id是否存在
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function checkBinding()
    {
        $this->load->model('customer_model');
        $result = $this->customer_model->findOneByOpenId($this->open_id);
        if(empty($result)){
            return $result;
        }else{
            return $result[0];
        }
    }
    
    /*
     * 检测二维码是否存在
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function checkQc()
    {
        $this->load->model('qc_code_model');
        return $this->qc_code_model->findOneByName($this->qc_code);
    }
    
    
                        /***************** 展示部分  *********************/
    
    /*
     * 信息展示
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function showCustomer()
    {
        if(!empty($_GET['open_id'])){
        $open_id = $_GET['open_id'];
        $res = $this->db->query("select 
              y.overtime,
              y.id,
              c.username,
              c.mobile
              from 
              coupon as y
              left join
              customer as c
              ON 
              c.open_id = y.open_id
              where y.open_id='$open_id'
              ")->result_array();
        $result = $res[0];
        if(time()>$result['overtime']){
            $qc_code = $_GET['qc_code'];
            $query_code = $this->db->query("SELECT account_id FROM qc_code WHERE qc_code_name = '$qc_code'")->row_array();
            $aid = $query_code['account_id'];
            //经销商电话
            $tell = $this->db->query("SELECT dealer_tell FROM ex_account WHERE id = $aid")->row_array();
            $this->page_data['dealer_tell'] = $tell['dealer_tell'];
            $this->page_data['username'] = $result['username'];
            $this->page_data['mobile'] = $result['mobile'];
            $id = $result['id'];
            $this->db->where('id',$id);
            $this->db->delete('coupon');
            //customer
            $this->db->where('open_id',$open_id);
            $this->db->delete('customer');
            $this->loadView('add_insert_overtime.html', $this->page_data);
        } else{
            $this->page_data['customerInfo'] = $this->checkBinding();
            $this->loadView('customer_info.html', $this->page_data);
        }

        }
    }
    
    /*
     * 用户信息添加成功
     * liting
     * 2016/05/17 18:00:00
     * 传入参数：
     * 无；
     *
     */
    public function showSuccess()
    {
        $this->loadView('insert_user_success.html', $this->page_data);
    }
    //用户信息添加成功 2018-06-05
    public function showSuccessUser()
    {
        $open_id = $_GET['open_id'];
        $doctor_id = $_GET['doctor_id'];
        //用户优惠券
        $query = $this->db->query("SELECT id,con_code_m,overtime,dealer_id,status_m,addtime FROM coupon WHERE open_id = '$open_id' AND doctor_id= $doctor_id");
        $row = $query->row_array();
        if(!empty($row)){
            $qc_code = $_GET['qc_code'];
            $query_code = $this->db->query("SELECT account_id FROM qc_code WHERE qc_code_name = '$qc_code'")->row_array();
            $aid = $query_code['account_id'];
                //经销商电话
                $tell = $this->db->query("SELECT dealer_tell FROM ex_account WHERE id = $aid")->row_array();
                //兑换金额
                $value = $this->db->query("SELECT coupon_value FROM ex_account WHERE id = $aid")->row_array();
                //用户id
                $query_id = $this->db->query("SELECT id FROM customer WHERE open_id = '$open_id'");
                $row_id = $query_id->row_array();
                $this->page_data['customer_id'] = $row_id['id'];
                $this->page_data['result'] = $row;
                $this->page_data['dealer_tell'] = $tell['dealer_tell'];
                $this->page_data['value'] = $value['coupon_value'];
                $this->loadView('insert_user_success_user.html', $this->page_data);
        }else{
            $this->loadView('insert_user_success_user.html', $this->page_data);
        }
    }


    //添加回访时间
    public function fangtime()
    {
        if(!empty($_POST)){
            $_POST['add_time'] = time();
            $_POST['status'] = 1;
            $this->db->insert('fangtime',$_POST);
        }
    }

    /*
     * 用户信息添加失败
     * liting
     * 2016/05/17 18:00:00
     * 传入参数：
     * 无；
     *
     */
    public function showFaild($status='')
    {
        $this->page_data['error_msg'] = $this->input->get('status')?$this->input->get('status'):$status;
        $this->loadView('insert_user_faild.html', $this->page_data);
    }
}










