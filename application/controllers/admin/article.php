<?php
require_once ('application/controllers/admin/common.php');

/**
 * 文章管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class article extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "article";
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
        
        $this->load->model('admin/article_model');
        $total = $this->article_model->count($search);
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/article/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->article_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        $this->page_data['result'] = $this->createData($result);
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '患者故事列表';
        $this->page_data['table_form'] = 'article_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'article_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/article/getList";
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
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='25%'>标题</th><th width='35%'>简介</th><th width='10%'>图片</th><th width='100'>添加时间
    </th><th width='15%'>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='6'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td width='15%'>" . $v['title'] . "</td>";
                $result .= "<td>" . mb_strcut($v['remark'],0,125,'utf-8') . "</td>";
                $result .= "<td><img src='" .$this->qcode_url .'article/' . $v['images'] . "' /></td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/article/articleEdit?id=" . $v['id'] . "'\" >编辑</button> | ";
                $result .= "<button type='button' class='btn btn-danger' onclick='articleDelete(" . $v['id'] . ")'>删除</button></td></tr>";
            }
        }
        
        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='article_delete_all()'>批量删除</button>";
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
     * 文章删除
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/article_model');
        if ($this->article_model->delete($id)) {
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
        $this->page_data['commit_url'] = 'admin/article/doAdd';
        $this->page_data['table_title_name'] = '新建患者故事';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'article_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'article_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/article/add";
        
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
        $this->load->model('admin/article_model');
        $this->db->trans_start();
        $insert_id = $this->article_model->insert($data);
        if ($_FILES['images']['error'] == '4') {
            $this->db->trans_rollback();
            echo "<script>alert('文件上传失败');history.go(-1);</script>";
            exit();
        }
        $images = $this->getFiles($insert_id);
        if ($images && $this->article_model->update($insert_id, array(
            'images' => $images['images']['file_name']
        ))) {
            $this->db->trans_commit();
            echo "<script>alert('添加成功');location.href='" . base_url('admin/article/getList') . "';</script>";
            exit();
        }
        
        $this->db->trans_rollback();
        echo "<script>alert('文件上传失败');history.go(-1);</script>";
        exit();
        $this->db->trans_rollback();
        echo "<script>alert('添加失败');history.go(-1);</script>";
        exit();
    }

    /*
     * 文章编辑
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function articleEdit()
    {
        $this->load->model('admin/article_model');
        $this->page_data['result'] = $this->article_model->findOneById($this->input->get('id'));
        $this->page_data['commit_url'] = 'admin/article/doEdit?id=' . $this->input->get('id');
        $this->page_data['table_title_name'] = '编辑患者故事';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'article_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'article_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/dealer/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 文章编辑处理
     * liting
     * 2016/05/12 15:00:00
     *
     */
    public function doEdit()
    {
        $id = $this->input->get('id');
        $data = $this->checkInfo();
        unset($data['add_time']);
        $this->load->model('admin/article_model');
        $this->db->trans_start();
        $insert_id = $this->article_model->update($id, $data);

        if ($_FILES['images']['error'] != '4') {
            $images = $this->getFiles($id);

            if ($images && $this->article_model->update($id, array(
                'images' => $images['images']['file_name']
            ))) {
                $this->db->trans_commit();
                echo "<script>alert('修改成功');location.href='" . base_url('admin/article/getList') . "';</script>";
                exit();
            }
            $this->db->trans_rollback();
            echo "<script>alert('文件上传失败');history.go(-1);</script>";
            exit();
        }
        if ($insert_id) {
            $this->db->trans_commit();
            echo "<script>alert('修改成功');location.href='" . base_url('admin/article/getList') . "';</script>";
            exit();
        }
        $this->db->trans_rollback();
        echo "<script>alert('修改失败');history.go(-1);</script>";
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
            $upload_path = $this->upload_base . "article/";

            $img = $this->zwyl_upload->uploadInfo($id, $upload_path);
            //var_dump($upload_path);die;
            return $img;
        }
    }
    
    /*
     * 文章名称唯一性校验
     * liting
     * 2016/05/12 16:00:00
     *
     */
    public function checkName()
    {
        $title = $this->input->get('title');
        $id = $this->input->get('id');
        $this->load->model('admin/article_model');
        $re = $this->article_model->findOnlyOne($title,$id);
        if($re){
            echo $this->result_lib->setErrorsJson('文章标题已存在');exit;
        }
        echo $this->result_lib->setInfoJson('');exit;
    }
}


