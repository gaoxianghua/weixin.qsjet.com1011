<?php
require_once ('application/core/MY_Controller.php');
/**
 * 患者登录注册
 * Enter description here .
 *
 * @author
 *
 * @property
 *
 */
class login extends MY_Controller_Site
{
    public $open_id = '';
    public $account = '';
    public $code;
    public $redirect_url = '';
    public $type = '';

    public function __construct()
    {
        parent::__construct();
        if($this->input->get('open_id')){
            $this->open_id = $this->page_data['open_id'] = $this->input->get('open_id');
        }

        $this->redirect_url = $this->page_data['redirect_url'] = $this->input->get('redirect_url')?$this->input->get('redirect_url'):'';
        $this->load->model('user_model');
    }


    /****************** 登录部分 **********************/
    /*
     * 患者登录展示页
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function index()
    {
        $this->loadView('showLogin.html', $this->page_data);
    }

    /*
     * 患者登录处理
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function doLogin()
    {
        $this->account = $this->input->get('account');
        $this->password = md5($this->input->get('password'));
        if( $user = $this->checkLogin() ){
            if($this->saveUserInfo($user['id'])){
                if( $this->type == 'web' ){
                    $this->session->set_userdata(array('user_id'=>$user['id']));
                }
                if($this->redirect_url&&$this->redirect_url!=''){
                    echo json_encode(array('result_code'=>'300','info'=>'登录成功','token'=>$user['token'],'url'=>$this->redirect_url));exit;
                }
                echo json_encode(array('result_code'=>'200','info'=>'登录成功','token'=>$user['token'],'url'=>base_url('user?token=').$user['token']));exit;
            }
            echo $this->result_lib->setErrorsJson('登录失败');exit;
        }
        echo $this->result_lib->setErrorsJson('账号或密码不正确');exit;
    }


    /****************** 注册部分 **********************/

    /*
     * 患者登录展示页
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function showRegister()
    {
        $this->loadView('showRegister.html', $this->page_data);
    }

    /*
     * 患者注册处理
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function doRegister()
    {
        $this->account = $this->input->post('account');
        $this->password = md5($this->input->post('password'));
        $this->code = $this->input->post('code');

        if(!$this->checkCodeTime()){
            echo $this->result_lib->setErrorsJson('验证码已过期');exit;
        }
        if(!$this->check_code()){
            echo $this->result_lib->setErrorsJson('验证码不正确');exit;
        }
        if( $this->session->userdata('register_account') && $this->account != $this->session->userdata('register_account')){
            echo $this->result_lib->setErrorsJson('与所发验证码的手机号不一致');exit;
        }
        if ($this->checkAccountOnly()) {
            echo $this->result_lib->setErrorsJson('该账号已注册');exit;
        }

        $this->insertData = $this->input->post();
        $this->createData();
        die;
    }

    /*
     * 患者注册添加入库
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function insert()
    {
        $this->open_id = $this->input->get('open_id')?$this->input->get('open_id'):'';
        $this->user_model->updateOpen($this->open_id);
        $user = array(
            'open_id'=>$this->open_id,
            'status'=>'0',
            'add_time'=>date('Y-m-d H:i:s'),
            'account'=>$this->account,
            'password'=>$this->password,
            'token'=>md5($this->code.'#KUAISHUER#'.$this->account),
            'mole'=>strtotime(date('H:i:s'))%3
        );
        $this->db->trans_begin();
        $isnert_id = $this->user_model->insert($user);
        $userInfo = array(
            'user_id'=>$isnert_id,
            'username'=>$this->insertData['username'],
            'gender'=>$this->insertData['gender']?$this->insertData['gender']:'',
            'mobile'=>$this->insertData['account'],
        );
        $this->load->model('user_info_model');
        $re = $this->user_info_model->insert($userInfo);
        if($isnert_id&&$re){
            $this->db->trans_commit();
            return $user['token'];
        }
        $this->db->trans_rollback();
        return false;
    }


    /****************** 忘记密码部分 **********************/
    /*
     * 忘记密码展示
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function showForget()
    {
        $this->loadView('forgetPwd.html', $this->page_data);
    }

    /*
     * 忘记密码执行修改
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function doForget()
    {
        $this->code = $this->input->get('code');
        $this->account = $this->input->get('account');
        if(!$this->session->userdata('code')){
            echo $this->result_lib->setErrorsJson('请发送验证码');exit;
        }
        if(!$this->check_code()){
            echo $this->result_lib->setErrorsJson('验证码不正确');exit;
        }
        if(!$this->checkCodeTime()){
            echo $this->result_lib->setErrorsJson('验证码已过期');exit;
        }
        if($this->account != $this->session->userdata('forget_account')){
            echo $this->result_lib->setErrorsJson('账号信息不一致');exit;
        }
        if(!$this->checkAccountOnly()){
            echo $this->result_lib->setErrorsJson('该账户尚未注册');exit;
        }

        $this->session->unset_userdata('code');
        $this->session->set_userdata(array('account'=>$this->account));
        echo json_encode(array('result_code'=>'200','info'=>'验证成功','url'=>base_url('login/showSetPassword?account=').$this->account));exit;
    }

    public function showSetPassword()
    {
        $this->page_data['account'] = $this->input->get('account');
        $this->loadView('setPwd.html', $this->page_data);
    }

    public function doSetPassword()
    {
        $account = $this->input->get('account');
        //$account = $_GET['account'];
        $password = md5($this->input->get('password'));
        $re = $this->user_model->updatePassword($account,$password);
        if($re){
            $this->session->unset_userdata('forget_account');
            echo $this->result_lib->setInfoJson('修改成功');exit;
        }
        echo $this->result_lib->setInfoJson('修改失败');exit;
    }


    /****************** 验证部分 **********************/
    /*
     * 注册数据检测
     * liting
     * 2016/06/04 15:00:00
     *
     */
    public function createData()
    {
        if(!is_array($this->insertData) || empty($this->insertData)){
            echo $this->result_lib->setErrorsJson('账号格式不正确');exit;
        }
        foreach($this->insertData as $k=>$v){
            $item = 'check_' . $k;
            $this->load->library('userinfo');
            $this->userinfo->check_account($v);
            if ($lang = $this->userinfo->$item(trim(htmlspecialchars($v)))) {
                echo $this->result_lib->setErrorsJson($lang);exit;
            }
            $this->insertInfo[$k] = trim(htmlspecialchars($v));
        }
        if($token = $this->insert()){
            $this->session->unset_userdata('code');
            $this->session->unset_userdata('register_account');
            if($this->redirect_url&&$this->redirect_url!=''){
                echo json_encode(array('result_code'=>'300','info'=>'注册成功','token'=>$token,'url'=>$this->redirect_url));exit;
            }
            echo json_encode(array('result_code'=>'200','info'=>'注册成功','token'=>$token,'url'=>base_url('user?token=').$token));exit;
        }
        echo $this->result_lib->setErrorsJson('注册失败');exit;
    }


    /*
     * 注册发送验证码
     * liting
     * 2016/06/04 15:00:00
     *
     */
    public function registerSendCodes()
    {
        $verify = $this->input->get('verify');
       if (! $verify || $verify != $_SESSION['verify_code']) {
           echo $this->result_lib->setErrorsJson('图像验证码错误');exit;
        }
        $this->account = $this->input->get('account');
        if($this->checkAccountOnly()){
            echo $this->result_lib->setErrorsJson('该账户已注册');exit;
        }
        $this->load->library('sendmess');
        //$re = $this->sendmess->duanxin($this->account,'27800');
        $re = $this->sendmess->sends($this->account,'27800');
        if( $re ){
            $this->session->set_userdata(array('code'=>$re['code'],'time_date'=>$re['time_date'],'register_account'=>$this->account));
            echo $this->result_lib->setInfoJson('发送成功');exit;
        }
        echo $this->result_lib->setErrorsJson('发送失败');exit;
    }

    /*
     * 忘记密码发送验证码
     * liting
     * 2016/06/04 15:00:00
     *
     */
    public function forgetSendCode()
    {
        $this->account = $this->input->get('account');
        if(!$this->checkAccountOnly()){
            echo $this->result_lib->setErrorsJson('该账户尚未注册');exit;
        }
        $this->load->library('sendmess');
        $re = $this->sendmess->sends($this->account,'27801');
        if( $re ){
            $this->session->set_userdata(array('code'=>$re['code'],'time_date'=>$re['time_date'],'forget_account'=>$this->account));
            echo $this->result_lib->setInfoJson('发送成功');exit;
        }
        echo $this->result_lib->setErrorsJson('发送失败');exit;
    }

    /*
     * 用户登录账号密码验证
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function checkLogin()
    {
        return $this->user_model->checkLogin($this->account,$this->password);
    }

    /*
     * 用户登录账号唯一性验证
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function checkAccountOnly()
    {
        return $this->user_model->findOnlyOne($this->account);
    }

    /*
     * 用户open_id验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkRegister()
    {
        return $this->user_model->findOneByOpenId($this->open_id);
    }

    /*
     * 验证码验证
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function check_code()
    {
        if( $this->session->userdata('code') == $this->code ){
            return true;
        }
        return false;
    }

    /*
     * 验证码验证
     * liting
     * 2016/06/02 16:00:00
     *
     */
    public function checkCodeTime()
    {
        if(strtotime($this->session->userdata('time_date')) < strtotime(date('Y-m-d H:i:s'))-60*30){
            return false;
        }
        return true;
    }

    /*
     * 用户信息更新
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function saveUserInfo($user_id)
    {
        if( $user_id == '' ){
            return true;
        }
        return $this->user_model->saveUserInfo($user_id,$this->open_id);
    }

    /*
     * 用户退出
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function logOut()
    {
        $open_id = $this->input->get('open_id');
        if($open_id&&$open_id){
            if($this->user_model->updateOpen($open_id)){
                echo $this->result_lib->setInfoJson(base_url('login?open_id='.$open_id));exit;
            }
            echo $this->result_lib->setErrorsJson('退出失败');exit;
        }
        echo $this->result_lib->setInfoJson(base_url('login'));exit;
    }

   
}










