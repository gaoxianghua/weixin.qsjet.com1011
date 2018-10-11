<?php
require_once ('application/libraries/zwyl/base_inter.php');

/**
 *
 * Enter description here ...
 * @auth Administrator
 * @date 2014-12-26 下午05:35:21
 *
 * @property Loader $load
 * @property base_model $base_model
 * @property zwyl_result $zwyl_result
 */
abstract class base
{

    public $load;

    public $ci;

    public $model_name;

    public $library;

    public $model;

    function __construct()
    {
        $this->ci = &get_instance();
        $this->load = $this->ci->load;
        $this->input = $this->ci->input;
        $this->library('zwyl/zwyl_result');
    }

    public function library($library = '', $params = NULL, $object_name = NULL)
    {
        $this->load->library($library, $params, $object_name);
        if (! $object_name) {
            $library = explode('/', $library);
            $library = end($library);
            $this->$library = $this->ci->$library;
        } else {
            $this->$library = $this->ci->$object_name;
        }
    }

    public function model($model, $name = '', $db_conn = FALSE)
    {
        $this->load->model($model, $name, $db_conn);
        if (! $name) {
            $model = end(explode('/', $model));
            $this->$model = $this->ci->$model;
        } else {
            $this->$model = $this->ci->$name;
        }
    }
}

?>