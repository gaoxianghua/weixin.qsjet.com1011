<?php
require_once ('application/libraries/Base.php');

/*
 * 管理端 - 账号管理
 * @Version: 0.0.1 alpha
 * @Created: 2016/05/10 12:00:00
 */
class Doctor_admin extends Base
{

    public function __construct()
    {
        parent::__construct();
        $this->model('admin/doctor_model');
        $this->ci = &get_instance();
    }

    /*
     * 经销商信息查询
     * liting
     * 2016/05/11 11:30:00
     * array()
     */
    public function unbundling($id)
    {
        return $this->doctor_model->update($id, array(
            'qc_code' => ''
        ));
    }
}



