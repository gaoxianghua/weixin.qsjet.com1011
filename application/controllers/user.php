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
class user extends MY_Controller_Site
{

    public $user_id = '';

    public $open_id = '';

    public $userInfo = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('userinfo');
        $this->load->model('user_model');
        $this->page_data['open_id'] = $this->open_id = $this->input->get('open_id');
        $this->page_data['user_id'] = $this->user_id = $this->input->get('user_id');
        $this->page_data['token'] = $this->token = $this->input->get('token');
        if(!$this->user = $this->checkRegister()){
            redirect(base_url('login?open_id='.$this->open_id.'&redirect_url='.__CLASS__));
            return;
        }
    }

                                /**********************  展示部分  ************************/
    public function index()
    {
        $this->userInfo = $this->checkUserInfoFinshed($this->user['id']);
        $this->page_data['userInfo'] = $this->userInfo;
        if($this->input->get('redirect_url')){
            $this->page_data['redirect_url'] = $this->input->get('redirect_url');
        }
        $this->page_data['userProject'] = $this->user_model->getUserPro($this->user['id']);
        //查询快递信息
        $ex = $this->user_model->getEx($this->user['id']);
        $this->page_data['data'] = $ex;
        //$datas['data'] = 1111;
        if(!empty($_GET['open_id'])){
            $opid = $_GET['open_id'];
            $this->page_data['open_id'] = $opid;
        }

        $this->loadView('user_info.html',$this->page_data);
        //$this->load->view('',$datas);
    }

    /*
     * 用户信息添加展示 -- 扫公众号二维码
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
    public function showUserInsert()
    {
//         if($this->checkUserInfoFinshed($this->user_id)){
//             redirect(base_url('user?user_id='. $this->user_id .'&open_id=' . $this->input->get('open_id')));
//             exit();
//         }
        $this->loadView('add_user_insert.html', $this->page_data);
    }

                                        /***************** 入库部分  *********************/
    /*
     * 用户信息添加处理
     * liting
     * 2016/06/02 18:30:00
     * 传入参数：
     * 无；
     *
     */
//     public function doUserInsert()
//     {
//         if($userinfo = $this->checkUserInfoFinshed($this->user_id)){
//             echo $this->result_lib->setErrorsJson('该用户信息已完善');exit;
//         }
//         if(!$this->userinfo->insertUserInfo($this->user_id)){
//             echo $this->result_lib->setErrorsJson('该用户信息完善失败');exit;
//         }
//         echo $this->result_lib->setInfoJson('信息更新成功');exit;
//     }

                                      /***************** 检测部分  *********************/
    /*
     * 用户所有信息检测
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * 无；
     *
     */
//     public function checkAllUserInfo()
//     {
//         //没有注册（open_id不存在）
//         if (! $user = $this->checkRegister($this->open_id)) {     
//             redirect(base_url('login?open_id=' . $this->input->get('open_id')));
//             exit();
//         }
//         $userInfo = $this->checkUserInfoFinshed($user['id']);
//         $this->session->set_userdata(array(
//             'user_id'=>$user['id'],
//             'open_id'=>$user['open_id'],
//             'token'=>$user['token'],
//             'account'=>$user['account'],
//             'status'=>$user['status'],
//         ));
//         return $userInfo;
//     }
    
    /*
     * 检测用户是否注册
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkRegister()
    {
        if( $this->token ){
            return $this->user_model->findOneToken($this->token);
        }
        if( $this->open_id ){
            return $this->user_model->findOneByOpenId($this->open_id);
        }
    }
    
    /*
     * 检测用户信息是否完善
     * liting
     * 2016/06/02 17:30:00
     * 传入参数：
     *
     */
    public function checkUserInfoFinshed($user_id)
    {
        $this->load->model('user_info_model');
        //检测信息是否完善
        return $this->user_info_model->findOneByUid($user_id);
    }
    
    /*
     * 用户信息添加成功
     * liting
     * 2016/05/17 18:00:00
     * 传入参数：
     * 无；
     *
     */
    public function showSuccess()
    {
        $this->loadView('insert_user_success.html', $this->page_data);
    }

    /*
     * 用户信息添加失败
     * liting
     * 2016/05/17 18:00:00
     * 传入参数：
     * 无；
     *
     */
    public function showFaild($status='')
    {
        $this->page_data['error_msg'] = $this->input->get('status')?$this->input->get('status'):$status;
        $this->loadView('insert_user_faild.html', $this->page_data);
    }
}










