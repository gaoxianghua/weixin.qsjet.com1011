<?php
require_once ('application/core/MY_Controller.php');
/**
 * 医生
 * Enter description here .
 *
 *
 *
 *
 *
 * @author
 *
 * @property
 *
 */
class doctor extends MY_Controller_Site
{

    public $qc_code;

    public $QcInfo;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('qc_code_model');
        $this->qc_code  = $this->input->get('qc_code');
        $this->open_id = $this->input->get('open_id');
        $this->page_data['qc_code'] =$this->qc_code;
        $this->page_data['open_id'] =$this->open_id;
        $this->doctor_id = $this->session->userdata('doctor_id');
        
    }

    /*
     * 二维码信息获取
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function findQcInfo()
    {
        return $this->qc_code_model->findOneByName($this->qc_code);
    }
    
    /*
     * 医师详情获取 （通过二维码）
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function getDoctorInfo()
    {
        $this->load->model('doctor_model');
        return $this->doctor_model->findOneByQc($this->qc_code);
    }
    
    /*
     * 医师详情获取 （通过open_id）
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function getDoctorByOpenId()
    {
        $this->load->model('doctor_model');
        return $this->doctor_model->findOneByOpenId($this->open_id);
    }
    
    /*
     * 首页
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function index()
    {
        $this->checkQcInfo();
    }
    
    /*
     * 跳转链接处理
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function checkQcInfo()
    {
        //二维码信息
        $this->QcInfo = $this->findQcInfo();
        //医生信息
        $this->doctorInfo = $this->getDoctorByOpenId();
        
        //二维码信息错误
        if(!$this->QcInfo){         
            redirect(base_url('doctor/showFaild?status=二维码信息不正确'));
            exit();
        }
        
        //二维码尚未指派
        if($this->QcInfo['status'] == '1'){
            redirect(base_url('doctor/showInsertDoctor?qc_code=' . $this->qc_code.'&open_id='.$this->open_id));
            exit();
        }
        //二维码已指派,跳转绑定医生页
        if($this->QcInfo['status'] == '2'){
            redirect(base_url('doctor/showInsertDoctor?qc_code=' . $this->qc_code.'&open_id='.$this->open_id));
            exit();
        }
        
        //二维码未绑定
        if($this->QcInfo['status'] == '3'){
            //医生已绑定其他经销商
            if($this->doctorInfo){
                redirect(base_url('doctor/showFaild?status=此来源已绑定其他经销商'));
                exit();
            }
            redirect(base_url('doctor/showCheckCode?qc_code=' . $this->qc_code.'&open_id='.$this->open_id));
            exit();
        }
        if($this->QcInfo['status'] == '4' ){
            //如果为医师且二维码id相同
            $doctor_qc = $this->getDoctorInfo();
            if( $doctor_qc['open_id'] == '' || $doctor_qc['open_id'] == $this->open_id ){
                redirect(base_url('doctor/showCheckCode?qc_code=' . $this->qc_code.'&open_id='.$this->open_id));
                exit();
            }
            // 客户操作
            redirect(base_url('customer?open_id=' . $this->open_id . '&dealer_id='.$this->QcInfo['dealer_id'].'&doctor_id='.$doctor_qc['id'].'&qc_code=' . $this->qc_code));
            exit();
        }
    }
    
    /*
     * 检测绑定信息
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function checkBuing()
    {
        $this->QcInfo = $this->findQcInfo();
        if (! $this->QcInfo) {
            redirect(base_url('doctor/showFaild?status=二维码信息不正确'));
            exit();
        }

        // 查询此医生是否绑定过其他二维码
        $doctorInfo = $this->getDoctorByOpenId();
        if ($doctorInfo&&$doctorInfo['qc_code'] != $this->qc_code) {
            redirect(base_url('doctor/showFaild?status=此二维码已绑定其他经销商'));
            exit();
        }
    }

    /*
     * 绑定信息展示
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function showDoctorInfo()
    {
        if(!$this->session->userdata('doctor_id')){
            redirect(base_url('doctor?open_id='.$this->open_id));
            exit();
        }
        $this->load->model('doctor_model');
        $this->page_data['result'] = $this->doctor_model->findOneById($this->session->userdata('doctor_id'));
        $dealer_id = $this->page_data['result']['dealer_id'];
        $doctor_id = $this->page_data['result']['doctor_id'];
        //推荐人数
        $res = $this->db->query("select * from customer where dealer_id='$dealer_id' and doctor_id = '$doctor_id' and status_c != 3")->result_array();
        $num = count($res);

        //180劵使用数量
        $res_m = $this->db->query("select * from coupon where doctor_id = '$doctor_id' and status_m = 2 and status != 3")->result_array();
        $num_m = count($res_m);
        $this->page_data['result']['recom'] = $num;
        $this->page_data['result']['num_m'] = $num_m;
        $this->page_data['customer'] = $this->doctor_model->findAll($dealer_id,$doctor_id);
     
        $this->loadView('doctor.html', $this->page_data);
    }

    /*
   * 绑定信息详情(表格)展示
   * liting
   * 2016/05/20 12:30:00
   *
   */
    public function showInfo()
    {
        if(!$this->session->userdata('doctor_id')){
            redirect(base_url('doctor?open_id='.$this->open_id));
            exit();
        }
        $this->load->model('doctor_model');
        $this->page_data['result'] = $this->doctor_model->findOneById($this->session->userdata('doctor_id'));
        $dealer_id = $this->page_data['result']['dealer_id'];
        $doctor_id = $this->page_data['result']['doctor_id'];
        $this->page_data['customer'] = $this->doctor_model->findAll($dealer_id,$doctor_id);

        $this->loadView('doctor_info.html', $this->page_data);
    }
    /*
     * 客户详情
     * 2018-07-10
     *
     */
    public function customer()
    {
        if(!empty($_GET)){
            $this->load->model('doctor_model');
            $cid = $_GET['cid'];
            $re = $this->doctor_model->findCustomer($cid);
            $this->page_data['customer'] = $re[0];
            $this->loadView('cus_info.html', $this->page_data);
        }

    }
    
    /*
     * 医生每月信息获取
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function getDoctorMon()
    {
        
        $months = $this->input->get('months');
        $years = $this->input->get('years');

        $this->load->model('doctor_count_model');
        $result = $this->doctor_count_model->findDoctorOne($this->doctor_id,$years,$months);
        $result['recommend'] = isset($result['recommend'])?$result['recommend']:'0';
        $result['deal'] = isset($result['deal'])?$result['deal']:'0';
        echo $this->result_lib->setInfoJson($result);
    }
    
    /*
     * 医生每年信息获取
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function getDoctorYears()
    {
        $years = $this->input->get('years');
        $this->load->model('doctor_count_model');
        $result = $this->doctor_count_model->findDoctorYears($this->doctor_id,$years);
        echo $this->result_lib->setInfoJson($result);
    }
    
    /*
     * 绑定医生
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function showInsertDoctor()
    {
        $this->QcInfo = $this->findQcInfo();
        if (!$this->QcInfo) {
            redirect(base_url('doctor/showFaild?status=二维码信息不正确'));
            exit();
        }
        if ( $this->QcInfo['status']==3) {
            redirect(base_url('doctor/showSuccess?qc_code=' . $this->qc_code.'&open_id='.$this->open_id));
            exit();
        }
        //$this->loadView('insert_doctor.html', $this->page_data);
        redirect(base_url('doctor/showFaild?status=此二维码还未绑定信息'));
    }

    /*
     * 医生添加
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function insert()
    {
        $this->QcInfo = $this->findQcInfo();
        if ($this->input->post('qc_code_name') != $this->qc_code) {
            echo $this->result_lib->setErrorsJson('二维码信息不正确');
            exit();
        }
        
        $id = $this->QcInfo['id']; // 二维码id
        if ($this->QcInfo['status'] == 3) {
            echo $this->result_lib->setErrorsJson('此二维码已绑定其他医师');
            exit();
        }
        
        $this->db->trans_begin();
        $this->load->model('doctor_model');
        // 添加医师主要信息
        $re_insert_id = $this->doctor_model->insert(array(
            'status' => 1,
            'qc_code' => trim(htmlspecialchars($this->input->post('qc_code_name'))),
            'add_time' => date('Y-m-d H:i:s'),
            'dealer_id' => $this->QcInfo['dealer_id']
        ));
        
        // 添加医师详情信息
        $re2 = $this->doctor_model->insertDetail(array(
            'doctor_id' => $re_insert_id,
            'doctor_name' => trim(htmlspecialchars($this->input->post('doctor_name'))),
            'position' => trim(htmlspecialchars($this->input->post('position'))),
            'department' => trim(htmlspecialchars($this->input->post('department'))),
            'hospital' => trim(htmlspecialchars($this->input->post('hospital'))),
            'add_time' => date('Y-m-d H:i:s')
        ));
        
        // 更新二维码信息
        
        $re3 = $this->qc_code_model->update($id, array(
            'status' => 3,
            'dealer_id' => $this->QcInfo['dealer_id'],
            'code' => md5(trim(htmlspecialchars($this->input->post('code'))))
        ));
        if ($re_insert_id && $re2 && $re3) {
            $this->db->trans_commit();
            echo $this->result_lib->setInfoJson('绑定成功');
            exit();
        }
        $this->db->trans_rollback();
        echo $this->result_lib->setErrorsJson('绑定失败');
    }
    
    /*
     * 验证医生
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function showCheckCode()
    {
        $this->loadView('check_doctor.html', $this->page_data);
    }
    
    /*
     * 验证医生执行
     * liting
     * 2016/05/20 12:30:00
     *
     */
    public function doCheckCode()
    {
        $this->QcInfo = $this->findQcInfo();
        $code = $this->input->get('code');
        $this->load->model('doctor_model');
        if( $this->QcInfo && $this->QcInfo['code'] == md5($code) ){
            $doctorInfo = $this->getDoctorInfo();
            if( $doctorInfo['open_id'] =='' ){
                $re = $this->doctor_model->update($doctorInfo['id'],array('open_id'=>$this->open_id));
                if($re){
                    $this->qc_code_model->update($this->QcInfo['id'],array('status'=>4));
                    $this->session->set_userdata(array('doctor_id'=>$doctorInfo['id']));
                    echo $this->result_lib->setInfoJson('验证成功!');exit;
                }
            }else{
                if($this->open_id == $doctorInfo['open_id'] ){
                    $this->doctor_id = $doctorInfo['id'];
                    $this->session->set_userdata(array('doctor_id'=>$doctorInfo['id']));
                    echo $this->result_lib->setInfoJson('验证成功!');exit;
                }else{
                    echo $this->result_lib->setErrorsJson('此微信号码与绑定身份不符!');exit;
                }
            }
        }
        echo $this->result_lib->setErrorsJson('验证码不正确!');exit;
    }
    
    /*
     * 修改验证码
     * liting
     * 2016/05/24 15:30:00
     *
     */
    public function resetCode()
    {
        $this->loadView('change_yzm.html', $this->page_data);
    }
    /*
     * 修改验证码
     * liting
     * 2016/05/24 15:30:00
     *
     */
    public function doResetCode()
    {
        $this->QcInfo = $this->findQcInfo();
        if(!$this->QcInfo || $this->QcInfo['qc_code_name'] != $this->input->get('qc_code')){
            echo $this->result_lib->setErrorsJson('二维码信息不正确!');exit;
        }
        
        if(!$this->QcInfo || $this->QcInfo['code'] != md5(trim(htmlspecialchars($this->input->get('code'))))){
            echo $this->result_lib->setErrorsJson('原验证码不正确!');exit;
        }
        
        $doctorInfo = $this->getDoctorInfo();
        if( !$doctorInfo || $doctorInfo['qc_code'] != $this->input->get('qc_code') ){
            echo $this->result_lib->setErrorsJson('账号信息不正确!');exit;
        }
        
        if($this->qc_code_model->update($this->QcInfo['id'],array('code'=>md5(trim(htmlspecialchars($this->input->get('newCode'))))))){
            echo $this->result_lib->setInfoJson('修改成功!');exit;
        }
        echo $this->result_lib->setErrorsJson('修改失败!');exit;
    }
    

    public function showSuccess()
    {
        $this->loadView('insert_doctor_success.html', $this->page_data);
    }

    public function showFaild()
    {
        $this->page_data['error_msg'] = trim(htmlspecialchars($this->input->get('status')));
        $this->loadView('insert_doctor_faild.html', $this->page_data);
    }
}










