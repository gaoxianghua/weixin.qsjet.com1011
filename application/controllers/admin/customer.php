<?php
require_once ('application/controllers/admin/common.php');

/**
 * 客户管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class customer extends common
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "customer";
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
     * 客户列表
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
        
        $this->load->model('admin/customer_model');
        $total = $this->customer_model->count($search);

        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/customer/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        //根据account id筛选
        if($this->session->userdata('type') == '3'){
            $admin_id = $this->session->userdata('admin_id');
            $query_account = $this->db->query("SELECT id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $account_id = $query_account['id'];
            $result = $this->customer_model->findAll_qc($search, $this->page_data['result']['page'], $this->page_data['result']['per_page'],$account_id);
            $this->page_data['count'] = count($result);
        }else if($this->session->userdata('type') == '1'){
            $result = $this->customer_model->findAll_admin($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
            $this->page_data['count'] = count($result);
        } else{
            $result = $this->customer_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
            $this->page_data['count'] = count($result);
        }
        $this->page_data['result'] = $this->createData($result);
        // search -- 经销商职称
        $this->page_data['dealer_name'] = $this->getDealerName();
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '客户列表';
        $this->page_data['table_form'] = 'customer_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'customer_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/customer/getList";
        $this->loadView('admin/index');
    }

    /*
     * 客户列表信息构建
     * liting
     * 2016/05/10 18:00:00
     *
     */
    public function createData($doctor_list)
    {
        if($this->session->userdata('type') == '3' || $this->session->userdata('type') == '1'){
            $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='7%'>姓名</th><th width='10%'> 手机号</th>
        <th>所属经销商</th><th>绑定编号</th><th >注册时间</th><th>优惠码使用情况</th><th>回访时间段</th><th>状态</th><th>操作</th></tr>";
        }else{
            $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='7%'>姓名</th><th width='10%'> 手机号</th>
        <th>所属业务员</th><th>绑定编号</th><th >注册时间</th><th>优惠码使用情况</th><th>回访时间段</th><th>状态</th><th>操作</th></tr>";
        }

        if (empty($doctor_list)) {

            $result .= "<tr><td colspan='13'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td align='center'><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td align='center'>" . $v['username'] . "</td>";
                $result .= "<td align='center'>" . $v['mobile'] . "</td>";
                if($this->session->userdata('type') == '3' || $this->session->userdata('type') == '1'){
                    $result .= "<td align='center'>" . $v['dealer_name'] . "</td>";
                }else{
                    $result .= "<td align='center'>" . $v['account_name'] . "</td>";
                }

                $result .= "<td align='center'>" . $v['doctor_name'] . "</td>";
                $result .= "<td align='center'>" . $v['add_time'] . "</td>";
                if(!empty($v['overtime']) && !empty($v['status_m'])){
                    if($v['overtime'] < time() && $v['status_m'] == '2'){
                        $result .= "<td align='center'>"  . "<font color='green'>" . '代金券已使用'. "</font>"
                            ."<br/>" .date('Y-m-d H:i:s',$v['extime_m']). "</td>";
                    }else if($v['overtime'] < time() && $v['status_m'] == '1'){
                        $result .= "<td align='center'>" .  "<font color='#969696'>" . '代金券已过期'. "</font>" . "</td>";
                    } else if ($v['overtime'] > time() && $v['status_m'] == '1'){
                        $result .= "<td align='center'>" . "<font color='red'>" . '代金券未使用'. "</font>" . "</td>";
                    }else if($v['overtime'] > time() && $v['status_m'] == '2'){
                        $result .= "<td align='center'>" . "<font color='green'>" . '代金券已使用'. "</font>"
                            ."<br/>" .date('Y-m-d H:i:s',$v['extime_m']). "</td>";
                  } else {
                        $result .= "<td>" .' ' . "</td>";
                    }
                }else{
                    $result .= "<td>" .' ' . "</td>";
                }
                $result .= "<td align='center'>" . $v['fangtime'] . "</td>";

                if($v['status_c'] == 3){
                    $result .= "<td align='center'>" . "已结算" . "</td>";
                }else{
                    $result .= "<td align='center'>" . "正常" . "</td>";
                }
                $result .= $this->createButton($v['status'],$v['id']);
            }
        }
            $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='customer_delete_all()'>批量删除</button>";
        return $result;
    }

    
    /*
     * 用户状态修改
     * liting
     * 2016/05/19 16:30:00
     *
     */
    public function createButton($status,$id)
    {
        $result = "<td ><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/customer/detail?id=" . $id . "'\" >查看</button> ";

        if( $status == '2' ){
            $result .= "
             | <button type='button' class='btn btn-danger' onclick='customerCheck($id,2)'>成交</button>";
        }
        if($this->session->userdata('type') == '3'){
            $result .= "</td></tr>";
        }else{
            $result .= " | <button type='button' class='btn btn-danger' onclick='customerDelete($id)'>删除</button></td></tr>";
        }

        return $result;
    }

    /*
     * 用户状态修改
     * liting
     * 2016/05/19 16:30:00
     *
     */
    public function doStatus()
    {
        $id = $this->input->get('id');
        $status = $this->input->get('status');
        $this->load->model('admin/customer_model');
        if ($this->customer_model->update(array(
            'id' => $id
        ), array(
            'status' => $status
        ))) {
            $result = $this->customer_model->findOneById($id);
            $this->load->model('doctor_count_model');
            if($status == 2){
                $this->doctor_count_model->saveRecom($result['doctor_id'],date('Y'),date('m'));
            }
            if($status == 4){
                $this->doctor_count_model->saveDeal($result['doctor_id'],date('Y'),date('m'));
            }
            echo $this->result_lib->setInfoJson('修改成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('修改失败');
        exit();
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
     * 状态值判断
     * liting
     * 2016/05/11 14:00:00
     *
     */
    public function setStatus($status)
    {
        switch ($status) {
            case '0':
                return '未审核';
                break;
            case '1':
                return '审核未通过';
                break;
            case 2:
                return '审核已通过';
                break;
            case 3:
                return '未成交';
                break;
            case 4:
                return '已成交';
                break;
        }
    }

    /*
     * 客户搜索
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['gender'] = $this->input->get('gender') ? trim(htmlspecialchars($this->input->get('gender'))) : '';
        //$this->page_data['data']['illness_type'] = $this->input->get('illness_type') ? trim(htmlspecialchars($this->input->get('illness_type'))) : '';
        $this->page_data['data']['username'] = $this->input->get('username') ? trim(htmlspecialchars($this->input->get('username'))) : '';
        $this->page_data['data']['status'] = $this->input->get('status') != null ? trim(htmlspecialchars($this->input->get('status'))) : '';
        $this->page_data['data']['dealer_id'] = '';
        $this->page_data['data']['status_c'] = 1;
        if ($this->session->userdata('type') == '0') {
            $dealerInfo = $this->getDealer(); // 经销商信息
            $this->page_data['data']['dealer_id'] = $dealerInfo['id'];
        }
        return $this->page_data['data'];
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
     * 客户详情
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function detail()
    {
        $id = $this->input->get('id');
        $this->load->model('admin/customer_model');
        $this->page_data['result'] = $this->customer_model->findOneById($id);
        $this->page_data['table_title_name'] = '客户详情';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'customer_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'customer_second_menu.php';
        
        $this->loadView('admin/index');
    }
    
    /*
     * 客户导出
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function downloadCustomer()
    {
        $search = $this->getSearch();
        $this->load->model('admin/customer_model');
        $result = $this->customer_model->findAll($search, 0, 0);
        //print_R($result);
        if (empty($result)) {
            echo "<script>alert('暂无数据');history.go(-1);</script>";
            exit();
        }
        $this->load->library('phpexecl_upload');
        $re = $this->phpexecl_upload->customerDowns($result);
    
    }
    
    /*
     * 客户信息删除
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/customer_model');
        if($this->customer_model->delete($id)){
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }
    
}
