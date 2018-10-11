<?php

class Wechat_receive extends CI_Controller
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
    const EVENTTYPE_UNSUBSCRIBE = "unsubscribe";

    const EVENTTYPE_SCAN = "SCAN";
    // 用户已关注时的扫描二维码事件推送
    const EVENTTYPE_LOCATION = "LOCATION";
    // 上报地理位置
    const EVENTTYPE_CLICK = "CLICK";
    // 自定义菜单点击事件
    const EVENTTYPE_VIEW = "VIEW";
    // 自定义菜单点击跳转事件
    public function index()
    {
        $wx = new WeiXinTest();
        $wx->responseMsg();
        if (! $this->input->get()) {}

        $this->load->library("wechat");
        $str = $this->wechat->firstInit();
        if ($str) {
            echo $str;
            exit();
        }

        $datas = $this->wechat->parseRequestData();
        if (isset($datas['MsgType'])) {
            switch ($datas['MsgType']) {
                case self::MSGTYPE_TEXT: // user input msg
                    echo $this->formTextMsg($datas['Content']);
                    // exit;
                    break;
                case self::MSGTYPE_EVENT: // user event
                    switch ($datas['Event']) {
                        case self::EVENTTYPE_SUBSCRIBE:
                            log_message('error', 'subscribe:' . json_encode($datas));
                            //关注公众号自动回复
                            echo $this->wechat->textResponse("欢迎您进入快舒尔医疗。\n快舒尔QS-P-01无针注射器隆重上市，\n完成注册即可享5年延保和专用保护盒。\n快快点击“用户中心”菜单中的“产品注册”领取吧！");
                            $this->addSubscriber($datas); // log_message('error', json_encode($datas));

                            break;
                        case self::EVENTTYPE_UNSUBSCRIBE:
                            // Set unsubscribed
                            $this->load->model('wechat/subscriber_model');
                            $openID = $datas['FromUserName'];
                            $this->subscriber_model->setStatus($openID, subscriber_model::STATUS_UNSUBSCRIBED);
                            $datas['EventKey'] = '';
                            $this->saveSubscribeLog($datas);
                            break;
                        case self::EVENTTYPE_SCAN:
                            // Subscribe log
                            $this->saveSubscribeLog($datas);
                            break;

                        case self::EVENTTYPE_CLICK:
                            // 查看对应的key值，回复不同的信息，用于与用户交互
                            // TODO:用户行为记录

                            echo $this->formTextMsg($datas['EventKey']);
                            log_message('error', json_encode($datas['EventKey']));
                            break;
                        case self::EVENTTYPE_VIEW:
                            log_message('error', 'VIEW:' . json_encode($datas));
                            // 查看key值， 微信会自己处理跳转，无需自己跳转
                            // TODO: 用户行为记录
                            break;
                    }

                default:
                    break;
            }
        }
    }

    /*
     * 1.提供用户输入选择
     * 2. 识别用户输入
     * 3. 对用户输入回复预先设置的模式回复
     */
    private function formTextMsg($textType)
    {
        $msgText = "欢迎使用快舒尔医疗!";
        $this->load->model('auto_msg_model');
        $auto_msg_info = $this->auto_msg_model->findOneByType($textType);
        if ($auto_msg_info) {
            if ($auto_msg_info['type'] == 'build') {
                $msgText = $auto_msg_info['text_msg'];
                $results = $this->wechat->textResponse($msgText);
                //echo $results;
            } else
                if ($auto_msg_info['type'] == 'card') {
                    //echo ''; // 必须回复空字符串
                    $this->sendWechatCard('', '', $auto_msg_info['card_id']);
                } else {
                    $results = $this->wechat->textResponse($msgText);
                    //echo $results;
                }
        } else {
            switch ($textType) {
                case "测试":
                    $results = $this->wechat->textResponse('欢迎使用快舒尔医疗');
                    echo $results;
                    break;
                case "suns":
                    // $this->sendWechatCard('', 1);
                    break;
                default:
                    $results = $this->wechat->textResponse($msgText);
                    echo $results;
                    break;
            }
        }
    }

    private function formEventMsg()
    {

    }

    private function addSubscriber($params)
    {
        // Get info of the subscriber;
        $openID = $params['FromUserName'];
        $subscriber_info = $this->wechat->getSubscriberInfo($openID);

        // 获取微信用户头像，存在本地；因为微信存在图片防盗链
        // 图片存本地
        $this->uploadAvatar($openID, $subscriber_info['headimgurl']);

        $this->load->model("wechat/subscriber_model");
        $this->load->model("wechat/subscribe_log");
        // Log user event
        $data = array(
            "openID" => $openID,
            "nickname" => $subscriber_info['nickname'],
            "sex" => $subscriber_info['sex'],
            "avatar_url" => base_url() . 'uploads/avatars/' . $openID . '.jpg',
            "city" => $subscriber_info['city'],
            "country" => $subscriber_info['country'],
            "province" => $subscriber_info['province'],
            "language" => $subscriber_info['language'],
            "status" => Subscriber_model::STATUS_SUBSCRIBED,
            "date_subscribed" => date("Y-m-d H:i:s", $params['CreateTime']),
            "date_recorded" => date("Y-m-d H:i:s")
        );
        if (! $this->subscriber_model->save($data)) {
            log_message("error", "Failed to save subscriber info");
            return false;
        }

        // Eventkey can be : 1, qrscene_1, {}, false;
        if ($params['EventKey'] === false || is_object($params['EventKey'])) {
            $params['EventKey'] = '';
        }
        if (strpos($params['EventKey'], 'qrscene_') !== false) {
            $params['EventKey'] = substr($params['EventKey'], 8);
        }
        if (! $this->saveSubscribeLog($params)) {
            return false;
        }

        return true;
    }

    private function saveSubscribeLog($params)
    {
        $openID = $params['FromUserName'];
        $this->load->model("wechat/subscribe_log");
        $data_log = array(
            "openID" => $openID,
            "behavior" => $params['Event'],
            "channel_id" => $params['EventKey'],
            "behave_time" => date("Y-m-d H:i:s", $params['CreateTime']),
            "date_recorded" => date("Y-m-d H:i:s")
        );
        log_message("error", json_encode($params));
        if (! $this->subscribe_log->save($data_log)) {
            log_message("error", "Failed to save subscriber log info");
            return false;
        }

        return true;
    }

    private function uploadAvatar($openID, $img_url)
    {
        $this->load->library('restclient');
        $img_content = $this->restclient->_GET($img_url);

        $upload_path = './uploads/avatars/';
        log_message('error', $upload_path);
        if (! file_exists($upload_path)) {
            @mkdir($upload_path);
        }

        $file_name = $openID . ".jpg";
        file_put_contents($upload_path . $file_name, $img_content);
    }

    public function createMenu($dataBut = '')
    {
        $this->load->library("wechat");
        $menuJsonStr = $this->formatMenuStr($dataBut);
        $result = $this->wechat->createMenu($menuJsonStr);
        if (! $result['status']) {
            echo json_encode(array(
                'info' => $result['msg'],
                'result_code' => '400'
            ));
            return;
        }
        echo json_encode(array(
            'info' => "自定义菜单创建成功",
            'result_code' => '200'
        ));
    }
    /*
     * 上传永久素材(图片)
     * Gao
     * 2018-06-25
     */
    public function uploadimg()
    {
        //access_token
        $token = '14_PfVXKda4BCY-sZAFIqN1Y-SxnrweNSHa3aRdtEEKHN1v9oLWRKqPQmBmLhAj2rrn9_hiaMZUBjhBfMK_314ObSroXPsGWHFNKBrhbB1DdUOgxcb0-_q7_0gJA8zyTHWoSd_vp57T0X-tudmPHJGeABADFC';

        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$token."&type=image";
        //目录文件 /home/wwwroot/weixin.qsjet.com/0822.jpg
        $imageName = "20180927.jpg";
        $image = new CURLFile($imageName);
        $data = ['media' => $image];

        echo $this->post($url,$data);
    }
    public static function post($url,$data)
    {
        //1 初始curl
        $ch = curl_init($url);

        //2 设置参数
        //是否返回数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);//是否显示请求头
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//请求超时时间
        curl_setopt($ch, CURLOPT_POST, true);//使用post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//使用post请求的参数


        //3执行
        $content = curl_exec($ch);

        //4 关闭
        curl_close($ch);

        return $content;
    }

    private function formatMenuStr($dataBut = '')
    {
        $auth_redirect_uri = base_url() . "wechat_oAuth2/index";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?" . "appid=" . $this->wechat->appid . "&redirect_uri=" . urldecode($auth_redirect_uri) . "&response_type=code&scope=snsapi_base"; // &state=STATE#wechat_redirect
        $menuArr = array(
            "button" => array(
                array(
                    "name" => urlencode("快舒尔"),
                    "sub_button" => array(
                       /* array(
                            "type" => "view",
                            "name" => urlencode("领取资料"),
                            "url" => $url . "&state=cmef#wechat_redirect"
                        ),*/
                        array(
                            "type" => "view",
                            "name" => urlencode("公司介绍"),
                            "url" => $url . "&state=company#wechat_redirect"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("新闻报道"),
                            "url" => $url . "&state=about#wechat_redirect"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("患者故事"),
                            "url" => $url . "&state=article#wechat_redirect"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("往期回顾"),
                            "url" => "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzA3MDkyMDgwOA==&scene=126#wechat_redirect"
                        ),
                    )
                ),
                array(
                    "name" => urlencode("产品中心"),
                    "sub_button" => array(
                        array(
                            "type" => "click",
                            "name" => urlencode("QS-P型无针注射器"),
                            "key" =>"qs_p"

                        ),
                        array(
                            "type" => "click",
                            "name" => urlencode("QB-P智能控温管家"),
                            "key" => "qb_p"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("操作视频"),
                            "url" =>  "http://weixin.qsjet.com/product/player"
                        ),
                    )
                ),
                array(
                    "name" => urlencode("用户中心"),
                    "sub_button" => array(
                        array(
                            "type" => "view",
                            "name" => urlencode("会员登录"),
                            "url" => $url . "&state=login#wechat_redirect"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("产品注册"),
                            "url" => $url . "&state=registerProject#wechat_redirect"
                        ),

                       /* array(
                            "type" => "view",
                            "name" => urlencode("联系客服"),
                           "url" => "http://weixin.qsjet.com/product/connect"

                        ),*/
                        array(
                            "type" => "click",
                            "name" => urlencode("联系客服"),
                            "key" => "connect"

                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("优惠兑换"),
                            "url" => $url . "&state=exlogin#wechat_redirect"
                        ),
                        array(
                            "type" => "view",
                            "name" => urlencode("我的优惠"),
                            "url" => $url . "&state=mydiscount#wechat_redirect"
                        )
                    )
                )
            ),
        );

        $menuJsonstr = urldecode(json_encode($menuArr));
        log_message('error', $menuJsonstr);
        return $menuJsonstr;
    }

    private function sendWechatCard($type = '', $menu_key = '', $card_id = '')
    {
        $this->load->model('wechat/wechat_card_model');
        if ($menu_key) {
            $card_info = $this->wechat_card_model->find(array(
                'status' => wechat_card_model::STATUS_PUBLISHED,
                'menu_key' => $menu_key
            ));
        }

        if ($card_id) {
            $card_info = $this->wechat_card_model->find(array(
                'status' => wechat_card_model::STATUS_PUBLISHED,
                'id' => $card_id
            ));
        }

        // log_message('error', json_encode($card_info));
        if (! $card_info) {
            $msgText = "欢迎使用快舒尔医疗";

            $results = $this->wechat->textResponse($msgText);
            //echo $results;
            exit();
        }

        // log_message('error', json_encode($card_info['card_info']));
        $msg_arr = array();
        foreach ($card_info['card_info'] as $value) {
            $tmp_arr = array(
                "title" => urlencode($value['title']),
                "description" => urlencode($value['description']),
                "url" => $value['url'],
                "picurl" => $value['img_url']
            );
            $msg_arr[] = $tmp_arr;
        }

        $this->load->library('wechat');
        $msg = array();
        $msg['touser'] = $this->wechat->getFromUser();
        $msg['msgtype'] = 'news';
        $msg['news']['articles'] = $msg_arr;
        $this->wechat->sendCustomMsg($msg);
    }
}
class WeiXinTest
{
    public function responseMsg()
    {
        if (phpversion() < 7) {//php版本<=5.6
            $postStr = $_GLOBALS['HTTP-RAW-POST-DATA'];
        } else {//php版本>=7
            $postStr = file_get_contents('php://input');
        }
        file_put_contents('11.txt', $postStr);
        //image : cWLb-WqG5MH7pSozCh3hx8MGCjlR1gsJRDLv2vw5mpSTv75_ZZGKffXSdLmXtrlC
        //video: RaBLD0sGWckv1YSSn5y0Wc2W7wZxHU8qbn-hclduxz8ObBaC-lQfAri9pieODx1e
        //将获得xml字符串转换成对象
        $obj = simplexml_load_string($postStr);
        $ToUserName = (string)$obj->ToUserName;
        $FromUserName = (string)$obj->FromUserName;
        $Content = (string)$obj->Content;
        $time = time() . '';
        file_put_contents('11.txt', $postStr);

        //$msg = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>";
        //$dest = "";
        if (strstr($Content, '')) {
            $dest = "11";
        } elseif (strstr($Content, '')) {
            $dest = "22";
        } else if ("CLICK" == $obj->Event) {
            $msg = "
<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>";
            if ("connect" == $obj->EventKey) {
                $mediaId = "Q7h9DNH79WEsuYNJa_OP5swP0q2xXvL3XOnDen38dIw";
                echo sprintf($msg, $FromUserName, $ToUserName, $time, 'image', $mediaId);
                return;
            } else if ('qb_p' == $obj->EventKey) {
                $mediaId = "Q7h9DNH79WEsuYNJa_OP5gsBfXTsnFP-KE2XCQg5bE4";
                $msg = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>";
                echo sprintf($msg, $FromUserName, $ToUserName, $time, 'image', $mediaId);
                return;
            } else if ('qs_p' == $obj->EventKey) {
                $mediaId = "Q7h9DNH79WEsuYNJa_OP5v9FpaiNV2PZNpFFAs3xX5s";
                $msg = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>";
                echo sprintf($msg, $FromUserName, $ToUserName, $time, 'image', $mediaId);
                return;
            }
        }
        $msg = sprintf($msg, $FromUserName, $ToUserName, $time, $dest);
        echo $msg;
    }
}
