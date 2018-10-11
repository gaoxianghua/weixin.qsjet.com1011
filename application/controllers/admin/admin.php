<?php
require_once ('application/controllers/admin/common.php');

/**
 * 管理员管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class admin extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "admin";
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
     * 2016/05/12 10:30:00
     *
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;
        
        $this->load->model('admin/admin_user_model');
        $total = $this->admin_user_model->count($search);
        
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/admin/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->admin_user_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        $this->page_data['result'] = $this->createData($result);
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '账号列表';
        $this->page_data['table_form'] = 'admin_user_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'admin_user_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/admin/getList";
        $this->loadView('admin/index');
    }

    /*
     * 客户列表信息构建
     * liting
     * 2016/05/12 10:30:00
     *
     */
    public function createData($doctor_list)
    {
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='8%'>编号</th><th width='10%'>姓名</th><th width='20%'>账号</th><th width='30%'>权限
    </th><th width='200'>最后一次登录时间</th><th width='15%'>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td>" . $v['id'] . "</td>";
                $result .= "<td>" . $v['admin_name'] . "</td>";
                $result .= "<td>" . $v['account'] . "</td>";
                $result .= "<td>" . $this->getPerName($v['id']) . "</td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/admin/adminEdit?id=" . $v['id'] . "'\" >编辑</button> | ";
                $result .= "<button type='button' class='btn btn-danger' onclick='adminDelete(" . $v['id'] . ")'>删除</button></td></tr>";
            }
        }
        
        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='admin_delete_all()'>批量删除</button>";
        return $result;
    }

    /*
     * 权限名称获取
     * liting
     * 2016/05/12 10:30:00
     *
     */
    public function getPerName($admin_id)
    {
        $this->load->model('admin/admin_user_model');
        $result = $this->admin_user_model->getPermissionsName($admin_id);
        if ($result) {
            $string = '';
            foreach ($result as $k => $v) {
                $string .= $v['name'] . "，";
            }
            return trim($string, '，');
        }
        return '';
    }

    /*
     * 管理员搜索
     * liting
     * 2016/05/12 11:30:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['admin_name'] = $this->input->get('admin_name') ? trim(htmlspecialchars($this->input->get('admin_name'))) : '';
        return $this->page_data['data'];
    }

    /*
     * 管理员创建
     * liting
     * 2016/05/12 11:30:00
     *
     */
    public function add()
    {
        
        // 权限
        $this->page_data['all_permission'] = $this->page_data['my_permission'];
        
        $this->page_data['commit_url'] = 'admin/admin/doAdd';
        $this->page_data['table_title_name'] = '新建账号';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'admin_user_do_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'admin_user_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/admin/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 管理员创建执行
     * liting
     * 2016/05/12 11:30:00
     *
     */
    public function doAdd()
    {
        $this->load->library('admin/admin_user');
        $this->db->trans_start();
        $re = $this->admin_user->insertDealer($this->input->post());

        if ($re['result_code'] && $re['result_code'] == 200) {
            
            $permissions = $this->input->post('permissions');

            foreach ($this->page_data['my_permission'] as $k => $v) {
                if (in_array($v['id'], $permissions) && ! empty($v['child'])) {
                    foreach ($v['child'] as $kk => $vv) {
                        $permissions[] += $vv['id'];
                    }
                }
            }
           
            $re = $this->admin_user->insertPermisssions($re['info'], $permissions);

            if ($re) {
                $this->db->trans_commit();
                echo "<script>alert('添加成功');location.href='" . base_url('admin/admin/getList') . "';</script>";
                exit();
            }
            $this->db->trans_rollback();
            echo "<script>alert('添加失败');history.go(-1);</script>";
            exit();
        } else {
            $this->db->trans_rollback();
            echo "<script>alert('" . $re['error_msg'] . "');history.go(-1);</script>";
            exit();
        }
    }

    /*
     * 管理员修改
     * liting
     * 2016/05/12 10:30:00
     *
     */
    public function adminEdit()
    {
        // 权限
        $this->page_data['resetPassword'] = false;
        $this->page_data['all_permission'] = $this->page_data['my_permission'];
        
        $id = $this->input->get('id');
        $this->load->model('admin/admin_user_model');
        $result = $this->admin_user_model->findOneById($id);
        if ($result['permissions'] && ! empty($result['permissions'])) {
            foreach ($result['permissions'] as $k => $v) {
                $result['permissions'][$k] = $v['id'];
            }
        }
        
        if( $this->session->userdata('account') === 'root' ){
            $this->page_data['resetPassword'] = true;
        }
        
        $this->page_data['result'] = $result;
        $this->page_data['commit_url'] = 'admin/admin/doAdminEdit?id=' . $id;
        $this->page_data['table_title_name'] = '编辑账号';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'admin_user_do_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'admin_user_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/admin/add";
        
        $this->loadView('admin/index');
    }

    /*
     * 管理员修改执行
     * liting
     * 2016/05/12 10:30:00
     *
     */
    public function doAdminEdit()
    {
        // 权限
        $id = $this->input->get('id');
        $this->load->library('admin/admin_user');
        $this->db->trans_start();
        $re = $this->admin_user->updateDealer($id, array(
            'type' => 1,
            'account' => $this->input->post('account'),
            'admin_name' => $this->input->post('admin_name')
        ));
        if ($re['result_code'] && $re['result_code'] == 200) {
            $permissions = $this->input->post('permissions');
            foreach ($this->page_data['my_permission'] as $k => $v) {
                if (in_array($v['id'], $permissions) && ! empty($v['child'])) {
                    foreach ($v['child'] as $kk => $vv) {
                        $permissions[] += $vv['id'];
                    }
                }
            }
            $re = $this->admin_user->updatePermissions($id, $permissions);
            if ($re) {
                $this->db->trans_commit();
                echo "<script>alert('修改成功');location.href='" . base_url('admin/admin/getList') . "';</script>";
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
     * 2016/05/12 14:30:00
     *
     */
    public function delete()
    {
        $id = trim($this->input->get('id'),',');
        $this->load->model('admin/admin_user_model');
        if ($this->admin_user_model->delete($id)) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }
    
    /*
     * 密码重置
     * liting
     * 2016/05/24 14:30:00
     *
     */
    public function resetPassword()
    {
        if( $this->session->userdata('account') === 'root' ){
            $id = $this->input->get('id');
            $this->load->model('admin/admin_user_model');
            $re = $this->admin_user_model->update($id,array('password'=>md5(123456)));
            if( $re ){
                echo $this->result_lib->setInfoJson('重置密码成功');
                exit();
            }
            echo $this->result_lib->setErrorsJson('重置密码失败');
            exit();
        }
        echo $this->result_lib->setErrorsJson('非法请求');
        exit();
    }
}



