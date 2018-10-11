<?php
require_once ('application/controllers/admin/common.php');

/**
 * 二维码
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class qc_code extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "qc_code";
        $this->page_data['second_menu_flag'] = "spaceuser";
        
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
        $this->page_data['qcode_url'] = $this->qcode_url = $this->config->item('qcode_url');
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
     * 二维码列表
     * liting
     * 2016/05/12 15:40:00
     *
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        
        $this->load->model('admin/qc_code_model');
        $total = $this->qc_code_model->count($search);
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/qc_code/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        if($this->session->userdata('type') == '3'){
            $admin_id = $this->session->userdata('admin_id');
            $query_account = $this->db->query("SELECT id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $account_id = $query_account['id'];
            $result = $this->qc_code_model->findAll_qc($search, $this->page_data['result']['page'], $this->page_data['result']['per_page'],$account_id);
            $this->page_data['count'] = count($result);
            }else{
            $result = $this->qc_code_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        }

        $this->page_data['result'] = $this->createData($result);
        // 经销商信息
        if($this->session->userdata('type') == '1'){
            $this->page_data['dealer_name'] = $this->getDealerAll();
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
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '二维码列表';
        $this->page_data['table_form'] = 'qc_code_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'qc_code_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/qc_code/getList";
        $this->loadView('admin/index');
    }
    public function getAccountName()
    {
        $dealer_id = $this->session->userdata('dealer_id');
        $query = $this->db->query("SELECT id,account_name as dealer_name FROM ex_account WHERE dealer_id = $dealer_id")->result_array();
        return $query;
    }

    /*
     * 所有经销商信息获取
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getDealerAll()
    {
        $this->load->model('admin/dealer_model');
        return $this->dealer_model->findAllByName();
    }

    /*
     * 所有经销商信息获取JSON
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getDealerAllJson()
    {
        $result = $this->result_lib->setInfo($this->getDealerAll());
        echo json_encode($result);
    }

    /*
     * 二维码搜索
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['status'] = $this->input->get('status') ? trim(htmlspecialchars($this->input->get('status'))) : '';
        $this->page_data['data']['dealer_id'] = $this->input->get('dealer_id') ? trim(htmlspecialchars($this->input->get('dealer_id'))) : '';
        $this->page_data['data']['start_time'] = $this->input->get('start_time') ? trim(htmlspecialchars($this->input->get('start_time'))) : '';
        $this->page_data['data']['end_time'] = $this->input->get('end_time') ? trim(htmlspecialchars($this->input->get('end_time'))) : '';
        if ($this->session->userdata('type') == '0') {
            $this->page_data['data']['dealer_id'] = $this->session->userdata('dealer_id');
        }
        return $this->page_data['data'];
    }

    /*
     * 二维码列表信息构建
     * liting
     * 2016/05/13 11:00:00
     *
     */
    public function createData($doctor_list)
    {
        if($this->session->userdata('type') == '3'){
            $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th>
            <th width='28%'>二维码ID</th><th width='10%'>所属经销商</th><th width='10%'>绑定编号</th><th width='240px'>生成时间</th><th width='10%'>状态
    </th><th width='13%'>操作</th><th>预览</th></tr>";
        }else{
            $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th>
            <th width='28%'>二维码ID</th><th width='10%'>所属业务员</th><th width='10%'>绑定编号</th><th width='240px'>生成时间</th><th width='10%'>状态
    </th><th width='13%'>操作</th><th>预览</th></tr>";
        }

        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                if($this->session->userdata('type') == '3'){
                    $dealer_name = ($v['doctor_name']!=''&$v['dealer_name']=='')?'无':$v['dealer_name'];
                }else{
                    $dealer_name = ($v['doctor_name']!=''&$v['dealer_name']=='')?'无':$v['account_name'];
                }

                $result .= "<td>" . $v['qc_code_name'] . "</td>";
                $result .= "<td>" .$dealer_name  . "</td>";
                $result .= "<td>" . $v['doctor_name'] . "</td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td> " . $this->handlStatus($v['status']) . "</td>";
                $result .= "<td>" . $this->handlButton($v);
                $result .= " </td> ";
                $result .= "<td><input class='btn btn-success' value='预览' type='button' onclick=printCode('".$this->qcode_url.'qc_code/'.$v['qc_code_name']."')></td> </tr>";
            }
        }
        
        //$result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='qc_code_delete_all()'>批量删除</button>";
        $result .= "</table>";
        return $result;
    }

    /*
     * 操作按钮判断
     * liting
     * 2016/05/13 11:00:00
     *
     */
    public function handlButton($v)
    {
        // 如果为经销商登录
        if ($this->session->userdata('type') == '3') {
            if ($v['doctor_id'] == '') {
                return "<button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/qc_code/binding?id=" . $v['id'] . "'\" >绑定</button> ";
            } else {
                return "<button type='button' class='btn btn-danger' onclick=unbundling(".$v['doctor_id'] .",'".$v['qc_code_name']."') >解绑</button>";
            }
        }
        if ($this->session->userdata('dealer_id')) {
            if ($v['doctor_id'] == '') {
                return "<button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/qc_code/binding?id=" . $v['id'] . "'\" >绑定</button> ";
            } else {
                return "<button type='button' class='btn btn-danger' onclick=unbundling(".$v['doctor_id'] .",'".$v['qc_code_name']."') >解绑</button>";
            }
        } elseif ($v['dealer_id'] == 11){
            if ($v['dealer_id'] == 11 && $v['status'] == 2) {
                return "<button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/qc_code/binding?id=" . $v['id'] . "'\" >绑定</button> ";
            } else {
                return "<button type='button' class='btn btn-danger' onclick=unbundling(".$v['doctor_id'] .",'".$v['qc_code_name']."') >解绑</button>";
            }
        }
        else {
            //if ($v['doctor_id'] == '' && $v['dealer_id'] == '') {
            if ($v['doctor_id'] == '' && $v['dealer_id'] == '') {
                return "<button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/qc_code/binding?id=" . $v['id'] . "'\" >绑定</button> | " . "<button type='button' class='btn btn-success' onclick='assign(" . $v['id'] . ")'>指派</button>";
            } else {
                //return "<button type='button' class='btn btn-danger' onclick='cancel(" . $v['id'] . ")'>注销</button>";
                return " ";
            }
        }
    }

    /*
     * 状态值判断
     * liting
     * 2016/05/13 11:00:00
     *
     */
    public function handlStatus($status)
    {
        switch ($status) {
            case 1:
                return '未指派';
                break;
            case 2:
                return '未绑定';
                break;
            case 3:
                return '验证中';
                break;
            case 4:
                return '已绑定';
                break;
        }
    }

    /*
     * 二维码绑定
     * liting
     * 2016/05/13 12:30:00
     *
     */
    public function binding()
    {
        $id = $this->input->get('id'); // 二维码id
        // 查询二维码信息
        $this->load->library('admin/admin_qc_code');
        $re = $this->admin_qc_code->checkCode($id);
        if ($re['result_code'] == 400) {
            echo "<script>alert('" . $re['error_msg'] . "');history.go(-1);</script>";
            exit();
        }
        //经销商与来源处理
        if(empty($re['info']['dealer_id'])){
            echo "<script>  alert('请先指派经销商！'); window.location.href='{$_SERVER['HTTP_REFERER']}'</script>";exit;
        }
        if($re['info']['dealer_id']){
            $dealer_id = $re['info']['dealer_id'];
            //var_dump($dealer_id);
            $query = $this->db->query("SELECT doctor_info.doctor_name,doctor_info.position FROM doctor LEFT JOIN doctor_info ON doctor.id = doctor_info.doctor_id WHERE doctor.dealer_id = $dealer_id");
            $row = $query->result_array();
            if(count($row)>0){
                $max = count($row) - 1;
                $maxid = $row[$max]['position'];
                $newnum = (int)$maxid + 1;
                $re['info']['position'] = $newnum;
                //几位整数
                $p = strlen($newnum);
                if($p == 1){
                    $bian = '000'. $newnum;
                } else if($p == 2){
                    $bian = '00'. $newnum;
                } else if($p == 3){
                    $bian = '0'. $newnum;
                } else if($p == 4){
                    $bian =  $newnum;
                }
                $re['info']['doctor_name'] = $bian;
            } else{
                $re['info']['position'] = 1;
                $re['info']['doctor_name'] = '0001';
            }
        }
        $this->page_data['result'] = $re['info'];

        // 查询职称
        $this->load->model('admin/doctor_type_model');
        $this->page_data['doctor_type'] = $this->doctor_type_model->findAll($id);
        $this->page_data['commit_url'] = $id ? 'admin/qc_code/doBinding?id=' . $id : '';
        $this->page_data['table_title_name'] = '二维码信息绑定';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'qc_code_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'qc_code_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/qc_code/binding";
        $this->loadView('admin/index');
    }

    /*
     * 医生绑定执行
     * liting
     * 2016/05/16 10:30:00
     *
     */
    public function doBinding()
    {
        $id = $this->input->get('id'); // 二维码id
        $data = $this->input->post();
        $this->load->library('admin/admin_qc_code');
        $re = $this->admin_qc_code->checkCode($id);
        if ($re['result_code'] == 400) {
            echo "<script>alert('" . $re['error_msg'] . "');history.go(-1);</script>";
            exit();
        }
        // 如果为经销商登录
        $data['dealer_id'] = '';
        if ($this->session->userdata('type') == '0') {
            $data['dealer_id'] = $this->session->userdata('dealer_id');
        }else if($this->session->userdata('type') == '3'){
           $admin_id = $this->session->userdata('admin_id');
           $query_account = $this->db->query("SELECT dealer_id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $data['dealer_id'] = $query_account['dealer_id'];
        }else{
            // root添加
            $data['dealer_id']=11;
        }
        //var_dump($data);die;
        $data['department'] = '';
        $data['hospital'] = '';
        if ($this->admin_qc_code->binding($id, $data)) {
            echo "<script>alert('添加成功');location.href='" . base_url('admin/qc_code/getList') . "';</script>";
            exit();
        }
        echo "<script>alert('绑定失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 二维码信息注销操作
     * liting
     * 2016/05/16 12:20:00
     *
     */
    public function cancel()
    {
        $this->load->library('admin/admin_qc_code');
        echo $this->admin_qc_code->cencel($this->input->get('id'));
    }

    /*
     * 二维码信息解绑
     * liting
     * 2016/05/16 12:20:00
     *
     */
    public function unbinding()
    {
        $this->input->get('id');
        $this->load->library('admin/admin_qc_code');
        echo $this->admin_qc_code->unbinding($this->input->get('id'));
    }

    /*
     * 二维码信息指派
     * liting
     * 2016/05/16 12:20:00
     *
     */
    public function doAssign()
    {
        $dealer_id = $this->input->get('dealer_id');
        $qc_code_id = $this->input->get('qc_code_id');
        $this->load->library('admin/admin_qc_code');
        echo $this->admin_qc_code->assign($dealer_id, $qc_code_id);
    }

    /*
     * 二维码信息生成
     * liting
     * 2016/05/16 12:20:00
     *
     */
    public function generateCode()
    {
        //root添加二维码
        if ($this->session->userdata('type') == '1'){
            $num = $this->input->get('code_num');
            $this->load->library('generate_code');
            if ($num > 0 && $num < 51) {
                for ($i = 0; $i < $num; $i ++) {
                    $name[] = $this->generate_code->generate();
                }
            }
            $this->load->model('admin/qc_code_model');
            $re = $this->qc_code_model->insert($name);
            if ($re) {
                echo $this->result_lib->setInfoJson('成功生成二维码' . $re . '个');
                exit();
            }
            echo $this->result_lib->setErrorsJson('二维码生成失败');
            exit();
        }
        //子账号添加二维码
        if ($this->session->userdata('type') == '3'){
            $num = $this->input->get('code_num');
            $this->load->library('generate_code');

            if ($num > 0 && $num < 51) {
                for ($i = 0; $i < $num; $i ++) {
                    $name['qc_code_name'] = $this->generate_code->generate();
                }
            }
            //admin_id
            $admin_id = $this->session->userdata('admin_id');
            $query_account = $this->db->query("SELECT id FROM ex_account WHERE admin_id = $admin_id")->row_array();
            $account_id = $query_account['id'];
            $this->load->model('admin/qc_code_model');
            //dealer_id
            $dealer_id = $_SESSION['dealer_id'];
            //经销商二维码添加
            $re = $this->qc_code_model->insertDealer($name,$account_id,$dealer_id);
            if ($re) {
                echo $this->result_lib->setInfoJson('成功生成二维码' . $re . '个');
                exit();
            }
            echo $this->result_lib->setErrorsJson('二维码生成失败');
            exit();
        }

    }
    
    /*
     * 二维码打印
     * liting
     * 2016/05/16 12:20:00
     *
     */
    public function printCode()
    {
        
        $codeId = $this->input->post('codeId');
        if(!$codeId){
            redirect(base_url('admin/qc_code/getList'));exit;
        }
        $codeId = trim(trim($codeId),',');
        $this->load->model('admin/qc_code_model');
        $result = $this->qc_code_model->findAllById($codeId);
        
        $this->page_data['result'] = $result;
        $this->page_data['total'] = count($result);
        $this->page_data['table_title_name'] = '二维码打印信息';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'qc_code_print.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'qc_code_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/qc_code/binding";
        
        $this->loadView('admin/index');
    }
    /*
    * 二维码删除
    * liting
    * 2016/05/11 11:00:00
    *
    */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/qc_code_model');
        if($this->qc_code_model->deletecode($id)){
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }
}

















