<?php
require_once ('application/libraries/zwyl/model_base.php');

/**
 *
 * Enter description here ...
 * @auth Administrator
 * @date 2014-12-29 下午05:39:01
 *
 * @property sms_client $sms_client
 */
class zwyl_passcode extends model_base
{

    public $length = 4;

    public $param_marker = '?';

    public function __construct()
    {
        parent::__construct('zwyl/base_model');
        $this->base_model->init('passcode');
    }

    /*
     * (non-PHPdoc)
     * @see base_inter::callBefore()
     */
    function callBefore()
    {
        // TODO Auto-generated method stub
        $this->base_model->init('passcode');
    }

    /**
     *
     * 发送短信验证码
     *
     * @param string $phone_number
     *            手机号
     * @param string $msg
     *            短信内容
     * @return boolean @auth zr
     *         @time 2014-12-30 上午12:28:30
     */
    public function send($phone_number, $msg)
    {
        $this->callBefore();
        $this->base_model->init('passcode');
        $this->load->library('sms_client');
        $passcode = $this->generateNumPasscode($this->length);
        $message = $this->compileParams($msg, $passcode);
        if ($this->ci->sms_client->sendSMS($phone_number, $message)) {
            $data = array(
                'phone_number' => $phone_number,
                'passcode' => $passcode,
                'date_recorded' => getMyDate()
            );
            return $this->base_model->save($data);
        }
        return false;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $phone_number
     *            手机号
     * @param string $passcode
     *            验证码
     * @return passcode_info 验证码记录
     *         @auth zr
     *         @time 2014-12-30 上午12:29:06
     */
    public function get($phone_number, $passcode)
    {
        $this->callBefore();
        $this->base_model->init('passcode');
        $where = array(
            'phone_number' => $phone_number,
            'passcode' => $passcode
        );
        return $this->base_model->findOne($where, '', 'id desc');
    }

    /**
     *
     * 通过时间验证有效性
     *
     * @param passcode_info $passcode_info
     *            验证码记录
     * @param int $time_out
     *            过期时间 小时
     * @return boolean @auth zr
     *         @time 2014-12-30 上午12:30:10
     */
    public function verifyByTime($passcode_info, $time_out = 1)
    {
        $date_recorded = $passcode_info['date_recorded'];
        $time_expired = date('Y-m-d H:i:s', strtotime("$date_recorded +$time_out hours"));
        return $time_expired >= getMyDate();
    }

    /**
     *
     * 生成数字验证码
     *
     * @param string $length
     *            验证码长度
     * @return string 验证码
     *         @auth zr
     *         @time 2014-12-30 上午12:31:17
     */
    public function generateNumPasscode($length = 4)
    {
        $passcode = rand(pow(10, $length - 1), pow(10, $length) - 1);
        return $passcode;
    }

    /**
     *
     * 补全字符串参数
     *
     * @param string $str            
     * @param array/string $params            
     * @return string @auth zr
     *         @time 2014-12-30 上午12:32:05
     */
    public function compileParams($str, $params)
    {
        if (strpos($str, $this->param_marker) === FALSE) {
            return $str;
        }
        if (! is_array($params)) {
            $params = array(
                $params
            );
        }
        $segments = explode($this->param_marker, $str);
        if (count($params) >= count($segments)) {
            $params = array_slice($params, 0, count($segments) - 1);
        }
        $result = $segments[0];
        $i = 0;
        foreach ($params as $param) {
            $result .= $param;
            $result .= $segments[++ $i];
        }
        return $result;
    }
}

?>