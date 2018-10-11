<?php
require_once ('application/core/MY_Controller.php');

/**
 * 首页
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 *
 */
class login extends MY_Controller_Site
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
      
        $this->page_data['err'] = $this->input->get('err') ? $this->input->get('err') : '';
        $this->loadView('admin/login.html', $this->page_data);
    }

    /*
     * 管理端登录处理
     * liting
     * 2016/05/10 12:00:00
     *
     */
    public function checkLogin()
    {
        $this->load->library('admin/admin_user');
        if (! $this->input->get() || ! $this->admin_user->checkData()) {
            echo $this->result_lib->setErrorsJson('账号或密码输入有误');
            exit();
        }
        echo $this->admin_user->checkLogin();
    }

    /*
     * 经销商登录信息查询
     * liting
     * 2016/05/10 15:30:00
     *
     */
    public function getDealer()
    {
        $this->load->library('admin/admin_dealer');
        return $this->admin_dealer->getDealerInfo(array(
            'admin_id' => $this->session->userdata('admin_id')
        ));
    }

    /*
     * 管理端退出
     * liting
     * 2016/05/10 15:00:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function logout()
    {
        $data = array(
            'admin_id' => '',
            'admin_name' => '',
            'email' => '',
            'access' => ''
        );
        $this->session->sess_destroy($data);
        redirect(base_url('admin/login'));
    }
}

?>
