<?php
require_once ('application/libraries/Base.php');

/*
 * 管理端 - 账号管理
 * @Version: 0.0.1 alpha
 * @Created: 2016/05/10 16:30:00
 */
class admin_qc_code extends Base
{

    public function __construct()
    {
        parent::__construct();
        $this->model('admin/qc_code_model');
        $this->ci = &get_instance();
    }

    /*
     * 经销商信息查询
     * liting
     * 2016/05/10 16:30:00
     * array()
     */
    public function getDealerQc($id)
    {
        return $this->qc_code_model->findAllByDealer($id);
    }

    /*
     * 绑定医师
     * liting
     * 2016/05/16 10:30:00
     * array()
     */
    public function binding($id, $data)
    {
        $qc_info = array(
            'status' => 3,
            'dealer_id' => $data['dealer_id'],
            'code' => md5($data['code'])
        );
        $data += $qc_info;
        // 修改二维码信息
        $this->model('admin/qc_code_model');
        $this->qc_code_model->_trans_begin();
        $re1 = $this->qc_code_model->update($id, $qc_info);
        // 添加医师信息
        $this->model('admin/doctor_model');
        $re2 = $this->doctor_model->insert($data);
        if ($re1 && $re2) {
            $this->qc_code_model->_trans_commit();
            return true;
        }
        $this->qc_code_model->_trans_rollback();
        return false;
    }

    /*
     * 解绑
     * liting
     * 2016/05/16 17:30:00
     * array()
     */
    public function unbinding($id)
    {
        $this->model('admin/qc_code_model');
        $re = $this->qc_code_model->unbinding($id);
        if (! $re) {
            return $this->result_lib->setErrorsJson('解绑失败');
            exit();
        }
        return $this->result_lib->setInfoJson('解绑成功');
        exit();
    }

    /*
     * 二维码信息检测
     * liting
     * 2016/05/10 16:30:00
     * array()
     */
    public function checkCode($id)
    {
        $this->model('admin/qc_code_model');
        $result = $this->qc_code_model->findOneById($id);
        if ($result && $result['status'] > 2) {
            return $this->result_lib->setErrors('此二维码已绑定其他医生');
            exit();
        }
        if (! $result) {
            return $this->result_lib->setErrors('此二维码信息错误');
            exit();
        }
        return $this->result_lib->setInfo($result);
        exit();
    }

    /*
     * 二维码注销
     * liting
     * 2016/05/16 12:30:00
     * array()
     */
    public function cencel($id)
    {
        $this->model('admin/qc_code_model');
        $re = $this->qc_code_model->cencel($id);
        if (! $re) {
            return $this->result_lib->setErrorsJson('注销失败');
            exit();
        }
        return $this->result_lib->setInfoJson('注销成功');
        exit();
    }

    /*
     * 二维码指派
     * liting
     * 2016/05/16 12:30:00
     * array()
     */
    public function assign($dealer_id, $qc_code_id)
    {
        $this->model('admin/qc_code_model');
        $re = $this->qc_code_model->assign($dealer_id, $qc_code_id);
        if (! $re) {
            return $this->result_lib->setErrorsJson('指派失败');
            exit();
        }
        return $this->result_lib->setInfoJson('指派成功');
        exit();
    }
}



