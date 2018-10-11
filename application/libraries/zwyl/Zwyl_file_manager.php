<?php
require_once ('application/libraries/zwyl/Zwyl_base.php');

class Zwyl_file_manager extends zwyl_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建文件夹
     *
     * @param unknown_type $path            
     * @author zr
     * @since 2015-6-3 上午12:54:31
     */
    public function createPath($path)
    {
        $mode = 0755;
        if (! file_exists($path)) {
            mkdir($path, $mode, true);
        } else {
            if (! is_dir($path)) {
                unlink($path);
                mkdir($path, $mode, true);
            }
        }
    }
}

?>