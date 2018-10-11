<?php
require_once ('application/libraries/zwyl/Zwyl_base.php');

class Zwyl_img extends zwyl_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 生成缩略图
     *
     * @param unknown_type $source            
     * @param unknown_type $dest            
     * @param unknown_type $width            
     * @param unknown_type $height            
     * @author zr
     * @since 2015-6-3 上午12:54:52
     */
    public function createThumb($source, $dest, $width = 100, $height = 100)
    {
        $this->library('zwyl/zwyl_file_manager');
        $this->zwyl_file_manager->createPath($dest);
        $config['image_library'] = 'GD2';
        $config['source_image'] = $source;
        $config['new_image'] = $dest;
        // $config['create_thumb'] = TRUE;
        $config['quality'] = 100;
        $config['maintain_ratio'] = TRUE;
        $config['thumb_marker'] = '';
        $config['width'] = $width;
        $config['height'] = $height;
        $this->library('image_lib');
        $this->image_lib->initialize($config);
        $result = $this->image_lib->resize();
        $this->image_lib->clear();
        return $result;
    }

    /**
     * 保存图片
     *
     * @param unknown_type $source            
     * @param unknown_type $save_path            
     * @param unknown_type $img_name            
     * @author zr
     * @since 2015-6-3 上午12:55:08
     */
    public function saveImage($source, $save_path, $img_name)
    {
        $ch = curl_init($source);
        $this->library('zwyl/zwyl_file_manager');
        $this->zwyl_file_manager->createPath($save_path);
        $fp = fopen($save_path . $img_name, 'w');
        
        // set URL and other appropriate options
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 60
        );
        
        curl_setopt_array($ch, $options);
        
        $save = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        return $save;
    }
}

?>