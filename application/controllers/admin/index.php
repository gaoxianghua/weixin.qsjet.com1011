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
 */
class index extends MY_Controller_Site
{

    public function __construct()
    {
        parent::__construct();
        if (! $this->session->userdata('admin_id')) {
            redirect(base_url('admin/login'));
        }
        $this->page_data['menu_flag'] = "index";
        $this->page_data['second_menu_flag'] = "spaceuser";
        $this->page_data['permissions'] = $this->session->userdata('permissions');
    }

    public function index()
    {
        $this->page_data['result'] = 'index';
        $this->page_data['table_title_name'] = '';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = '管理端首页 ';
        $this->page_data['detail'] = 'index_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'index_second_menu.php';
        $this->load->library('zwyl/zwyl_form');
        $this->loadView('admin/index');
    }

    public function per_errors()
    {
        $this->page_data['result'] = 'index';
        $this->page_data['table_title_name'] = '';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = '错误 ';
        $this->page_data['detail'] = 'index_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'index_second_menu.php';
        $this->load->library('zwyl/zwyl_form');
        $this->loadView('admin/index');
    }

    public function password()
    {
        $this->page_data['result'] = "";
        $this->page_data['table_title_name'] = '';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = '密码修改 ';
        $this->page_data['detail'] = 'index_password_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'index_second_menu.php';
        $this->load->library('zwyl/zwyl_form');
        $this->loadView('admin/index');
    }

    public function doPassword()
    {
        $pwd = $this->input->post('pwd');
        $newPwd = $this->input->post('newPwd');
        $newPwd = md5($newPwd);
        $admin_id = $this->session->userdata('admin_id');
        $this->load->model('admin/admin_user_model');
        $result = $this->admin_user_model->findOneById($admin_id);
        if (empty($result)) {
            echo $this->result_lib->setErrorsJson('登录信息不正确');
            exit();
        }
        if (md5($pwd) != $result['password']) {
            echo $this->result_lib->setErrorsJson('原始密码不正确');
            exit();
        }
        
        $re = $this->admin_user_model->update($admin_id, array(
            'password' => $newPwd
        ));
        
        if ($re) {
            echo $this->result_lib->setInfoJson('修改成功');
            exit();
        } else {
            echo $this->result_lib->setErrorsJson('修改失败');
            exit();
        }
    }
}
