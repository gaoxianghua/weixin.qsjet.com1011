<?php
require_once ('application/libraries/zwyl/Zwyl_base.php');

/**
 *
 * Enter description here ...
 *
 * @author zr
 * @since 2015-6-3 上午12:21:58
 * @property upload $upload
 * @property zwyl_file_manager $zwyl_file_manager
 */
class Zwyl_upload extends zwyl_base
{

    public $config;

    function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @return the $config
     *         @auth zr
     *         @date 2015-6-3 上午12:20:22
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     *
     * @param field_type $config
     *            @auth zr
     *            @date 2015-6-3 上午12:20:22
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * 上传图片
     *
     * @param unknown_type $save_path            
     * @param unknown_type $field_name            
     * @param unknown_type $file_name            
     * @author zr
     * @since 2015-6-3 上午12:55:32
     */
    public function uploadImg($save_path, $field_name, $file_name)
    {
        return $this->uploadFile($save_path, $field_name, $file_name, 4096, 'jpg|png|gif|doc|docx|pdf|txt|bmp', 'doc');
    }

    /**
     * 上传文件
     *
     * @param unknown_type $save_path            
     * @param unknown_type $field_name            
     * @param unknown_type $file_name            
     * @param unknown_type $max_size            
     * @param unknown_type $allowed_types            
     * @param unknown_type $default_suffix            
     * @author zr
     * @since 2015-6-3 上午12:55:54
     */
    public function uploadFile($save_path, $field_name, $file_name, $max_size = 4096, $allowed_types = '*', $default_suffix = '')
    {
        if (! isset($_FILES) || ! isset($_FILES[$field_name])) {
            return $this->zwyl_result->setError($field_name . '没有上传', ZWYL_RESULT_FILE_NOT_UPLOAD);
        }
        $this->library('zwyl/zwyl_file_manager');
        $this->zwyl_file_manager->createPath($save_path);
        $str_tmp = explode('.', $_FILES[$field_name]['name']);
        $size = sizeof($str_tmp);
        if ($size > 1) {
            $_FILES[$field_name]['name'] = "{$file_name}.{$str_tmp[$size-1]}";
        } else {
            $_FILES[$field_name]['name'] = "{$file_name}.{$default_suffix}";
        }
        $file_name = $_FILES[$field_name]['name'];
        $this->config['upload_path'] = $save_path;
        $this->config['allowed_types'] = $allowed_types;
        $this->config['max_size'] = $max_size;
        $this->config['overwrite'] = True;
        $this->library('upload', $this->config);
        $this->upload->initialize($this->config);
        
        if (! $this->upload->do_upload($field_name)) {
            if (isset($_FILES[$field_name]['error']) && $_FILES[$field_name]['error'] != 4 && $_FILES[$field_name]['error'] <= 8) {
                $error_info = $this->upload->display_errors();
                if ($error_info) {
                    return $this->zwyl_result->setError("上传错误：" . $error_info);
                }
            }
        }
        return $this->zwyl_result->setInfo(array(
            'file_name' => $file_name
        ));
    }

    public function uploadInfo($name, $upload_path)
    {
        $this->load->library('result_lib');
        //$img = "";
        foreach ($_FILES as $k => $v) {
            if ($_FILES[$k]['name'] != '') {
                $zwyl_result_logo = $this->uploadImg($upload_path, $k, $name);
                if ($zwyl_result_logo->code) {
                    $img[$k] = $zwyl_result_logo->info;
                } else {
                    return $this->result_lib->setErrorsJson('添加失败');
                }
            }
        }
        //var_dump($zwyl_result_logo);
        //var_dump($img);die;
        return $img;
    }
}

?>