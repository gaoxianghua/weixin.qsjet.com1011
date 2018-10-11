<?php
require_once ('application/core/MY_Controller.php');

/**
 * 产品
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
class project extends MY_Controller_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('userinfo');
        $this->page_data['open_id'] = $this->open_id = $this->input->get('open_id') . rand(0, 1999);
        $this->page_data['download_url'] = $this->config->item('download_url');
        $this->page_data['qcode_url'] = $this->config->item('qcode_url');
    }

    public function index()
    {
        $this->load->model('project_model');
        $result = $this->project_model->count();
        $this->page_data['total'] = $result['total'];
        $this->loadView('project.html', $this->page_data);
    }
    
    public function detail()
    {
        if( $this->input->get('redirect_url') ){
            header('location:' . $this->input->get('redirect_url') );exit;
        }
    }
    
    
    public function getProject()
    {
        $page = $this->input->get('page')? $this->input->get('page'):'0';
        $per_page = 8;
        $this->load->model('project_model');
        $result = $this->project_model->findAll($page,$per_page);
        echo $this->result_lib->setInfoJson($result);
    }
}

