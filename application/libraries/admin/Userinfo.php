<?php
require_once ('application/libraries/Base.php');

/*
 * 用户公共信息
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
class Userinfo extends Base
{

    const REG_NAME = "/^[\s\x{4e00}-\x{9fa5}A-Za-z0-9]{2,30}+$/u";

    const REG_MOBILE = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|189[0-9]{8}|147[0-9]{8}|17[07]{1}[0-9]{8}$/";

    public $insertInfo = '';

    public function __construct()
    {
        parent::__construct();
        $this->model('admin/user_model');
        $this->ci = &get_instance();
    }

    public function unbundling($id)
    {
        return $this->user_model->update(array(
            'doctor_id' => $id
        ), array(
            'doctor_id' => ''
        ));
    }
}