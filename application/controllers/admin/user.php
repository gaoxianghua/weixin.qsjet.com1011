<?php
require_once ('application/controllers/admin/common.php');
//require_once ('application/libraries/PhpExcel/PHPExcel.php');

/**
 * 会员管理
 * Enter description here .
 *
 *
 * ..
 *
 * @author
 *
 * @property t_user_course_rel_model $t_user_course_rel_model
 */
class user extends MY_Admin_Site
{

    public function __construct()
    {
        parent::__construct();
        $this->page_data['menu_flag'] = "user";
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
     * 会员列表
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
        
        $this->load->model('admin/user_model');
        $total = $this->user_model->count($search);
        
        $this->page_data['count'] = $total['total'] ? $total['total'] : '0';
        $this->load->library('zwyl/zwyl_table');
        $this->zwyl_table->createPagination('admin/user/getList', $this->page_data['count'], $this->page_data['result']['per_page']);
        
        $result = $this->user_model->findAll($search, $this->page_data['result']['page'], $this->page_data['result']['per_page']);
        
        $this->page_data['result'] = $this->createData($result);
        
        $this->page_data['table_frame'] = 'table_frame.php';
        $this->page_data['table_title_name'] = '会员列表';
        $this->page_data['table_form'] = 'user_form.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'user_second_menu.php';
        $this->page_data['second_menu_flag'] = "admin/user/getList";
        $this->loadView('admin/index');
    }

    /*
     * 会员列表信息构建
     * liting
     * 2016/05/10 18:00:00
     *
     */
    public function createData($user_list)
    {
        $result = "<table class='table table-bordered table-striped'><tr><th width='5%'><input style='webkit-appearance: none;' type='checkbox' class='select0' onclick='selectAll()'>全选</th><th width='8%'>姓名</th><th width='5%'>性别</th><th width='10%'>手机号</th>
            <th width='5%'> 注射剂量</th><th width='5%'>注射时间</th>
            <th width='10%'>胰岛素名称</th><th width='5%'>硬结</th><th width='10%'>地址</th><th width='200'>注册时间</th><th width='200'>操作</th></tr>";
        if (empty($user_list)) {
            $result .= "<tr><td colspan='9'><center>暂无数据！</center></td></tr>";
        } else {
            foreach ($user_list as $k => $v) {
                $result .= "<tr><td><input type='checkbox' value='" . $v['id'] . "_".$v['mole']."' name='select'></td>";
                $result .= "<td>" . $v['username'] . "</td>";
                $result .= "<td>" . $v['gender'] . "</td>";
                $result .= "<td>" . $v['account'] . "</td>";
                $result .= "<td>" . $v['injected_dose'] . "</td>";
                $result .= "<td>" . $v['medical_history'] . "</td>";
                $result .= "<td>" . $v['insulin'] . "</td>";
                $is_scleroma = $v['is_scleroma']?'有':'无';
                $result .= "<td>" . $is_scleroma . "</td>";
                $result .= "<td>" . $v['address'] . "</td>";
                $result .= "<td>" . $v['add_time'] . "</td>";
                $result .= "<td ><button type='button' class='btn btn-success' onclick=\"javascript:location.href='" . base_url() . "admin/user/detail?id=" . $v['id'] . "'\" >查看</button> 
                                 | <button type='button' class='btn btn-danger' onclick=\"userDelete('".$v['id']."_".$v['mole']."')\" >删除</button> 
                                </td></tr>";
            }
        }
        
        $result .= "</table><button type='button' class='btn btn-danger' style='float:left;margin-top:1%;' onclick='user_delete_all()'>批量删除</button>";
        return $result;
    }


//     /*
//      * 用户状态修改
//      * liting
//      * 2016/05/19 16:30:00
//      *
//      */
//     public function doStatus()
//     {
//         $id = $this->input->get('id');
//         $status = $this->input->get('status');
//         $this->load->model('admin/user_model');
//         if ($this->user_model->update(array(
//             'id' => $id
//         ), array(
//             'status' => $status
//         ))) {
//             if($status == 4){
//                 $result = $this->user_model->findOneById($id);
//                 $this->load->model('doctor_count_model');
//                 $this->doctor_count_model->saveDeal($result['doctor_id'],date('Y'),date('m'));
//             }
//             echo $this->result_lib->setInfoJson('修改成功');
//             exit();
//         }
//         echo $this->result_lib->setInfoJson('修改失败');
//         exit();
//     }

//     /*
//      * 经销商名称获取
//      * liting
//      * 2016/05/10 18:00:00
//      *
//      */
//     public function getDealerName()
//     {
//         $this->load->model('admin/dealer_model');
//         return $this->dealer_model->findAllByName();
//     }

//     /*
//      * 状态值判断
//      * liting
//      * 2016/05/11 14:00:00
//      *
//      */
//     public function handlStatus($status)
//     {
//         switch ($status) {
//             case '0':
//                 return '未审核';
//                 break;
//             case '1':
//                 return '审核未通过';
//                 break;
//             case 2:
//                 return '审核已通过';
//                 break;
//             case 3:
//                 return '未成交';
//                 break;
//             case 4:
//                 return '已成交';
//                 break;
//         }
//     }

    /*
     * 会员搜索
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function getSearch()
    {
        $this->page_data['data']['gender'] = $this->input->get('gender') ? trim(htmlspecialchars($this->input->get('gender'))) : '';
        $this->page_data['data']['illness_type'] = $this->input->get('illness_type') ? trim(htmlspecialchars($this->input->get('illness_type'))) : '';
        $this->page_data['data']['username'] = $this->input->get('username') ? trim(htmlspecialchars($this->input->get('username'))) : '';
        return $this->page_data['data'];
    }

    /*
     * 会员详情
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function detail()
    {
        $id = $this->input->get('id');
        $this->load->model('admin/user_model');
        $this->page_data['result'] = $this->user_model->findOneById($id);
        $this->page_data['table_title_name'] = '会员详情';
        $this->page_data['detail_frame'] = 'detail_frame.php';
        $this->page_data['detail_title_name'] = ' ';
        $this->page_data['detail'] = 'user_detail.php';
        $this->page_data['first_menu'] = 'index_first_menu.php';
        $this->page_data['second_menu'] = 'user_second_menu.php';
        
        $this->loadView('admin/index');
    }
    
    /*
     * 会员信息删除
     * liting
     * 2016/05/11 11:00:00
     *
     */
    public function delete()
    {
        $success_num = 0;
        $user_id='';
        $id = trim($this->input->get('id'),',');
        
        $idArr = explode(',',$id);
        foreach($idArr as $k=>$v){
            $info = explode('_',$v);
            $user_id[] = $info[0];
            $user_info[] = $info;
        }
        $this->load->model('admin/user_model');
        $this->db->trans_start();
        if($this->user_model->delete($user_id)){
            $this->load->model('days_card_model');
            foreach($user_info as $key=>$value){
                if($this->days_card_model->deleteUserAll($value[0],$value[1])){
                    $this->db->trans_commit();
                    $success_num += 1;
                }
            }
        }
        echo $this->result_lib->setInfoJson('成功删除'.$success_num.'条');
        exit();
    }
    
    /*
     * 会员导出
     * liting
     * 2016/05/10 15:00:00
     *
     */
    public function downloadUser()
    {
        $search = $this->getSearch();

        $this->load->model('admin/user_model');
        $result = $this->user_model->findAll($search, 0, 0);

        if (empty($result)) {
            echo "<script>alert('暂无数据');history.go(-1);</script>";
            exit();
        }
        $datalist = $result;
        foreach ($datalist as $key => $value) {
            if($datalist[$key]['is_scleroma'] == 1){
                $datalist[$key]['is_scleroma'] = '有';
            } else {
                $datalist[$key]['is_scleroma'] = '无';
            }
            unset($datalist[$key]['status']);
            unset($datalist[$key]['id']);
            unset($datalist[$key]['mole']);
        }
        //var_dump($result);die;
        $this->excel($datalist);
        
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
        $tableheader = array('注册时间','手机号','编号','姓名','性别','注射剂量','硬结','注射时间','胰岛素名称','地址','');
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
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(24);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
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
        header("Content-Disposition:attachment;filename='会员信息_$time.xls'");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
    
}
