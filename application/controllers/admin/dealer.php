<?php
require_once ('application/controllers/admin/common.php');

/**
 * 经销商管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class dealer extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "dealer";
        $this->page_data['second_menu_flag'] = "spaceuser";
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
     * 经销商列表
     * liting
     * 2016/05/11 14:30:00
     *
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        
        $this->load->model('admin/dealer_model');
        $total = $this->dealer_model->count($search);

        $this->page_data['count'] = isset($total['total']) ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/dealer/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->dealer_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);

        $this->page_data['result'] = $this->createData($result);
        // 经销商大区信息
        $this->load->model('admin/large_area_model');
        $this->page_data['large_area'] = $this->large_area_model->findAll();
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '经销商列表';
        $this->page_data['table_form'] = 'dealer_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'dealer_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/dealer/getList";
        $this->loadView('admin/index');
    }

    /*
     * 经销商列表信息构建
     * liting
     * 2016/05/11 15:00:00
     *
     */
    public function createData($doctor_list)
    {
        $result = "<table class='table table-bordered table-striped'><tr><th width='10%'>经销商名称</th><th width='5%'>二维码数</th><th width='5%'>绑定数</th><th width='5%'>客户数
    </th><th width='12%'>代理区域</th><th width='10%'>销售经理</th><th width='12%'>销售经理电话</th><th>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='10'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<td>" . $v['dealer_name'] . "</td>";
                $result .= "<td>" . $v['qc_total'] . "</td>";
                $result .= "<td>" . $v['doctor_total'] . "</td>";
                $result .= "<td>" . $v['user_total'] . "</td>";
                $result .= "<td>" . $v['agent_area'] . "</td>";
                $result .= "<td>" . $v['project_person'] . "</td>";
                $result .= "<td>" . $v['project_mobile'] . "</td>";
                $result .= "<td width=25%><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/dealer/detail?id=" . $v['id'] . "'\" >查看</button> | ";
                $result .= "<button type='button' class='btn btn-success' onclick='dealerEdit(" . $v['id'] . ")'>编辑</button> | ";
                $admin_id = $v['id']."_".$v['admin_id'];
                if($v['admin_id'] == '1'){
                    $result .= "<button type='button'style='background-color: #ccc;' class='btn btn-danger-none' onclick=\"\">删除</button></td></tr>";
                }else{
                    $result .= "<button type='button' class='btn btn-danger' onclick=\"dealerDelete('".$v['id']."_".$v['admin_id']."')\">删除</button></td></tr>";
                }
            }
        }

        return $result;
    }

    /*
     * 经销商搜索
     * liting
     * 2016/05/11 15:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['area_id'] = $this->input->get('area_id') ? trim(htmlspecialchars($this->input->get('area_id'))) : '';
        $this->page_data['data']['contract'] = $this->input->get('contract') ? trim(htmlspecialchars($this->input->get('contract'))) : '';
        $this->page_data['data']['dealer_name'] = $this->input->get('dealer_name') ? trim(htmlspecialchars($this->input->get('dealer_name'))) : '';
        return $this->page_data['data'];
    }

    /*
     * 经销商详情
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function detail()
    {
        $id = $this->input->get('id');
        $this->load->model('admin/dealer_model');
        $this->page_data['result'] = $this->dealer_model->findOneById($id);
        $this->page_data['table_title_name'] = '经销商详情';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'dealer_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'dealer_second_menu.php';
        
        $this->loadView('admin/index');
    }

    /*
     * 经销商新建
     * liting
     * 2016/05/11 17:00:00
     *
     */
    public function add()
    {
        // 经销商大区信息
        $this->load->model('admin/large_area_model');
        $this->page_data['large_area'] = $this->large_area_model->findAll();
        
        $this->page_data['commit_url'] = 'admin/dealer/doAdd';
        $this->page_data['table_title_name'] = '新建经销商';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'dealer_do_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'dealer_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/dealer/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 经销商新建处理
     * liting
     * 2016/05/11 17:00:00
     *
     */
    public function doAdd()
    {
        $re_permisssions = '';
        
        $data = $this->input->post();
        $data['area_id'] = '3';
        $data['dealer_person'] = '';
        $data['dealer_fax'] = '';
        $data['term'] = '';
        $data['contract'] = '';
        $this->db->trans_start();
        // 管理员表中数据增加
        $this->load->library('admin/admin_user');
        $re_admin_user = $this->admin_user->insertDealer($data);

        if ($re_admin_user['result_code'] == 200) {
            $data['admin_id'] = isset($re_admin_user['info']) ? $re_admin_user['info'] : '';
            // 经销商权限增加
            $this->load->library('admin/admin_user');
            $re_permisssions = $this->admin_user->insertDealerPermisssions($data['admin_id']);
            //var_dump($re_permisssions);die;
            // 经销商表中数据增加
            $this->load->library('admin/admin_dealer');
            $data['add_time'] = date('Y-m-d H:i:s');
            $re_dealer = $this->admin_dealer->insertDealer($data);
            
            if ($re_dealer['result_code'] == 200 && $re_permisssions) {
                $this->db->trans_commit();
                echo "<script>alert('添加成功');location.href='" . base_url('admin/dealer/getList') . "';</script>";
                exit();
            }
        }
        
        $this->db->trans_rollback();
        echo "<script>alert('" . $re_admin_user['error_msg'] . "');history.go(-1);</script>";
        exit();
    }

    /*
     * 经销商编辑
     * liting
     * 2016/05/11 18:00:00
     *
     */
    public function dealerEdit()
    {
        $this->page_data['id'] = $this->input->get('id');
        // 经销商大区信息
        $this->load->model('admin/large_area_model');
        $this->page_data['large_area'] = $this->large_area_model->findAll();
        
        $this->load->model('admin/dealer_model');
        $this->page_data['result'] = $this->dealer_model->findOneById($this->page_data['id']);
        
        $this->page_data['commit_url'] = 'admin/dealer/doDealerEdit?id=' . $this->page_data['id'] . '&a_id=' . $this->page_data['result']['admin_id'];
        $this->page_data['table_title_name'] = '编辑经销商';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'dealer_do_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'dealer_second_menu.php';
        
        $this->loadView('admin/index');
    }

    /*
     * 经销商编辑执行
     * liting
     * 2016/05/11 18:00:00
     *
     */
    public function doDealerEdit()
    {
        $this->page_data['id'] = $this->input->get('id');
        $this->page_data['admin_id'] = $this->input->get('a_id');
        
        $data = $this->input->post();
        $this->db->trans_start();
        
        // 管理员表中数据修改
        $this->load->library('admin/admin_user');
        $re = $this->admin_user->updateDealer($this->page_data['admin_id'], $data);
        
        if($re['result_code'] == '200'){
            // 经销商表中数据修改
            $this->load->library('admin/admin_dealer');
            
            $re = $this->admin_dealer->updateDealer($this->page_data['id'], $data);
            if($re['result_code'] == '200'){
                $this->db->trans_commit();
                echo "<script>alert('修改成功');location.href='" . base_url('admin/dealer/getList') . "';</script>";
                exit();
            }
        }
        $this->db->trans_rollback();
        echo "<script>alert('".$re['error_msg']."');history.go(-1);</script>";
        exit();
    }

    /*
     * 删除
     * liting
     * 2016/05/11 18:00:00
     *
     */
    public function delete()
    {
        
        $this->page_data['id'] = $this->input->get('id');
        $id = trim($this->page_data['id'],',');
        $idArr = explode(',',$id);
        $dealer_id = '';
        $admin_id = '';
        foreach($idArr as $k=>$v){
            $v = explode('_',$v);
            $dealer_id .= $v[0].',';
            $admin_id .= $v[1].',';
        }
        
        $this->db->trans_start();
        
        // 管理员表中数据删除
        $this->load->model('admin/admin_user_model');
        $re_admin_user = $this->admin_user_model->delete(trim($admin_id,','));
        
        // 经销商表中数据删除
        $this->load->model('admin/dealer_model');
        $re_dealer = $this->dealer_model->delete(trim($dealer_id,','));
        
        if ($re_admin_user && $re_dealer) {
            $this->db->trans_commit();
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        $this->db->trans_rollback();
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }

    /*
     * 大区管理
     * liting
     * 2016/05/11 18:00:00
     *
     */
    public function areaList()
    {
        // 搜索信息
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        $this->page_data['large_area'] = $this->input->get('large_area')?trim(htmlspecialchars($this->input->get('large_area'))):'';;
        
        $this->load->model('admin/large_area_model');
        $total = $this->large_area_model->count($this->page_data['large_area']);
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/dealer/areaList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $doctor_list = $this->large_area_model->findAllLimit($this->page_data['large_area'], $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        
        $this->page_data['result'] = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th>大区名称</th><th>大区负责人</th><th style='width:150px'>操作</th></tr>";
        if (empty($doctor_list)) {
            $this->page_data['result'] .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $this->page_data['result'] .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $this->page_data['result'] .= "<td>" . $v['area_name'] . "</td>";
                $this->page_data['result'] .= "<td>" . $v['area_person'] . "</td>";
                $this->page_data['result'] .= "<td width=20%><button type='button' class='btn btn-success' onclick='areaSave(" . $v['id'] . ",this)'>编辑</button> | ";
                $this->page_data['result'] .= "<button type='button' class='btn btn-danger' onclick=\"areaDelete(" . $v['id'] . ")\" |>删除</button></td></tr>";
            }
        }
        
        $this->page_data['result'] .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='area_delete_all()'>批量删除</button>";
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '大区管理';
        $this->page_data['table_form'] = 'dealer_area_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'dealer_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/dealer/areaList";
        $this->loadView('admin/index');
    }

    /*
     * 大区删除
     * liting
     * 2016/05/11 19:00:00
     *
     */
    public function areaDelete()
    {
        $this->page_data['id'] = trim($this->input->get('id'),',');
        $this->load->model('admin/large_area_model');
        if ($this->large_area_model->delete($this->page_data['id'])) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrors('删除成功');
        exit();
    }

    /*
     * 大区新建
     * liting
     * 2016/05/17 10:30:00
     *
     */
    public function doAddArea()
    {
        $this->load->model('admin/large_area_model');
        $data['area_name'] = $this->input->get('area_name')?trim(htmlspecialchars($this->input->get('area_name'))):'';
        $data['area_person'] = $this->input->get('area_person')?trim(htmlspecialchars($this->input->get('area_person'))):'';
        if(!$this->checkLargeName($data['area_name'])){
            echo $this->result_lib->setErrorsJson('大区名称已存在');
            exit();
        }
        if ($this->large_area_model->insert($data)) {
            echo $this->result_lib->setInfoJson('添加成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('添加失败');
        exit();
    }
    
    /*
     * 大区详情获取
     * liting
     * 2016/05/24 10:30:00
     *
     */
    public function getAreaOne()
    {
        $area_id = $this->input->get('area_id');
        $this->load->model('admin/large_area_model');
        if ($result = $this->large_area_model->findOneById($area_id)) {
            echo $this->result_lib->setInfoJson($result);
            exit();
        }
        echo $this->result_lib->setErrorsJson('获取失败');
        exit();
    }
    
    /*
     * 大区修改
     * liting
     * 2016/05/24 10:30:00
     *
     */
    public function doSaveArea()
    {
        $id = $this->input->get('id');
        $this->load->model('admin/large_area_model');
        $data['area_name'] = $this->input->get('area_name')?trim(htmlspecialchars($this->input->get('area_name'))):'';
        $data['area_person'] = $this->input->get('area_person')?trim(htmlspecialchars($this->input->get('area_person'))):'';
        if(!$this->checkLargeName($data['area_name'],$id)){
            echo $this->result_lib->setErrorsJson('大区名称已存在');
            exit();
        }
        if ($this->large_area_model->update($id,$data)) {
            echo $this->result_lib->setInfoJson('修改成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('修改失败');
        exit();
    }
    
    /*
     * 大区名称唯一性验证
     * liting
     * 2016/05/24 10:30:00
     *
     */
    public function checkLargeName($area_name,$area_id='')
    {
        $this->load->model('admin/large_area_model');
        if ($this->large_area_model->findOneByName($area_name,$area_id)) {
            return false;
        }
        return true;
    }
    /*
    * 优惠券管理
    * liting
    * 2018-06-05
    *
    */
    public function coupon()
    {
        $this->load->model('admin/product_model');
        $result = $this->product_model->getData();
        //var_dump($data);
        $datas['data'] = $result;
        $this->load->view('admin/coupon.html',$datas);
    }
}




