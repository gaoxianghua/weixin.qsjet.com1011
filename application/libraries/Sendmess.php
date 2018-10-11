<?php
require_once ('application/libraries/Base.php');
/*
 * 发送短信
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
 

class SendMess extends Base
{
    
    public  $uri ;
    public  $ac ;
    public  $authkey ;
    public  $cgid ;
    public  $csid ;
    public  $t ;
    public  $c ;
    
    public function __construct(){
        $this->ci = & get_instance();
        $this->ci->config->load('sendemail');
        $email_config = $this->ci->config->item('email');
        $this->uri = $email_config['uri'];
        $this->token = $email_config['token'];
        $this->sid = $email_config['sid'];
        $this->appId = $email_config['appId'];
        
        /*       
         *  'uri'=> "http://www.ucpaas.com/maap/sms/code",
            'token'=>'798fbb499cf365253a2d84663a74f04c',
            'sid'=> "bee116cd3223f452b5c79a80536b6cfb",
            'appId'=> "8484424a31674eb891dc44e9fbca9429",
            'time'=> "5620",
            'templateId'=> "23970",
        */
    }

    public function sends($m,$templateId)
    {
        $this->templateId = $templateId;
        $time = date('YmdHis').'000';
        $date = date('Y-m-d H:i:s');
        $code = rand(1000, 9999);
        $data = "sid=".$this->sid."&appId=".$this->appId."&sign=".md5($this->sid.$time.$this->token)."&time=".$time."&templateId=".$this->templateId."&to=".$m."&param=".$code;
        $re = file_get_contents($this->uri.'?'.$data);
        $re = json_decode($re,true);
        if($re['resp']['respCode'] == 000000 ){
            return array('code'=>$code,'time_date'=>$date);
        }
        return false;
//         $data = array(
//             'sid'=>$this->sid,
//             'appId'=>$this->appId,
//             'sign'=>md5($this->sid.$time.$this->token),
//             'time'=>$time,
//             'templateId'=>$this->templateId,
//             'to'=>$m,
//             'param'=>$code,
//         );
//        $re = $this->postSMS(json_encode($data));
    }

    // 短信发送接口

    function postSMS( $data )
    {
        $ch = curl_init(); // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $this->uri); // 要访问的地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
        }
        return $res;
    }
    public function duanxin($m,$templateId)
    {

        $to = $m;

        $Token = "798fbb499cf365253a2d84663a74f04c";
        $AccountSid = "bee116cd3223f452b5c79a80536b6cfb";
        $Version = "2014-06-30";
        $appId = "1a8b0c5629cc4bfaa404f349d90c0abf";
        $templateId = $templateId;
        date_default_timezone_set('PRC');
        $time = date('YmdHms', time());
        $header = [
            'Accept:application/json',
            'Content-Type:application/json;charset=utf-8',
            'Authorization:' . Base64_encode($AccountSid . ":" . $time),
        ];
        $date = date('Y-m-d H:i:s');
        $code = rand(1000, 9999);
        //Cookie::set('duanxin', $a, 120);
        $SigParameter = strtoupper(md5($AccountSid.$Token.$time));
        $data = [
            "templateSMS"=>[
                "appId"=>$appId,
                "templateId"=>$templateId,
                "to"=>$to,
                "param"=>"$code"
            ],
        ];

        $data = json_encode($data);

        $url= "https://api.ucpaas.com/".$Version."/Accounts/".$AccountSid."/Messages/templateSMS?sig=".$SigParameter;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        if($result['resp']['respCode'] == 000000 ){
            return array('code'=>$code,'time_date'=>$date);
        }
        return false;

    }
}
?>