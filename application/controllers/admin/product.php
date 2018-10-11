<?php
require_once ('application/controllers/admin/common.php');

/**
 * 注册产品管理
 * Enter description here .
 * @author
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class product extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "product";
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
     * 注册产品列表
     * liting
     * 2016/07/21 16:15:00
     */
    public function getList()
    {
        // 搜索信息
        $search = $this->getSearch();
        $page = $this->uri->segment(4);
        $this->page_data['result']['page'] = $page > 1 ? $page - 1 : 0;
        $this->page_data['result']['per_page'] = 10;

        $this->load->model('admin/product_model');
        $total = $this->product_model->count($search);
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/product/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        $result = $this->product_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        $this->page_data['result'] = $this->createData($result);
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '注册产品列表';
        $this->page_data['table_form'] = 'product_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'product_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/product/getList";
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
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='200px;'>产品编号</th><th width='200px;'>产品类别</th>
            <th width='200px;'>会员名称</th><th width='200px;'>手机号码</th><th width='200px;'>添加时间</th><th width='100px;'>操作</th></tr>";
        if (empty($doctor_list)) {
            $result .= "<tr><td colspan='8'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($doctor_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "' name='select'></td>";
                $result .= "<td>" . $v['project_num'] . "</td>";
                $result .= "<td>" . $v['project_type'] . "</td>";
                $result .= "<td>" . $v['username'] . "</td>";
                $result .= "<td>" . $v['mobile'] . "</td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td><button type='button' class='btn btn-danger' onclick='productDelete(" . $v['id'] . ")'>删除</button></td></tr>";
            }
        }

        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;margin-bottom: 1.5%;' onclick='product_delete_all()'>批量删除</button>";
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
        $this->page_data['data']['key'] = $this->input->get('key') ? trim(htmlspecialchars($this->input->get('key'))) : '';
        $this->page_data['data']['project_type'] = $this->input->get('project_type') ? trim(htmlspecialchars($this->input->get('project_type'))) : '';
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
        $this->load->model('admin/product_model');
        if ($this->product_model->delete($id)) {
            echo $this->result_lib->setInfoJson('删除成功');
            exit();
        }
        echo $this->result_lib->setErrorsJson('删除失败');
        exit();
    }
    /**
     * 快递管理
     */
    public function express()
    {
        //$query = $this->db->query("SELECT id,open_id,status FROM user WHERE id = '1127'");
        //$query = $this->db->select('id')->where('id','8')->form('express')->get();
        //$row = $query->row_array();
        $this->load->model('admin/product_model');
        $result = $this->product_model->getData();
        $datas['data'] = $result;
        //查询超过15天未收货订单
        $now = time();
        $query = $this->db->query("SELECT id FROM express WHERE deltime < $now AND status = 2");
        $row = $query->result();
        if(count($row)>0){
            foreach($row as $value){
                $this->product_model->zdsh($value->id,array('status'=>4));
            }
        }
        $this->load->view('admin/express.html',$datas);
    }
    /**
     * 添加/修改快递单号
     */
    public function editEx()
    {
        if(!empty($_GET)){
            $eid = $_GET['eid'];
            //express
            $this->load->model('admin/product_model');
            $userdata = $this->product_model->userEx($eid);
            //var_dump($userdata);
            $datas['data'] = $userdata[0];
            //user_express
            //$userex = $this->product_model->userEx($uid);
            $this->load->view('admin/edit_express.html',$datas);
        }
    }
    /**
     * 添加/修改客户信息
     */
    public function editInfo()
    {
        if(!empty($_POST)){
            $id = $_POST['id'];
            $data['ename'] = $_POST['ename'];
            $data['ephone'] = $_POST['ephone'];
            $data['eaddress'] = $_POST['eaddress'];
            $this->load->model('admin/product_model');
            $result = $this->product_model->editInfou($id,$data);
            if($result){
                echo "<script language=javascript> alert('修改成功！'); history.back();</script>";
            }else{
                echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
            }
        }

    }
    /**
     * 添加/修改快递信息
     */
    public function editExinfo()
    {
        if(!empty($_POST)){
            $id = $_POST['id'];
            $data['exname'] = $_POST['exname'];
            $data['exnum'] = $_POST['exnum'];
            $data['status'] = 2;
            $data['deltime'] = strtotime("+15 day",time());

            $this->load->model('admin/product_model');
            $result = $this->product_model->editInfoe($id,$data);
            if($result){
                echo "<script language=javascript> alert('修改成功！'); history.back();</script>";
            }else{
                echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
            }
        }
    }
    /**
     * 修改回访状态
     */
    public function editFang()
    {
        if(!empty($_POST['fang_status'])){
            $id = $_POST['id'];
            $data['fang_status'] = $_POST['fang_status'];
            $this->load->model('admin/product_model');
            $result = $this->product_model->editFangs($id,$data);
            if($result){
                echo "<script language=javascript> alert('修改成功！'); history.back();</script>";
            }else{
                echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
            }
        }else{
            echo "<script language=javascript> alert('修改失败！'); history.back();</script>";
        }

    }
    /*
     * 添加快递信息
     * 2018-08-06
     */
    public function addEx()
    {
        $this->load->view('admin/add_express.html');
    }
    /*
     * 获取到手机号查找用户
     * 2018-08-06
     */
    public function getPhone()
    {
        if(!empty($_GET['phone'])){
            $account = $_GET['phone'];
            $query = $this->db->query("SELECT id,add_time FROM user WHERE account = $account")->result_array();
            if(empty($query)){
                echo "<script language=javascript> alert('此手机号用户未注册！'); history.back();</script>";exit;
            }
            //用户id
            $user_id = $query[0]['id'];
            //注册时间
            $add_time = $query[0]['add_time'];
            $query_info = $this->db->query("SELECT username,gender,mobile FROM user_info WHERE user_id = $user_id")->result_array();
            //用户信息
            $username = $query_info[0]['username'];
            $sex = $query_info[0]['gender'];
            $mobile = $query_info[0]['mobile'];
            //查询用户注射产品信息
            $query_pro = $this->db->query("SELECT pro.id,pro.user_id,pro.project_type,pro.project_num,pro.add_time,ex.id as eid FROM user_project as pro
                                                LEFT JOIN
                                                 express as ex
                                                ON 
                                                ex.pid = pro.id
                                            WHERE user_id = $user_id")->result_array();
            $this->page_data['data']['result'] = $query_pro;
            $this->page_data['data']['username'] = $username;
            $this->page_data['data']['gender'] = $sex;
            $this->page_data['data']['mobile'] = $mobile;
            $this->page_data['data']['add_time'] = $add_time;
            $this->page_data['data']['user_id'] = $user_id;
            $this->load->view('admin/search_express.html',$this->page_data['data']);
        }
    }
    /*
     * 添加注射器 注册
     * 2018-08-07
     */
    public function addInject()
    {
        if(!empty($_POST)){
            if(empty($_POST['project_num'])){
                echo "<script language=javascript> alert('注射器编号不能为空！'); history.back(-1);</script>";exit;
            }
            $project_num = 	trim($_POST['project_num']);
            $query = $this->db->query("SELECT id FROM user_project WHERE project_num = '$project_num'")->result_array();
            if(!empty($query)){
                echo "<script language=javascript> alert('此注射器编号已存在！'); history.back(-1);</script>";exit;
            }
            $data['project_type'] = $_POST['project_type'];
            $data['project_num'] = trim($_POST['project_num']);
            $data['user_id'] = $_POST['user_id'];
            $data['add_time'] = date('Y-m-d H:i:s',time());
            $data['status'] = '';
            $re = $this->db->insert('user_project',$data);
            if($re){
                $array = array('status'=>4,'type'=>1);
                $this->db->where('id', $data['user_id']);
                $this->db->update('user', $array);
                echo "<script language=javascript> alert('添加成功！'); history.back(-1);</script>";
            }else{
                echo "<script language=javascript> alert('添加失败！'); history.back(-1);</script>";
            }
        }
    }
    /*
    * 添加快递信息-获取到pid
    * 2018-08-07
    */
    public function getPid()
    {
        if(!empty($_GET)){
            $this->page_data['data']['pid'] = $_GET['pid'];
            $this->page_data['data']['uid'] = $_GET['uid'];
            $this->load->view('admin/add_exinfo.html',$this->page_data['data']);
        }
    }
    /*
   * 添加快递信息
   * 2018-08-07
   */
    public function addExinfo()
    {
        if(!empty($_POST)){
            if($_POST['uid'] !='' && $_POST['pid'] !=''){
                $data['uid'] = $_POST['uid'];
                $data['pid'] = $_POST['pid'];
                $data['ename'] = $_POST['ename'];
                $data['ephone'] = $_POST['ephone'];
                $data['ephone'] = $_POST['ephone'];
                $data['eaddress'] = $_POST['pro'] . $_POST['city'] . $_POST['eaddress'];
                $data['time'] = time();
                $data['exname'] = '';
                $data['exnum'] = '';
                $data['status'] = 1;
                $data['updatetime'] = '';
                $data['deltime'] = '';
                $data['fang_status'] = '';
                $re = $this->db->insert('express',$data);
                if($re){
                    echo "<script language=javascript> alert('添加成功！'); history.go(-2);</script>";
                }else{
                    echo "<script language=javascript> alert('添加失败！'); history.back(-1);</script>";
                }
            }else{
                echo "<script language=javascript> alert('添加失败！'); history.back(-1);</script>";
            }
        }
    }
    /**
     * 筛选
     * 2018-08-10
     */
    public function screenGettime()
    {
        if(!empty($_GET['start']) && !empty($_GET['end']) ){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']) + (24*60*60);
            $query = $this->db->query("
            SELECT
                   u.`id`,
                    u.`status`,
                    user_project.`project_num`,
                    e.`id` as `eid`,
                    e.`ename`,
                    e.`ephone`,
                    e.`eaddress`,
                    e.`time`,
                    e.`exname`,
                    e.`exnum`,
                    e.`updatetime`,
                    e.`fang_status`,
                    e.`status`
                FROM
                     `express` as e
                LEFT JOIN 
                  user_project
                ON 
                  user_project.`id` = e.`pid`
                LEFT JOIN
                 `user` as u
                ON
                    u.`id` = e.`uid` 
                AND 
                   user_project.`user_id` = u.`id` 
                WHERE 
                    u.`status` = 4
                AND 
                    e.time > $start && e.time < $end
                    ORDER BY 
                    e.time
                    DESC 
            ")->result_array();
            //var_dump($query);
            $datas['data'] = $query;
            $datas['count'] = count($query);
            $this->load->view('admin/screen_express.html',$datas);
            //$sql .= " order by `time` desc ";
        } else{
            echo "<script language=javascript> history.back(-1);</script>";
        }

    }
    /**
     * 删除
     */
    public function delEx()
    {
        if(!empty($_GET['id'])){
            $id = $_GET['id'];
            $this->db->where_in('id',$id);
            $re = $this->db->delete('express');
            if($re){
                echo "<script language=javascript> alert('删除成功！'); history.back(-1);</script>";
            } else{
                echo "<script language=javascript> alert('删除失败！'); history.back(-1);</script>";
            }
        } else {
            echo "<script language=javascript> alert('未选择任何数据！'); history.back(-1);</script>";
        }
    }
    /**
     * 导出
     *
     * 2018-07-19
     */
    public function export()
    {
        if(!empty($_POST['id'])) {
            $ids = $_POST['id'];
            $id = implode(',', $ids);
            $this->load->model('admin/product_model');
            $result = $this->product_model->getExport($id);
            //var_dump($result);die;
            foreach ($result as $key => $value) {
                //回访状态
                if ($result[$key]['fang_status'] == 0) {
                    $result[$key]['fang_status'] = '未回访';
                } else {
                    $result[$key]['fang_status'] = '已回访';
                }
                //提交时间
                $result[$key]['time'] = date('Y-m-d H:i:s', $result[$key]['time']);
                //phone number
                $result[$key]['ephone'] = ' '. $result[$key]['ephone'];
                //状态
                if ($result[$key]['status'] == 1) {
                    $result[$key]['status'] = '未发货';
                } else {
                    if ($result[$key]['status'] == 2) {
                        $result[$key]['status'] = '已发货';
                    } else {
                        if ($result[$key]['status'] == 4) {
                            $result[$key]['status'] = '已完成';
                        } else {
                            $result[$key]['status'] = ' ';
                        }
                    }
                }
            }
            $datalist = $result;
            $this->excel($datalist);
        }else {
            echo "<script language=javascript> alert('未选择任何数据！');location.href='/admin/product/express';</script>";
        }
    }

    public function excel($datalist)
    {
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        //创建对象
        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','N','O','P');
        //表头数组
        $tableheader = array('姓名','手机号','注射器编号','地址','提交时间','快递名称','快递单号','回访状态','状态');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //表格数组
        $data=$datalist;
        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        //设置列宽度
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(44);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(16);

        //设置对齐
        $excel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //创建Excel输入对象
        $write = new \PHPExcel_Writer_Excel5($excel);
        $time=time();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename='快递信息_$time.xls'");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
}
