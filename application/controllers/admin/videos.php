<?php
require_once ('application/controllers/admin/common.php');

/**
 * 视频管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class videos extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "videos";
        $this->page_data['second_menu_flag'] = "spaceuser";
        
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
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
     * 文章列表
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
        
        $this->load->model('admin/videos_model');
        $total = $this->videos_model->count($search);
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/videos/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->videos_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        $this->page_data['result'] = $this->createData($result);
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '视频列表';
        $this->page_data['table_form'] = 'videos_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'videos_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/videos/getList";
        $this->loadView('admin/index');
    }

    /*
     * 客户列表信息构建
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function createData($doctor_list)
    {
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='20%'>标题</th><th width='10%'>类别</th><th width='35%'>简介</th><th>图片</th><th>添加时间
    </th><th style='width:150px'>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='6'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td>" . $v['title'] . "</td>";
                $result .= "<td>" . $v['type_name'] . "</td>";
                $result .= "<td>" . mb_strcut($v['remark'],0,125,'utf-8') . "</td>";
                $result .= "<td><img src='" .$this->download_url .'videos/' . $v['images'] . "' /></td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/videos/videosEdit?id=" . $v['id'] . "'\" >编辑</button> | ";
                $result .= "<button type='button' class='btn btn-danger' onclick='videosDelete(" . $v['id'] . ")'>删除</button></td></tr>";
            }
        }
        
        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='videos_delete_all()'>批量删除</button>";
        return $result;
    }

    /*
     * 文章搜索
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['title'] = $this->input->get('title') ? trim(htmlspecialchars($this->input->get('title'))) : '';
        $this->page_data['data']['start_time'] = $this->input->get('start_time') ? trim(htmlspecialchars($this->input->get('start_time'))) : '';
        $this->page_data['data']['end_time'] = $this->input->get('end_time') ? trim(htmlspecialchars($this->input->get('end_time'))) : '';
        return $this->page_data['data'];
    }

    /*
     * 视频删除
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/videos_model');
        if ($this->videos_model->delete($id)) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }

    /*
     * 文章新建
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function add()
    {
        $this->load->model('admin/videos_model');
        $this->page_data['videos_type'] = $this->videos_model->findType();
        
        $this->page_data['commit_url'] = 'admin/videos/doAdd';
        $this->page_data['table_title_name'] = '新建视频';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'videos_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'videos_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/videos/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 文章新建处理
     * liting
     * 2016/05/12 16:30:00
     *
     */
    public function doAdd()
    {
        $data = $this->checkInfo();
        $this->load->model('admin/videos_model');
        $this->db->trans_start();
        $insert_id = $this->videos_model->insert($data);
        if ($_FILES) {
            $images = $this->getFiles($insert_id);
            if ($images && $this->videos_model->update($insert_id, array(
                'images' => $images['images']['file_name']
            ))) {
                $this->db->trans_commit();
                echo "<script>alert('添加成功');location.href='" . base_url('admin/videos/getList') . "';</script>";
                exit();
            }
            $this->db->trans_rollback();
            echo "<script>alert('文件上传失败');history.go(-1);</script>";
            exit();
        }
        $this->db->trans_rollback();
        echo "<script>alert('添加失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 视频编辑
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function videosEdit()
    {
        $this->load->model('admin/videos_model');
        $this->page_data['result'] = $this->videos_model->findOneById($this->input->get('id'));
        $this->page_data['videos_type'] = $this->videos_model->findType();
        
        $this->page_data['commit_url'] = 'admin/videos/doVideosEdit?id='.$this->input->get('id');
        $this->page_data['table_title_name'] = '编辑视频';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'videos_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'videos_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/videos/add";
        
        $this->loadView('admin/index');
    }
    
    /*
     * 视频编辑处理
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function doVideosEdit()
    {
        $id = $this->input->get('id');
        $data = $this->checkInfo();
        unset($data['add_time']);
        $this->load->model('admin/videos_model');
        $this->db->trans_start();
        if ($_FILES&&$_FILES['images']['error']!=4) {
            $images = $this->getFiles($id);
            if(!$images){
                $this->db->trans_rollback();
                echo "<script>alert('文件上传失败');history.go(-1);</script>";
                exit();
            }
            $data['images'] = $images['images']['file_name'];
        }
        
        $re = $this->videos_model->update($id,$data);
        $this->db->trans_commit();
        if($re){
            echo "<script>alert('修改成功');location.href='" . base_url('admin/videos/getList') . "';</script>";
            exit();
        }
       
        $this->db->trans_rollback();
        echo "<script>alert('添加失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 文章信息处理
     * liting
     * 2016/05/12 16:30:00
     *
     */
    public function checkInfo()
    {
        $data['title'] = $this->input->post('title') ? trim(addslashes(htmlentities($this->input->post('title')))) : '';
        $data['url'] = $this->input->post('url') ? trim(addslashes(htmlentities($this->input->post('url')))) : '';
        $data['remark'] = $this->input->post('remark') ? trim(addslashes(htmlentities($this->input->post('remark')))) : '';
        $data['type_id'] = $this->input->post('type_id') ? trim(addslashes(htmlentities($this->input->post('type_id')))) : '';
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
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
            $upload_path = $this->upload_base . "videos/";
            $img = $this->zwyl_upload->uploadInfo($id, $upload_path);
            return $img;
        }
    }
    
    /*
     * 视频名称唯一性校验
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function checkName()
    {
        $title = $this->input->get('title');
        $id = $this->input->get('id');
        $this->load->model('admin/videos_model');
        $re = $this->videos_model->findOnlyOne($title,$id);
        if($re){
            echo $this->result_lib->setErrorsJson('视频名称已存在');exit;
        }
        echo $this->result_lib->setInfoJson('');exit;
    }
}


