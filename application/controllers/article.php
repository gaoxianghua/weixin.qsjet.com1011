<?php
require_once ('application/core/MY_Controller.php');

/**
 * 患者故事
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
class article extends MY_Controller_Site
{

    public $open_id = '';

    public function __construct()
    {
        parent::__construct();
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
        $this->page_data['qcode_url'] = $this->qcode_url = $this->config->item('qcode_url');
    }
    
    /*
     * 患者故事展示
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function index()
    {
        $this->load->model('article_model');
        $result = $this->article_model->count();
        $this->page_data['total'] = $result['total'];
        $this->loadView('article.html', $this->page_data);
    }

    public function getList()
    {
        $page = $this->input->get('page')? $this->input->get('page'):'0';
        $per_page = 7;
        $this->load->model('article_model');
        $result =  $this->article_model->findAll($page,$per_page);
        echo $this->result_lib->setInfoJson($result);
    }

    public function detail()
    {
        $id = $this->input->get('id');
        $this->page_data['result'] = $this->article_model->findOne();
        $this->loadView('article_detail.html', $this->page_data);
    }
}










