<?php
require_once ('application/core/MY_Controller.php');
require_once ('application/libraries/lib/nusoap.php');

/**
 * 产品注册
 * Enter description here .
 *
 * liting
 * 2016/06/24 16:30:00
 *
 * @author
 *
 * @property
 *
 */
class register_project extends MY_Controller_Site
{

    public $token = '';
    public $user_id = '';
    public $open_id = '';
    public $checkProject_url = '';

    public function __construct()
    {
        parent::__construct();
        
        if(!$this->input->get('open_id')&&!$this->input->get('token')){
            redirect(base_url('login?redirect_url=' . __CLASS__ ));
            return;
        }
        
        if(!$this->input->get('token')){
            if(!$this->user = $this->checkWechatRegister($this->input->get('open_id'))){
                redirect(base_url('login?open_id=' . $this->input->get('open_id') . '&redirect_url=' . __CLASS__ ));
            }
        }else{
            if(!$this->user = $this->checkToken($this->input->get('token'))){
                redirect(base_url('login?redirect_url=' . __CLASS__ ));
                return;
            }
        }
        $this->token = $this->page_data['token'] = $this->user['token'];
        $this->user_id = $this->page_data['user_id'] = $this->user['id'];
    }

    /**
     * ******************** 公共展示部分 ***********************
     */
    
    /*
     * 注册产品提交页展示
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function index()
    {
        $this->loadView('showProject.html', $this->page_data);
    }

    /**
     * ******************** 检测部分 ***********************
     */
    
    /*
     * 检测患者产品编号格式
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkNum($project_num)
    {
        return preg_match('/^[0-9]{16}/u', $project_num);
    }

    /*
     * 检测患者产品编号与类型唯一性
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     *
     */
    public function checkNumOnly($project_num)
    {
        return $this->checkProjectOnlyOne($project_num, 'user');
    }

    /*
     * 检测验证码是否正确
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkVerify($verify)
    {
        if (! $verify || $verify != $_SESSION['verify_code']) {
            return false;
        }
        return true;
    }

    /*
     * 检测用户是否注册 --- 微信
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkWechatRegister($open_id)
    {
        $this->load->model('user_model');
        return $this->user_model->findOneByOpenId($open_id);
    }
    
    /*
     * 检测用户是否注册  --- web
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkToken($token)
    {
        $this->load->model('user_model');
        return $this->user_model->findOneToken($token);
    }
    
    /*
     * 检测产品患者（用户）编号唯一性
     * liting
     * 2016/06/24 16:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkProjectOnlyOne($project_num, $type)
    {
        $model = $type . '_project_model';
        $this->load->model($model);
        return $this->$model->findProjectOne($project_num);
    }

    /**
     * ******************** 会员部分 ***********************
     */

    /*
     * 产品数据获取
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（微信id）
     *
     */
    public function getUserProject()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : '0';
        $per_page = 10;
        $this->load->model('user_project_model');
        $result = $this->user_project_model->findAll($this->user_id, $page, $per_page);
        echo $this->result_lib->setInfoJson($result);
    }

    /*
     * 会员产品编号增加处理
     * liting
     * 2016/06/27 12:00:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     *
     */
    public function doUserProject()
    {
        $project_type = htmlspecialchars($this->input->post('project_type'));
        $project_num = htmlspecialchars($this->input->post('project_num'));
        $verify = htmlspecialchars($this->input->post('verify'));
        if(!$this->checkVerify($verify)){ echo $this->result_lib->setErrorsJson('验证码不正确');exit; }
        if(!$this->checkNum($project_num)){ echo $this->result_lib->setErrorsJson('产品编号输入不正确');exit; }
        if($this->checkNumOnly($project_num)){ echo $this->result_lib->setErrorsJson('此产品编号已添加');exit; }

        if($project_type == 'QS-M无针注射器'){
            $type_code ='M';
        }else if( $project_type == 'QS-P无针注射器'){
            $type_code ='P';
        } else{
            $type_code ='QBP';
        }

        // 检测患者产品编号与类型正确性
        $re = $this->checkProject($project_num);

        // TODO 判断值
        if($re->systemState&&($re->systemState==001)){
            $this->load->model('user_project_model');
            $zcode = $this->xml_to_array($re->reply,"/<zcode>/","</zcode>");
            //var_dump($zcode);
            if(!strstr($zcode,$type_code)){
                echo $this->result_lib->setErrorsJson('产品类型不正确');
               exit();
           }
            $status = $this->xml_to_array($re->reply,"/<cuanhuocode>/","</cuanhuocode>") ? $this->xml_to_array($re->reply,"/<cuanhuocode>/","</cuanhuocode>") : '';
            $re = $this->user_project_model->insert($project_type, $zcode, $this->user_id,$status);
            if ($re) {
                $this->load->model('user_model');
                $this->user_model->update($this->user_id, array(
                    'status' => '4',
                    'type' => '1'
                ));
                if($type_code == 'P'){
                    //领取保护盒
                    $data['ename'] = htmlspecialchars($this->input->post('ename'));
                    $data['ephone'] = htmlspecialchars($this->input->post('ephone'));
                    $data_add['eaddpro'] = htmlspecialchars($this->input->post('eaddpro'));
                    $data_add['eaddcity'] = htmlspecialchars($this->input->post('eaddcity'));
                    $data_add['eadd'] = htmlspecialchars($this->input->post('eaddress'));
                    $data['eaddress'] =  $data_add['eaddpro'] . $data_add['eaddcity'] . $data_add['eadd'];
                    $data['uid'] = $this->user_id;
                    $data['pid'] = $re;
                    $data['time']=time();
                    $data['exname']='';
                    $data['exnum']='';
                    $data['status']=1;
                    $data['updatetime']='';
                    $data['deltime']='';
                    $data['fang_status']=0;
                    if(!empty($data['ephone'])){
                        $this->db->insert('express',$data);
                    }
                }
                echo $this->result_lib->setInfoJson('添加成功');

                exit();
            }
            echo $this->result_lib->setErrorsJson('添加失败');
            exit();
        }
	log_error($re->reply);
        $reply = $this->xml_to_array($re->reply,"/<reply>/","</reply>");

        echo json_encode(array('result_code'=>401,'error_msg'=>$reply));
        die;
    }

    
    function xml_to_array( $xml,$first_code,$last_code ){
        $chars = preg_split($first_code, $xml, 0, PREG_SPLIT_OFFSET_CAPTURE);
        if(isset($chars[1][0])&&!empty($chars[1][0])){
            $code = explode($last_code,$chars[1][0]);
           if(!empty($code)){
                return   $code[0]; 
           }
        }
        return false;
    }
    /**
     * ******************** 产品验证部分 ***********************
     */
    /*
     * 产品数据获取
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkProject($project_num)
    {
        $client = new SoapClient('http://digitcode.yesno.com.cn/CCNOutService/OutDigitCodeService.asmx?wsdl');
        $client->soap_defencoding = 'UTF-8';
        // 参数转为数组形式传递
        $aryPara = array(
            'userID' => 'b6cf5d36063e44b2aa1a7d33599a1b3e',
            'userPwd' => '50e374505e31470dad4455951ea42066',
            'ip' => $this->input->ip_address(),
            'acCode' => $project_num,
            'language' => '1',
            'channel' => 'X'
        );
        // 调用远程函数
        $result =  $client->Get_AcCodeInfoInterface($aryPara);
        return $result;
    }
}










