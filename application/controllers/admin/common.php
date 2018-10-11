<?php
require_once ('application/core/MY_Controller.php');

/**
 * ������¼
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class common extends MY_Controller_Site
{

    public $array = '';

    public $permissionsName = '';

    public function __construct()
    {
        parent::__construct();
        $CI = &get_config();
        if (! $this->session->userdata('admin_id')) {
            redirect(base_url('admin/login'));
        }
    }

    public function checkType()
    {
        if ($this->session->userdata('type') == '0') {
            $this->load->library('admin/admin_user');
            $this->admin_user->getQcCode();
        }
    }
}
