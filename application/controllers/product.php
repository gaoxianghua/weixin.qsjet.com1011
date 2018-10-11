<?php
require_once ('application/core/MY_Controller.php');

/**
 * 注册产品展示
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
class product  extends MY_Controller_Site
{
    const API_OPEN = "https://open.weixin.qq.com/connect/oauth2/authorize";
    public $open_id = '';

    public function __construct()
    {
        parent::__construct();
        $CI = & get_instance();
        $this->page_data['redirect'] = base_url('register_project');
        if($this->input->get('open_id')){
            $this->page_data['redirect'] = base_url('register_project?open_id='.$this->input->get('open_id'));
        }
        if($this->input->get('token')){
            $this->page_data['redirect'] = base_url('register_project?token='.$this->input->get('token'));
        }
        $CI->config->load('wechat');
        $wechat_config = $CI->config->item('wechat');
        $this->appid = $wechat_config['appid'];
    }

    public function index()
    {
        $Agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($Agent,'MicroMessenger')){
            $redirect_uri = urldecode(base_url('wechat_oAuth2/index'));
            $this->page_data['redirect'] = self::API_OPEN . '?appid=' . $this->appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=registerProject#wechat_redirect';
        }
        $this->loadView('product_register.html', $this->page_data);
    }
    
    public function player()
    {
        $this->loadView('product_videos.html', $this->page_data);
    }

    //conn
    public function connect()
    {
        $this->loadView('product_conn.html', $this->page_data);
    }
    //确认收货
    public function confrimGoogs()
    {
        if(!empty($_POST)){
            $id = $_POST['id'];
            $open_id = $_POST['open_id'];
            $data['status'] = 4;
            $data['updatetime'] = time();
            $this->load->model('project_model');
            $result = $this->project_model->conGoods($id,$data);
            if($result){
               echo "<script language=javascript> alert('收货成功！');location.href='http://weixin.qsjet.com/user?open_id=$open_id';</script>";
            }else{
                echo "<script language=javascript> alert('收货失败！'); history.back();</script>";
            }
        }

    }
}










