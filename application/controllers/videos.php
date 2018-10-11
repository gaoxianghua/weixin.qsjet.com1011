<?php
require_once ('application/core/MY_Controller.php');

/**
 * 用户
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
class videos extends MY_Controller_Site
{

    public $open_id = '';

    public function __construct()
    {
        parent::__construct();
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
    }

    public function index()
    {
        $this->load->model('videos_model');
        $result = $this->videos_model->count('1');
        $this->page_data['total'] = $result['total'];
        $this->loadView('videos.html', $this->page_data);
    }

    public function getVideosList()
    {
        $page = $this->input->get('page')? $this->input->get('page')-1:'0';
        $per_page = 8;
        $type_id = $this->input->get('status') ? $this->input->get('status') : '1';
        $this->load->model('videos_model');
        $videos = $this->videos_model->findByType($type_id,$page,$per_page);
        //var_dump($videos);die;
        $result = $this->result_lib->setInfo($videos);
        //var_dump($result);die;
        echo json_encode($result);
    }
}










