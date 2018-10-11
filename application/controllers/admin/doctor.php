<?php
require_once ('application/controllers/admin/common.php');

/**
 * 医生管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class doctor extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "doctor";
        $this->page_data['second_menu_flag'] = "spaceuser";
        $this->load->library('result_lib');
        
        if (! $this->checkPermissions()) {
            redirect(base_url('admin/index/per_errors'));
        }
    }

    public function checkPermissions()
    {
        $this->load->model('auth_permission_model');
        $permission_me = $this->auth_permission_model->getPermissionByName($this->page_data['menu_flag']);
        if (in_array($permission_me['id'], $this->session->userdata('permissions'))) {
            return true;
        }
        return false;
    }

    /*
     * 绑定列表
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        
        $this->load->model('admin/doctor_model');
        $total = $this->doctor_model->count($search);
        
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/doctor/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        //根据qc_code -> account_id 筛选
        if($this->session->userdata('type') == '3'){
            $admin_id = $this->session->userdata('admin_id');
            $query_account = $this->db->query("SELECT id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $account_id = $query_account['id'];
            $result = $this->doctor_model->findAll_qc($search, $this->page_data['result']['page'], $this->page_data['result']['per_page'],$account_id);
            $this->page_data['count'] = count($result);
        }else if($this->session->userdata('type') == '1'){
            $result = $this->doctor_model->findAll_admin($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
            $this->page_data['count'] = count($result);
        }else{
            $result = $this->doctor_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
            $this->page_data['count'] = count($result);
        }

        $this->page_data['result'] = $this->createData($result);
        // search -- 经销商职称
        if($this->session->userdata('type') == '1'){
            $this->page_data['dealer_name'] = $this->getDealerName();
        }else if($this->session->userdata('type') == '0'){
            //经销商查找业务员
            $this->page_data['dealer_name'] = $this->getAccountName();
        }else{
            //业务员下显示经销商
            $admin_id = $this->session->userdata('admin_id');
            $query_dealer = $this->db->query("SELECT dealer_id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $dealer_id = $query_dealer['dealer_id'];
            $dealer_info =  $this->db->query("SELECT dealer_name FROM dealer WHERE id = $dealer_id")->row_array();
            $dealer_name = $dealer_info['dealer_name'];
            $this->page_data['dealer_name'] =[0=>['id'=>$dealer_id,'dealer_name'=>$dealer_name]];
        }

        // search -- 医师职称
        $this->page_data['position'] = $this->getDoctorPosition();
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '绑定编号列表';
        $this->page_data['table_form'] = 'doctor_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'doctor_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/doctor/getList";
        $this->loadView('admin/index');
    }

    public function getAccountName()
    {
        $dealer_id = $this->session->userdata('dealer_id');
        $query = $this->db->query("SELECT id,account_name as dealer_name FROM ex_account WHERE dealer_id = $dealer_id")->result_array();
        return $query;
    }

    /*
     * 来源列表信息构建
     * liting
     * 2016/05/10 18:00:00
     *
     */
    public function createData($doctor_list)
    {
        if($this->session->userdata('type') == '3' || $this->session->userdata('type') == '1'){
            $result = "<table class='table table-bordered table-striped'><tr><th width='10%'>绑定编号</th><th width='13%'>所属经销商</th><th width='28%'>二维码ID
    </th><th width='6%'>总推荐数</th><th width='6%'>代金券使用数量</th><th width='6%'>已结算数量</th><th width='100px;'>绑定时间</th><th width='14%'>操作</th></tr>";
        }else {
            $result = "<table class='table table-bordered table-striped'><tr><th width='10%'>绑定编号</th><th width='13%'>业务员</th><th width='28%'>二维码ID
    </th><th width='6%'>总推荐数</th><th width='6%'>代金券使用数量</th><th width='6%'>已结算数量</th><th width='100px;'>绑定时间</th><th width='14%'>操作</th></tr>";
        }

        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td>" . $v['doctor_name'] . "</td>";
                if($this->session->userdata('type') == '3' || $this->session->userdata('type') == '1'){
                    $result .= "<td>" . $v['dealer_name'] . "</td>";
                }else{
                    $result .= "<td>" . $v['account_name'] . "</td>";
                }

                $result .= "<td>" . $v['qc_code'] . "</td>";
                $result .= "<td>" . $v['recommend'] . "</td>";
                $result .= "<td>" . $v['deal_m'] . "</td>";
                $result .= "<td>" . $v['js_count'] . "</td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/doctor/detail?id=" . $v['id'] . "'\" >查看</button> | ";
                $result .= "<button type='button' class='btn btn-danger' onclick=unbundling(".$v['doctor_id'] .",'".$v['qc_code']."')>解绑</button> |";
                if($v['recommend_js']>0){
                    $result .= "<button type='button' class='btn btn-danger-none' style='background-color: orange' onclick=settle(".$v['doctor_id'].")><font color='#fff'>结算</font></button></td></tr>";
                }else{
                    $result .= "<button type='button' class='btn btn-danger-none' style='background-color: #c0c0c0' onclick=''><font color='#fff'>结算</font></button></td></tr>";
                }
            }
        }
        
        $result .= "</table>";
        return $result;
    }
    /*
     * 结算操作
     * 2018-07-04
     * Gao
     */
    public function settle()
    {
       /* $doctor_id = $this->input->get('doctor_id');
        $data = array('status_c' => 3);
        $this->db->where('doctor_id', $doctor_id);
        $re = $this->db->update('customer', $data);
        $data = array('status' => 3);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->update('coupon', $data);*/
        $doctor_id = $this->input->get('doctor_id');
        $t = time();
        $re = $this->db->query("
                                UPDATE customer  LEFT JOIN coupon ON customer.doctor_id = coupon.doctor_id AND customer.open_id = coupon.open_id SET customer.status_c = 3
                                WHERE customer.doctor_id = $doctor_id AND coupon.status_m = 2 OR coupon.overtime<$t");
        $this->db->query("
                                UPDATE coupon  LEFT JOIN customer ON customer.doctor_id = coupon.doctor_id AND customer.open_id = coupon.open_id SET coupon.status = 3
                                WHERE coupon.doctor_id = $doctor_id AND coupon.status_m = 2 OR coupon.overtime<$t");
        if($re){
            echo $this->result_lib->setInfoJson('结算成功');
        } else {
            echo $this->result_lib->setInfoJson('结算失败');
        }
    }

    /*
     * 经销商名称获取
     * liting
     * 2016/05/10 18:00:00
     *
     */
    public function getDealerName()
    {
        $this->load->model('admin/dealer_model');
        return $this->dealer_model->findAllByName();
    }

    /*
     * 医师职称名称获取
     * liting
     * 2016/05/11 10:00:00
     *
     */
    public function getDoctorPosition()
    {
        $this->load->model('admin/doctor_type_model');
        return $this->doctor_type_model->findAll();
    }

    /*
     * 医生列表
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getSearch()
    {
        if($this->session->userdata('type') == '0'){
            $this->page_data['data']['account_id'] = $this->input->get('dealer_id') ? trim(htmlspecialchars($this->input->get('dealer_id'))) : '';
         }else{
            $this->page_data['data']['dealer_id'] = $this->input->get('dealer_id') ? trim(htmlspecialchars($this->input->get('dealer_id'))) : '';
        }

        $this->page_data['data']['position'] = $this->input->get('position') ? trim(htmlspecialchars($this->input->get('position'))) : '';
        $this->page_data['data']['add_time'] = $this->input->get('add_time') ? trim(htmlspecialchars($this->input->get('add_time'))) : '';
        $this->page_data['data']['doctor_name'] = $this->input->get('doctor_name') ? htmlspecialchars($this->input->get('doctor_name')) : '';
        $this->page_data['data']['start_time'] = $this->input->get('start_time') ? htmlspecialchars($this->input->get('start_time')) : '';
        $this->page_data['data']['end_time'] = $this->input->get('end_time') ? htmlspecialchars($this->input->get('end_time')) : '';
        if ($this->session->userdata('type') == '0') {
            $dealerInfo = $this->getDealer(); // 经销商信息
            $this->page_data['data']['dealer_id'] = $dealerInfo['id'];
        }
        return $this->page_data['data'];
    }

    /*
     * 医生详情
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function detail()
    {
        $id = $this->input->get('id');
        $this->load->model('admin/doctor_model');
        $this->page_data['result'] = $this->doctor_model->findOneById($id);
        $this->page_data['table_title_name'] = '绑定编号详情';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'doctor_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'doctor_second_menu.php';
        
        $this->loadView('admin/index');
    }

    /*
     * 解绑
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function unbundling()
    {
        $doctor_id = $this->input->get('doctor_id');
        $qc_code = $this->input->get('qc_code');
        $this->load->model('admin/qc_code_model');
        $dealer_id = $this->session->userdata('dealer_id')?$this->session->userdata('dealer_id'):'';
        $this->load->model('admin/doctor_model');
        if ($this->doctor_model->unbundling($doctor_id,$qc_code,$dealer_id)) {
            echo $this->result_lib->setInfoJson('解绑成功');
            exit();
        }
        echo $this->result_lib->setInfoJson('解绑失败');
        exit();
    }

    /*
     * 经销商登录信息查询
     * liting
     * 2016/05/10 15:30:00
     *
     */
    public function getDealer()
    {
        $this->load->library('admin/admin_dealer');
        return $this->admin_dealer->getDealerInfo(array(
            'admin_id' => $this->session->userdata('admin_id')
        ));
    }

    /*
     * 获取经销商所拥有二维码
     * liting
     * 2016/05/10 15:30:00
     *
     */
    public function getQc($dealerInfo)
    {
        $this->load->library('admin/admin_qc_code');
        return $this->admin_qc_code->getDealerQc($dealerInfo['id']);
    }
}
