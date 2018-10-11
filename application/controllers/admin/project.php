<?php
require_once ('application/controllers/admin/common.php');

/**
 * 产品管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class project extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "project";
        $this->page_data['second_menu_flag'] = "spaceuser";
        $this->load->library('result_lib');
        if (! $this->checkPermissions()) {
            redirect(base_url('admin/index/per_errors'));
        }
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
        $this->page_data['qcode_url'] = $this->qcode_url = $this->config->item('qcode_url');
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
     * 产品列表
     * liting
     * 2016/05/19 15:00:00
     *
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        
        $this->load->model('admin/project_model');
        $total = $this->project_model->count($search);
        
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/project/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->project_model->findAll($search,$this->page_data['result']['page'], $this->page_data['result']['per_page']);
        $this->page_data['result'] = $this->createData($result);
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '产品列表';
        $this->page_data['table_form'] = 'project_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'project_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/project/getList";
        $this->loadView('admin/index');
    }

    /*
     * 产品列表信息构建
     * liting
     * 2016/05/19 15:00:00
     *
     */
    public function createData($doctor_list)
    {
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='25%'>产品名称</th><th width='35%'>产品简介</th><th width='10%'>产品图片</th><th width='100'>添加时间
    </th><th width='15%'>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td>" . $v['name'] . "</td>";
                $result .= "<td>" .mb_strcut($v['remark'],0,125,'utf-8') . "</td>";
                $result .= "<td><img src='" . $this->qcode_url . 'project/' . $v['images'] . "' /></td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/project/detail?id=" . $v['id'] . "'\" >编辑</button> | ";
                $result .= "<button type='button' class='btn btn-danger' onclick='projectDelete(" . $v['id'] . ")'>删除</button></td></tr>";
            }
        }
        
        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='project_delete_all()'>批量删除</button>";
        return $result;
    }
    
    
    /*
     * 产品搜索
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['name'] = $this->input->get('name') ? trim(htmlspecialchars($this->input->get('name'))) : '';
        $this->page_data['data']['start_time'] = $this->input->get('start_time') ? trim(htmlspecialchars($this->input->get('start_time'))) : '';
        $this->page_data['data']['end_time'] = $this->input->get('end_time') ? trim(htmlspecialchars($this->input->get('end_time'))) : '';
        return $this->page_data['data'];
    }

    /*
     * 产品新建
     * liting
     * 2016/05/19 15:00:00
     *
     */
    public function add()
    {
        $this->page_data['commit_url'] = 'admin/project/doAdd';
        $this->page_data['table_title_name'] = '新建产品';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'project_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'project_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/project/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 产品新建处理
     * liting
     * 2016/05/19 15:00:00
     *
     */
    public function doAdd()
    {
        $data = $this->checkInfo();
        if($this->checkOnly($data['name'])){
            echo "<script>alert('产品名称已存在');history.go(-1);</script>";
            exit();
        }
        $this->load->model('admin/project_model');
        $this->db->trans_start();
        $insert_id = $this->project_model->insert($data);
        
        if ($_FILES['images']['error'] == '4') {
            echo "<script>alert('文件上传失败');history.go(-1);</script>";
            exit();
        }
        
        $images = $this->getFiles($insert_id);
        if ($images && $this->project_model->update($insert_id, array(
            'images' => $images['images']['file_name']
        ))) {
            $this->db->trans_commit();
            echo "<script>alert('添加成功');location.href='" . base_url('admin/project/getList') . "';</script>";
            exit();
        }
        $this->db->trans_rollback();
        
        $this->db->trans_rollback();
        echo "<script>alert('添加失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 文件上传
     * liting
     * 2016/05/12 16:30:00
     *
     */
    public function checkOnly($name,$id='')
    {
        $this->load->model('admin/project_model');
        return $this->project_model->findOnlyOne($name,$id);
    }
    
    /*
     * 文件上传
     * liting
     * 2016/05/12 16:30:00
     *
     */
    public function getFiles($id)
    {
        if (isset($_FILES['images']['name']) && ! empty($_FILES['images']['name'])) {
            $this->load->library('zwyl/zwyl_upload');
            $upload_path = $this->upload_base . "project/";
            $img = $this->zwyl_upload->uploadInfo($id, $upload_path);
            return $img;
        }
    }

    /*
     * 产品信息处理
     * liting
     * 2016/05/12 16:30:00
     *
     */
    public function checkInfo()
    {
        $data['name'] = $this->input->post('name') ? trim(addslashes(htmlentities($this->input->post('name')))) : '';
        $data['remark'] = $this->input->post('remark') ? trim(addslashes(htmlentities($this->input->post('remark')))) : '';
        $data['project_url'] = $this->input->post('project_url') ? trim(addslashes(htmlentities($this->input->post('project_url')))) : '';
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        return $data;
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
        $this->load->model('admin/project_model');
        $this->page_data['result'] = $this->project_model->findOneById($id);
        $this->page_data['commit_url'] = 'admin/project/doEdit?id=' . $id;
        $this->page_data['table_title_name'] = '产品编辑';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'project_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'project_second_menu.php';
        
        $this->loadView('admin/index');
    }

    /*
     * 产品编辑处理
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function doEdit()
    {
        $id = $this->input->get('id');
        $data = $this->checkInfo();
        if($this->checkOnly($data['name'],$id)){
            echo "<script>alert('产品名称已存在');history.go(-1);</script>";
            exit();
        }
        unset($data['add_time']);
        $this->load->model('admin/project_model');
        $this->db->trans_start();
        $insert_id = $this->project_model->update($id, $data);
        if ($_FILES['images']['error'] != '4') {
            $images = $this->getFiles($id);
            if ($images && $this->project_model->update($id, array(
                'images' => $images['images']['file_name']
            ))) {
                $this->db->trans_commit();
                echo "<script>alert('修改成功');location.href='" . base_url('admin/project/getList') . "';</script>";
                exit();
            }
            $this->db->trans_rollback();
            echo "<script>alert('文件上传失败');history.go(-1);</script>";
            exit();
        }
        
        if ($insert_id) {
            $this->db->trans_commit();
            echo "<script>alert('修改成功');location.href='" . base_url('admin/project/getList') . "';</script>";
            exit();
        }
        
        $this->db->trans_rollback();
        echo "<script>alert('修改失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 产品删除
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/project_model');
        if ($this->project_model->delete($id)) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }
    
    /*
     * 产品名称唯一性校验
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function checkName()
    {
        $name = $this->input->get('name'); 
        $id = $this->input->get('id');
        $this->load->model('admin/project_model');
        $re = $this->project_model->findOnlyOne($name,$id);
        if($re){
           echo $this->result_lib->setErrorsJson('产品名称已存在');exit;
        }
        echo $this->result_lib->setInfoJson('');exit;
    }
}
