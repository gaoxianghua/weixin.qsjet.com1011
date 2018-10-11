<?php
require_once ('application/libraries/zwyl/base.php');

abstract class model_base extends base
{

    function __construct($model_name)
    {
        parent::__construct();
        $this->init($model_name);
    }

    /*
     * (non-PHPdoc)
     * @see base_inter::init()
     */
    public function init($model_name)
    {
        // TODO Auto-generated method stub
        $this->load->model($model_name);
        $model_name = explode('/', $model_name);
        $model_name = end($model_name);
        $this->$model_name = $this->ci->$model_name;
        $this->model_name = $model_name;
    }

    public function getFields()
    {
        $model_name = $this->model_name;
        return $this->$model_name->listFields();
    }

    public function getFieldData()
    {
        $model_name = $this->model_name;
        return $this->$model_name->fieldData();
    }
    
    /*
     * public function __call($method,$arguments){
     * if(method_exists($this, $method)) {
     * $this->callBefore();
     * return call_user_func_array(array($this,$method),$arguments);
     * }
     * }
     */
}

?>