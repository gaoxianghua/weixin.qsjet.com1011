<?php
require_once ('application/core/MY_Controller.php');

/**
 * CMEF展会
 * Enter description here .
 *
 */
class cmef extends MY_Controller_Site
{
    public function __construct()
    {
        parent::__construct();
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
    }
    public function index(){

        if(!empty($_GET['open_id'])){
            $openid = $_GET['open_id'];
            $query = $this->db->query("SELECT id,open_id,status FROM cmef WHERE open_id = '$openid'");
            $row = $query->row_array();

            if (isset($row))
            {
                if(!empty($row['id']) && $row['status'] == 2){
                    echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/link?openid='+'$openid';</script>";
                    //$this->link();
                } else if(!empty($row['id']) && $row['status'] == 1){
                    $openid = $row['open_id'];
                    echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/checkcode?open_id='+'$openid';</script>";
                }
            }

            $data['openid'] = $openid;
            $this->load->view('cmef.html',$data);
        } else{
            echo "<script type='text/javascript'>alert('未知错误');</script>";exit;
        }
    }
    /*
     * 保存信息入库
     */
    public function submit()
    {
        $data['username'] = $_POST['username'];
        $data['phone'] = $_POST['phone'];
        $data['open_id'] = $_POST['openid'];
        $dataod= $_POST['openid'];
        $data['company'] = $_POST['company'];
        $data['email'] = $_POST['email'];
        $data['job'] = $_POST['job'];
        $data['createtime'] = time();
        $data['status'] = 1;
        $data['sex'] = '';
        $data['age'] = '';
        $data['address'] = $_POST['pro'] . $_POST['city'] . $_POST['address'];
        $result = $this->db->insert('cmef',$data);
        if($result){
            //echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/link?openid='+'$data_ip';</script>";
            //echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/checkcode;</script>";
            //echo '<meta http-equiv="Refresh" content="1; url=http://weixin.qsjet.com/cmef/checkcode?open_id=$dataod">';
            echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/checkcode?open_id='+'$dataod';</script>";
        } else {
            echo "<script type='text/javascript'>alert('添加错误，请返回修改。'); history.back();</script>";
        }
    }
    //验证码验证
    public function checkcode()
    {
        if(!empty($_GET)){
            $data['openid'] = $_GET['open_id'];
            $this->load->view('checkcode.html',$data);
        }

    }
    public function checknum(){
        $num = $_POST['code'];
        if($num =='0520'){
            $openid = $_POST['openid'];
            $data=array(
                'status'=>2
                        );
        $this->db->where('open_id',$openid);
        $this->db->update("cmef",$data);
            echo "<script type='text/javascript'>location.href='http://weixin.qsjet.com/cmef/link?openid='+'$openid';</script>";
        } else{
            echo "<script language=javascript> alert('请向工作人员索要验证码'); history.back();</script>";
        }
    }
    //读取资料(test)
    private function data()
    {
        $this->load->view('data.html');
    }
    //技术推介pdf
    public function pdf_data()
    {
        $this->load->view('data.html');
    }
    //学术推广pdf
    public function science()
    {
        $this->load->view('xueshu.html');
    }
    //qbox介绍
    public function qbox()
    {
        $this->load->view('qbox.html');
    }
    //无针优势
    public function advantage()
    {
        $this->load->view('adv.html');
    }

    //链接
    public function link()
    {
        if(!empty($_GET['openid'])){
            $this->load->view('link.html');
        } else {
            echo "<script type='text/javascript'>alert('页面错误'); history.back();</script>";
        }
    }
   /* public function download()
    {
        $filename = 'cmef.pdf';
        $num = 'http://weixin.qsjet.com/datas.pdf';
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        @readfile($num);
        exit;
    }*/

}