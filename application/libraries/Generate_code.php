<?php
require_once ('application/libraries/Base.php');

include "application/libraries/phpqrcode.php";

/*
 * 用户公共信息
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
class Generate_code extends Base
{

    const API_OPEN = "https://open.weixin.qq.com/connect/oauth2/authorize";

    public $name = '';

    public function __construct()
    {
        parent::__construct();
        $CI = & get_instance();
        
        $this->upload_base = $CI->config->item('upload_base');
        $this->download_url = $CI->config->item('download_url');
        
        $CI->config->load('wechat');
        $wechat_config = $CI->config->item('wechat');
        $this->token = $wechat_config['token'];
        $this->appid = $wechat_config['appid'];
        $this->appsecret = $wechat_config['appsecret'];
    }

    public function generate()
    {
        //error_reporting(0);
        $codeUri = $this->api_path();
        $len = strlen($codeUri);
        if ($len <= 360) {
            $file = fopen("t.txt", "r+");
            flock($file, LOCK_EX);
            if ($file) {
                $get_file = fgetss($file);
                $file2 = fopen($this->upload_base . "t.txt", "w+");
                fwrite($file2, $this->name);
            }
            flock($file, LOCK_UN);
            fclose($file);
            fclose($file2);
            
            QRcode::png($codeUri, $this->upload_base . 'qc_code/' . $this->name . '.png');
            $sc = urlencode($codeUri);
        }
        
        return $this->name;
    }

    private function getName()
    {
        $date = date('Y-m-d H:i:s');
        $rand = mt_rand(100000, 999999);
        $this->name = 'QC' . md5($date . $rand);
        return $this->name;
    }

    private function api_path()
    {
        $this->getName();
        $redirect_uri = urldecode(base_url('wechat_oAuth2/index'));
        $api_path = self::API_OPEN . '?appid=' . $this->appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=code_' . $this->getName() . // 处理地址
'#wechat_redirect';
        return $api_path;
    }
}


