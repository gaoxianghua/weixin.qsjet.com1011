<?php

/**
 * 
 * Enter description here ...
 * @auth zr
 * @date 2014-12-26 下午05:35:21
 * @property CI_Loader $load
 * @property zwyl_result $zwyl_result
 * @property result_lib  $result_lib 
 */
class Zwyl_base
{

    public $load;

    public $ci;

    function __construct()
    {
        $this->ci = &get_instance();
        $this->load = $this->ci->load;
        $this->input = $this->ci->input;
        $this->library('zwyl/zwyl_result');
        $this->library('result_lib');
    }

    /**
     *
     * library 里导入lib
     *
     * @param unknown_type $library            
     * @param unknown_type $params            
     * @param unknown_type $object_name            
     * @author zr
     * @since 2015-6-3 上午12:53:42
     */
    public function library($library = '', $params = NULL, $object_name = NULL)
    {
        $this->load->library($library, $params, $object_name);
        if (! $object_name) {
            $arr = explode('/', $library);
            $library = end($arr);
            $library = strtolower($library);
            $this->$library = $this->ci->$library;
        } else {
            $this->$library = $this->ci->$object_name;
        }
    }

    /**
     * library 里导入model
     *
     * @param unknown_type $model            
     * @param unknown_type $name            
     * @param unknown_type $db_conn            
     * @author zr
     * @since 2015-6-3 上午12:54:12
     */
    public function model($model, $name = '', $db_conn = FALSE)
    {
        $this->load->model($model, $name, $db_conn);
        if (! $name) {
            $arr = explode('/', $model);
            $model = end($arr);
            $this->$model = $this->ci->$model;
        } else {
            $this->$model = $this->ci->$name;
        }
    }
}

?>