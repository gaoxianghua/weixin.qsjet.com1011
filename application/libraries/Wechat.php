<?php
// +-------------------------------------------------
// |
// +-------------------------------------------------
// |
// +-------------------------------------------------
require_once 'Restclient.php';

class Wechat
{

    const MSGTYPE_TEXT = 'text';

    const MSGTYPE_IMAGE = 'image';

    const MSGTYPE_LOCATION = 'location';

    const MSGTYPE_LINK = 'link';

    const MSGTYPE_EVENT = 'event';

    const MSGTYPE_MUSIC = 'music';

    const MSGTYPE_NEWS = 'news';

    const MSGTYPE_VOICE = 'voice';

    const MSGTYPE_VIDEO = 'video';

    const EVENTTYPE_SUBSCRIBE = "subscribe";
    // 关注
    const EVENTTYPE_SCAN = "SCAN";
    // 用户已关注时的扫描二维码事件推送
    const EVENTTYPE_LOCATION = "LOCATION";
    // 上报地理位置
    const EVENTTYPE_CLICK = "CLICK";
    // 自定义菜单点击事件
    const EVENTTYPE_VIEW = "VIEW";
    // 自定义菜单点击跳转事件
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
        $CI = & get_instance();
        $CI->config->load('wechat');
        $wechat_config = $CI->config->item('wechat');
        $this->token = $wechat_config['token'];
        $this->appid = $wechat_config['appid'];
        $this->appsecret = $wechat_config['appsecret'];
    }

    public function firstInit()
    {
        // 首次接入，验证信息成为开发者
        log_message('error', 'token:' . $this->token);
        $str = '';
        if (isset($_GET['echostr'])) {
            if ($this->checkSignature()) {
                $str = $_GET['echostr'];
            } else {
                log_message('error', 'Failed to check signature');
            }
        }
        
        return $str;
    }

    public function parseRequestData()
    {
        // response message from wexin
        // check valid of msg
        if (! $this->checkSignature()) {
            // TODO:log
        }
        $postStr = file_get_contents("php://input");
        
        $this->_receive = (array) simplexml_load_string($postStr, "SimpleXMLElement", LIBXML_NOCDATA);
        if ($this->_receive === false) {
            // TODO:log
        }
        return $this->_receive;
    }

    public function createMenu($menuJsonstr)
    {
        $restclient = new Restclient();
        $result = $restclient->_POST(self::API_WECHAT . "menu/create?access_token=" . $this->getAccessToken(), $menuJsonstr, '', '', 'application/json');
        log_message("error", json_encode($result));
        if (! isset($result['errcode']) || $result['errcode'] != 0) {
            log_message("error", json_encode($result));
            return array(
                'status' => 0,
                'msg' => json_encode($result)
            );
        }
        
        return array(
            'status' => 1,
            'msg' => ''
        );
    }

    public function textResponse($contentStr = "")
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag> // if mark this msg
					</xml>";
        $resultStr = sprintf($textTpl, $this->getFromUser(), $this->getToUser(), time(), self::MSGTYPE_TEXT, $contentStr);
        return $resultStr;
    }

    public function formImage()
    {}

    public function sendCustomMsg($msgArr)
    {
        $msgJson = urldecode(json_encode($msgArr)); // log_message('error', 'msg json--->' . $msgJson);
        $restclient = new Restclient();
        $response = $restclient->_POST(self::API_WECHAT . "message/custom/send?access_token=" . $this->getAccessToken(), $msgJson, '', '', 'application/json');
        // log_message('error', json_encode($response));
        if (isset($response['errcode']) && $response['errcode'] != 0) {
            log_message('error', "Error happens when send Custom Msg: " . json_encode($response));
            return false;
        }
        
        return true;
    }
    
    // 获取图文信息
    public function getMaterial($msgJson)
    {
        $restclient = new Restclient();
        $response = $restclient->_POST(self::API_WECHAT . "message/custom/send?access_token=" . $this->getAccessToken(), $msgJson, '', '', 'application/json');
        // log_message('error', json_encode($response));
        return $response;
        // if (isset($response['errcode']) && $response['errcode'] != 0) {
        // log_message('error', "Error happens when send Custom Msg: " . json_encode($response));
        // return false;
        // }
        //
        // return true;
    }

    public function getFromUser()
    {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    public function getToUser()
    {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    public function getAccessToken()
    {
        // get from db if not expired
        $access_token = '';
        $CI = & get_instance();
        $query = $CI->db->get("wechat_access_token");
        $row = $query->row_array();
        // FIXME::访问期间也有可能过期
        if ($row && $row['access_token'] && $row['expires_in'] > date("Y-m-d H:i:s")) {
            return $row['access_token'];
        }
        
        // Refresh the access token if expired
        $restclient = new Restclient();
        $response = $restclient->_GET(self::API_WECHAT . "token", array(
            "grant_type" => "client_credential",
            "appid" => $this->appid,
            "secret" => $this->appsecret
        ));
        log_message("error", json_encode($response));
        if (isset($response['errcode']) && $response['errcode'] != 0) {
            log_message("error", json_encode($response));
            exit();
        }
        // refresh db
        $expired_in = date("Y-m-d H:i:s", strtotime("+" . $response['expires_in'] . "second"));
        $data = array(
            'access_token' => $response['access_token'],
            'expires_in' => $expired_in
        );
        if (! $CI->db->empty_table("wechat_access_token")) {
            log_message('error', $CI->db->last_query());
        }
        if (! $CI->db->insert("wechat_access_token", $data)) {
            log_message('error', $CI->db->last_query());
            // return false;
        }
        
        return $response['access_token'];
    }

    public function getSubscriberInfo($openID)
    {
        $restclient = new Restclient();
        $result = $restclient->_GET(self::API_WECHAT . "user/info", array(
            "access_token" => $this->getAccessToken(),
            "openid" => $openID,
            "lang" => "zh_CN"
        ));
        if (isset($result['errcode'])) {
            log_message("error", "openID: " . $openID . ", resaon: " . json_encode($result));
            return array();
        }
        return $result;
    }

    public function getOAuth2Result($code)
    {
        $restclient = new Restclient();
        $result = $restclient->_GET(self::API_WECHAT_OAUTH2 . "access_token", array(
            "appid" => $this->appid,
            "secret" => $this->appsecret,
            "code" => $code,
            "grant_type" => "authorization_code"
        ));
        // $result = $restclient->_GET("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx1bec3643bb036013&secret=97d188a740c63cbde6275a0f99cd3d3c&code={$code}&grant_type=authorization_code");
        log_message("error", "Failed to get oAuth info, reason:" . json_encode($result));
        if (! is_array($result) || ! isset($result['openid'])) {
            log_message("error", "Failed to get oAuth info, reason:" . json_encode($result));
            return false;
        }
        
        // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx1bec3643bb036013&secret=97d188a740c63cbde6275a0f99cd3d3c&code={$code}&grant_type=authorization_code";
        //
        // $ch = curl_init($url);
        // curl_setopt($ch,CURLOPT_HEADER,0);
        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //
        // $result = curl_exec($ch);
        // $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        //
        // log_message('error' , __METHOD__ . "::curl_error::" . (curl_error($ch)));
        // log_message('error' , __METHOD__ . "::curl_error::" . (curl_errno($ch)));
        //
        // curl_close($ch);
        //
        // log_message('error' , __METHOD__ . ":::" . ($httpCode));
        // log_message('error' , __METHOD__ . ":::" . ($result));
        // $result = json_decode($result);
        return (array) $result;
    }

    public function generateQRCode($scene_id, $type = "QR_LIMIT_SCENE")
    {
        // Get ticket
        $restclient = new Restclient();
        
        if ($type == "QR_LIMIT_SCENE") {
            $params = array(
                'action_name' => 'QR_LIMIT_SCENE',
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $scene_id
                    )
                )
            );
        } else {
            // tmp qrcode
            $params = array(
                'expire_seconds' => 1800,
                'action_name' => 'QR_SCENE',
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $scene_id
                    )
                )
            );
        }
        
        $access_token = $this->getAccessToken();
        $result = $restclient->_POST(self::API_WECHAT . 'qrcode/create?access_token=' . $access_token, json_encode($params));
        if (! is_array($result) || ! isset($result['ticket'])) {
            log_message('error', 'generateQRCode results : ' . json_encode($result));
            return false;
        }
        
        $ticket = $result['ticket'];
        // Get qrcode(img)
        $url = self::API_WECHAT_QR . 'showqrcode?ticket=' . $ticket;
        $img_info = $this->downloadQRImg($url);
        
        // $qrcode = $restclient->_GET(self::API_WECHAT_QR . 'showqrcode', array('ticket'=>$ticket));
        return $img_info['body'];
    }

    private function checkSignature()
    {
        if (! isset($_GET['signature']) || ! isset($_GET['timestamp']) || ! isset($_GET['nonce'])) {
            return false;
        }
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        
        $token = $this->token;
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function downloadQRImg($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array(
            'body' => $package
        ), array(
            'header' => $httpinfo
        ));
    }

    public function getSubscriberList()
    {
        $restclient = new Restclient();
        $result = $restclient->_GET(self::API_WECHAT . 'user/get', array(
            'access_token' => $this->getAccessToken(),
            'next_openid' => ''
        ));
        if (! is_array($result) || isset($result['errcode'])) {
            log_message('error', json_encode($result));
            return array();
        }
        return $result['data']['openid'];
    }

    public function createGroup($groupnameArr)
    {
        $restclient = new Restclient();
        $result = $restclient->_POST(self::API_WECHAT . "groups/create?access_token=" . $this->getAccessToken(), json_encode(array(
            'group' => $groupnameArr
        )), 'application/json');
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            log_message("error", "Error happens:" . __METHOD__ . ":::" . json_encode($result) . ':' . json_encode(array(
                'group' => $groupnameArr
            )));
            return array();
        }
        return $result;
    }

    public function groupUser($openID, $groupId)
    {
        $restclient = new Restclient();
        $result = $restclient->_POST(self::API_WECHAT . "groups/members/update?access_token=" . $this->getAccessToken(), json_encode(array(
            "openid" => $openID,
            "to_groupid" => $groupId
        )), 'application/json');
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            log_message("error", "Error happens:" . __METHOD__ . ":::" . json_encode($result));
            
            return false;
        }
        return true;
    }

    public function getGroups()
    {
        $restclient = new Restclient();
        $result = $restclient->_GET(self::API_WECHAT . "groups/get?access_token=" . $this->getAccessToken());
        if (isset($result['errcode'])) {
            log_message("error", "Error happens:" . __METHOD__ . ":::" . json_encode($result));
            return array();
        }
        return $result;
    }

    public function getGroupIdByOpenID($openID)
    {
        $restclient = new Restclient();
        $result = $restclient->_GET(self::API_WECHAT . "groups/getid?access_token=" . $this->getAccessToken(), array(
            'openid' => $openID
        ));
        if (isset($result['errcode'])) {
            log_message("error", "Error happens:" . __METHOD__ . ":::" . json_encode($result));
            return false;
        }
        return $result ? $result['groupid'] : 0;
    }
}

?>