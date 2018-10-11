<?php
// +-------------------------------------------------
// |
// +-------------------------------------------------
// |
// +-------------------------------------------------
require_once ('application/libraries/Base.php');

class Wechatlist extends Base
{

    const API_WECHAT = "https://api.weixin.qq.com/cgi-bin/";

    const API_WECHAT_QR = "https://mp.weixin.qq.com/cgi-bin/";

    const API_WECHAT_OAUTH2 = "https://api.weixin.qq.com/sns/oauth2/";

    private $_receive = array();

    private $_msg = "";

    public $token = '';

    public $appid = '';

    public $appsecret = '';

    public function __construct()
    {
        parent::__construct();
        $CI = & get_instance();
        $CI->config->load('wechat');
        $wechat_config = $CI->config->item('wechat');
        $this->token = $wechat_config['token'];
        $this->appid = $wechat_config['appid'];
        $this->appsecret = $wechat_config['appsecret'];
        $this->library('curls');
    }

    public function getAccessToken()
    {
        // get from db if not expired
        $access_token = '';
        
        $this->model('wechat_access_token_model');
        $row = $this->wechat_access_token_model->find();
        // FIXME::访问期间也有可能过期
        if ($row && $row['access_token'] && $row['expires_in'] > date("Y-m-d H:i:s")) {
            return $row['access_token'];
        }
        
        // curl 提交，获取access_token
        $keys = self::API_WECHAT . "token?grant_type=client_credential&&appid=" . $this->appid . "&secret=" . $this->appsecret;
        $response = $this->curls->get($keys);
        $response = json_decode($response, true);
        log_message("error", json_encode($response));
        if (isset($response['errcode']) && $response['errcode'] != 0) {
            log_message("error", json_encode($response));
            exit();
        }
        // refresh db
        $CI = & get_instance();
        $expired_in = date("Y-m-d H:i:s", strtotime("+" . $response['expires_in'] . "second"));
        $data = array(
            'access_token' => $response['access_token'],
            'expires_in' => $expired_in
        );
        
        if (! $this->wechat_access_token_model->add($data)) {
            log_message('error', $this->wechat_access_token_model->add($data));
        }
        return $response['access_token'];
    }

    public function getUserInfo($open_id)
    {
        $access_token = $this->getAccessToken();
        $url = self::API_WECHAT . "user/info?access_token=" . $access_token . '&&openid=' . $open_id . '&&lang=zh_CN';
        return $this->curls->get($url);
    }
}