<?php
require_once ('application/core/MY_Controller.php');

/**
 *
 * Enter description here ...
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class common extends MY_Controller_Site
{

    public $user_id;

    public $doc_id;

    public $open_id;

    public $rule_type;

    public $is_orders;

    public $is_home;

    public function __construct()
    {
        parent::__construct();
        
        if (! $this->input->get('open_id')) {
            echo 1;
            die();
            redirect('login');
        }
        
        $CI = & get_instance();
        $CI->config->load('wechat');
        $wechat_config = $CI->config->item('wechat');
        $this->token = $wechat_config['token'];
        $this->appid = $wechat_config['appid'];
        $this->appsecret = $wechat_config['appsecret'];
        $this->load->library('curls');
        $this->load->library('wechatlist');
        $this->load->model('wechat_access_token_model');
        
        $this->check();
        $this->session->userdata('all_rand', rand(100, 999));
        if ($this->session->userdata('status') == 0) { // 此用户被禁用
            redirect('login?open_id=' . $this->input->get('open_id'));
        }
        $this->page_data['upload_base'] = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->config->item('download_url');
    }

    public function check()
    {
        $this->open_id = $this->input->get('open_id');
        if (! (! $this->session->userdata('user_id') && ! $this->session->userdata('doc_id')) || $this->open_id != $this->session->userdata('open_id')) {
            if (! $this->findLoginInfo()) {
                // redirect('login?open_id=' . $this->input->get('open_id'));
            }
        }
        
        if ($this->session->userdata('user_id')) {
            $this->user_id = $this->session->userdata('user_id');
        }
        if ($this->session->userdata('doc_id')) {
            $this->doc_id = $this->session->userdata('doc_id');
        }
        $this->page_data['rule_type'] = $this->rule_type = $this->session->userdata('rule_type');
        $this->page_data['rule_name'] = $this->session->userdata('rule_type') == 'doctor' ? '我是诊疗师' : '我是客户';
    }

    public function findLoginInfo()
    {
        $this->load->model('user_open_model');
        $this->load->model('doctor_open_model');
        $user_open_info = $this->user_open_model->findOneByOpen($this->open_id);
        $doctor_open_info = $this->doctor_open_model->findOneByOpen($this->open_id);
        
        if (! isset($user_open_info[0]) && ! isset($doctor_open_info[0])) {
            return false;
        }
        
        if (isset($doctor_open_info[0]) && ! empty($doctor_open_info[0])) {
            $this->load->model('doctor_model');
            $doctor_info = $this->doctor_model->loginFindOne($doctor_open_info[0]['doc_id']);
            
            if (! isset($doctor_info[0]) || $doctor_open_info[0]['status'] == '0') {
                return false;
            }
            
            $this->rule_type = 'doctor';
            $this->is_orders = $doctor_info[0]['is_orders'];
            $this->is_home = $doctor_info[0]['is_home'];
            $data = array(
                'rule_type' => 'doctor',
                'doc_id' => $doctor_info[0]['id'],
                'username' => $doctor_info[0]['doc_name'],
                'mobile' => $doctor_info[0]['mobile'],
                'status' => $doctor_info[0]['status']
            );
            $this->session->set_userdata($data);
            return true;
        }
        
        if (isset($user_open_info[0]) && ! empty($user_open_info[0])) {
            $this->load->model('user_model');
            $user_info = $this->user_model->findOneById($user_open_info[0]['user_id']);
            if (! isset($user_info[0]) || $user_open_info[0]['status'] == '0') {
                return false;
            }
            $this->rule_type = 'user';
            $data = array(
                'rule_type' => 'user',
                'user_id' => $user_info[0]['id'],
                'username' => $user_info[0]['username'],
                'mobile' => $user_info[0]['mobile'],
                'status' => $user_info[0]['status']
            );
            $this->session->set_userdata($data);
            return true;
        }
    }
}













