<?php
require_once ('application/libraries/Base.php');

/*
 * 管理端 - 账号管理
 * @Version: 0.0.1 alpha
 * @Created: 2016/05/10 12:00:00
 */
class Admin_dealer extends Base
{

    public function __construct()
    {
        parent::__construct();
        $this->model('admin/dealer_model');
        $this->ci = &get_instance();
    }

    /*
     * 经销商信息查询
     * liting
     * 2016/05/10 16:00:00
     * array()
     */
    public function getDealerInfo($array)
    {
        if (is_array($array)) {
            return $this->dealer_model->getDealerInfo($array);
        }
        return false;
    }

    /*
     * 经销商信添加
     * liting
     * 2016/05/11 16:30:00
     * array()
     */
    public function insertDealer($data)
    {
        if ($insert_id = $this->dealer_model->insert($data)) {
            return $this->result_lib->setInfo($insert_id);
            exit();
        }
        return $this->result_lib->setErrors('添加失败');
        exit();
    }

    /*
     * 经销商信修改
     * liting
     * 2016/05/11 16:30:00
     * array()
     */
    public function updateDealer($id, $data)
    {
        if ($this->dealer_model->update($id, $data)) {
            return $this->result_lib->setInfo('修改成功');
            exit();
        }
        return $this->result_lib->setErrors('修改失败');
        exit();
    }

}
