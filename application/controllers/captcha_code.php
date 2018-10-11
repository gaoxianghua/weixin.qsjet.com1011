<?php
require_once ('application/core/MY_Controller.php');

/**
 * 
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
class captcha_code extends MY_Controller_Site
{

    public function __construct()
    {
        parent::__construct();
    }

    public function verify_image()
    {
        $conf['name'] = 'verify_code'; // 作为配置参数
        $this->load->library('captcha', $conf);
        $this->captcha->show();
        $yzm_session = $this->session->userdata('verify_code');
        echo $yzm_session;
    }
}










