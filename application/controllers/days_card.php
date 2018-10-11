<?php
require_once ('application/core/MY_Controller.php');

/**
 * 21天卡
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
class days_card extends MY_Controller_Site
{

    public $open_id = '';

    public $user_id = '';

    public $mole = '';

    public $userInfo = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('days_cards');
        $this->load->library('userinfo');
        $this->page_data['open_id'] = $this->open_id = $this->input->get('open_id');
        $this->load->model('user_model');
        
        if (! $this->userInfo = $this->checkRegister()) {
            redirect(base_url('user?open_id=' . $this->open_id)); // TODO:没有注册用户
        }
        
        //未购买产品用户
        if ( $this->userInfo['type'] != 1 || $this->userInfo['status'] != 4 ) {
            redirect(base_url('user/showFaild?status=请确认是否已购买产品&open_id=' . $this->open_id)); // TODO:没有注册用户
        }
        if(!$this->session->userdata('user_id')){
            $this->session->set_userdata(array(
                'user_id' => $this->userInfo['id']
            ));
            $this->session->set_userdata(array(
                'mole' => $this->userInfo['mole']
            ));
        }
        $this->user_id = $this->userInfo['id'];
        $this->mole = $this->userInfo['mole'];
    }

    public function index()
    {
        if( ! $this->checkUserInfo() ){
            redirect(base_url('user/showUserInsert?user_id='. $this->user_id .'&open_id=' . $this->open_id)); // TODO:没有注册用户
            exit();
        }
        if (! $this->getCardsListReturn()) {
            $this->showNoCards();
        } else {
            $this->loadView('bianji.html', $this->page_data);
        }
    }

    /*
     * 创建21天卡
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function showInsertCards()
    {
        $this->loadView('add_cards.html', $this->page_data);
    }

    /*
     * 无21天卡提示页面
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function showNoCards()
    {
        $this->loadView('add_cards_info.html', $this->page_data);
    }

    /*
     * 21天卡数据录入展示
     * liting
     * 2016/05/09 11:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function showInsertDataCards()
    {
        $this->page_data['id'] = $this->input->get('id');
        $this->loadView('add_data_cards.html', $this->page_data);
    }

    /*
     * 21天卡信息获取return
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function getCardsListReturn()
    {
        return $this->days_cards->getCardsList($this->user_id);
    }

    /*
     * 21天卡信息获取
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function getCardsList()
    {
        $result = $this->getCardsListReturn();
        if ($result) {
            $result = $this->result_lib->setInfo($result);
            echo json_encode($result);
            exit();
        }
        
        echo $this->result_lib->setErrorsJson('暂无21天信息');
        exit();
    }

    /*
     * 21天卡详情数据信息获取
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function getCardsDataList()
    {
        $result = $this->result_lib->setInfo($this->days_cards->getCardsDataList($this->user_id,$this->mole));
        echo json_encode($result);
    }

    /*
     * 创建21天卡
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function doInsertCards()
    {
        $this->db->trans_start();
        $insert_id = $this->days_cards->insertCards($this->user_id,$this->mole);
        if ($insert_id) {
            $start_time = strtotime($this->input->post('start_time'));
            $string = '';
            for( $i=0;$i<21;$i++ ){
                $start_date = date('Y-m-d',$start_time);
                $string .=  '('.'"'.$start_date.'",'.$this->user_id.','.$insert_id.'),';
                $start_time += 3600*24;
            }
            $this->load->model('days_detail_model');
            $re = $this->days_detail_model->insertCards(trim($string,','),$this->mole);
            if($re){
                $this->db->trans_commit();
                echo $this->result_lib->setInfoJson('创建成功');
                exit();
            }
        }
        $this->db->trans_rollback();
        echo $this->result_lib->setErrorsJson('创建失败');
        exit();
    }

    /*
     * 21天卡数据录入
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function doInsertDataCards()
    {
        $data = $this->getCardsListReturn();
        if($data){
            if(strtotime(date('Y-m-d')) > strtotime($data[count($data)-1]['end_time'])  || strtotime(date('Y-m-d')) < strtotime($data[count($data)-1]['start_time']) ){
                echo $this->result_lib->setErrorsJson('请选择正确的21天卡');
                exit();
            }
        }
        $re = $this->days_cards->insertDataCards($this->user_id,$this->mole);
        if ($re) {
            $maxData = $this->days_detail_model->findMax($this->user_id,$this->mole);
            if( date('Y-m-d') == $maxData['days_time'] ){
                if($maxData['sugar_morning'] !='' && $maxData['sugar_noon'] !='' && $maxData['sugar_night'] !='' && $maxData['pressure_morning'] !='' && $maxData['pressure_noon'] !='' && $maxData['pressure_night'] !=''  ){
                    echo $this->result_lib->setInfoJson('此二十一天卡已完成');
                    exit();
                }
            }
            echo $this->result_lib->setInfoJson('添加成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('添加失败');
        exit();
        // TODO:创建结果处理
    }

    /*
     * 查询21天卡是否完成
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkCardsStatus()
    {
        $result = $this->result_lib->setInfo($this->days_cards->checkCardsStatus());
        echo json_encode($result);
    }

    /*
     * 删除21天卡
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function delete()
    {
        if ($this->days_cards->delete($this->user_id,$this->input->get('id'),$this->mole)) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }

    /*
     * 检测用户注册
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
     * 检测用户详情
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkUserInfo()
    {
        $this->load->model('user_info_model');
        //检测信息是否完善
        return $this->user_info_model->findOneByUid($this->user_id);
    }
}










