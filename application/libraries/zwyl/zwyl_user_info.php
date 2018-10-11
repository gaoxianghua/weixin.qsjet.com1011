<?php
require_once ('application/libraries/zwyl/model_base.php');

class zwyl_user_info extends model_base
{

    function __construct()
    {
        parent::__construct('zwyl/base_model');
        $this->base_model->init('user_info');
    }

    public function isExist($colum_name, $colum_value)
    {
        return $this->base_model->findOne(array(
            $colum_name => $colum_value
        ));
    }

    public function isExistNickName($nick_name)
    {
        return $this->isExist('nick_name', $nick_name);
    }

    public function getByUserId($user_id)
    {
        return $this->base_model->findOne(array(
            'user_id' => $user_id
        ));
    }
}

?>