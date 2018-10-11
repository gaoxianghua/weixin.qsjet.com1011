<?php
header("Content-type:text/html;charset=utf-8");
require_once ('system/core/Controller.php');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('result_lib');
    }

    public function __call($method, $args)
    {
        if (is_callable(array(
            $this->result_lib,
            $method
        ))) {
            return call_user_func_array(array(
                $this->result_lib,
                $method
            ), $args);
        }
        return call_user_func_array(array(
            $this,
            $method
        ), $args);
    }

    public function _output($output)
    {
        if ($output && is_array($output))
            echo json_encode($output);
        else
            echo $output;
        return;
    }
}

/**
 *
 * Enter description here ...
 *
 * @author
 *
 * @since
 *
 * @property result_lib $result_lib
 */
class MY_Controller_Site extends CI_Controller
{

    public $page_data = array();

    public $provider_user;

    public $demander_user;
    
    public $parent_permission;

    public function __construct()
    {
        parent::__construct();
        header('Expires: 0');
        header('Pragma: no-cache');
        header('Cache-Control: no-cache, no-store');
        $this->output->enable_profiler(FALSE);
        $this->page_data['menu'] = '';
        $this->page_data['sidebar'] = '';
        if (! session_id())
            session_start();
        $this->page_data['attributes'] = $this->getCasAttributes();
        $this->load->model('auth_permission_model');
        $this->load->helper('cookie_helper');
        // 查询权限
         $this->page_data['my_permission'] = $this->auth_permission_model->findAll();
         //所有父级菜单
         $this->parent_permission = $this->auth_permission_model->findParent();
        // $product_id = $this->uri->segment(2);
    }

    public function __call($method, $args)
    {
        if (is_callable(array(
            $this->result_lib,
            $method
        ))) {
            return call_user_func_array(array(
                $this->result_lib,
                $method
            ), $args);
        }
        return call_user_func_array(array(
            $this,
            $method
        ), $args);
    }

    public function getCasAttributes()
    {
        $phpCAS = isset($_SESSION['phpCAS']) ? $_SESSION['phpCAS'] : '';
        if ($phpCAS && isset($phpCAS['attributes']) && isset($phpCAS['attributes'])) {
            return $phpCAS['attributes'];
        }
        return false;
    }

    function loadView($view)
    {
        $this->load->view($view, $this->page_data);
    }
}

class MY_Admin_Site extends MY_Controller_Site
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
}
?>