<?php
require_once ('application/libraries/Base.php');
/*
 * 用户公共信息
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
class Userinfo extends Base
{

    const REG_NAME = "/^[\s\x{4e00}-\x{9fa5}A-Za-z0-9·_.-]{1,15}$/u";
    const REG_PASSWORD = "/^[\s\x{4e00}-\x{9fa5}A-Za-z0-9·_.-]{6,15}$/u";
    const REG_MOBILE = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}|14[0-9]{1}[0-9]{8}|17[0-9]{1}[0-9]{8}$/";
    const REG_CODE = "/^[0-9]{4}$/u";
    const REG_CALLOUS = "/^[01]$/u";
    const REG_AGE = "/^[0-9.]{1,5}$/";
    const REG_ACCOUNT = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}|14[0-9]{1}[0-9]{8}|17[0-9]{1}[0-9]{8}$/";
    
    const ACCOUNT_ERRORS = '账号输入不正确';
    const PASSWORD_ERRORS = '密码输入不正确';
    const CODE_ERRORS = '验证码输入不正确';
    const USERNAME_ERRORS = '姓名输入不正确';
    const GENDER_ERRORS = '性别输入不正确';
    const INSULIN_ERRORS = '胰岛素输入不正确';
    const INJECTED_DOSE_ERRORS = '注射剂量输入不正确';
    const MEDICAL_HISTORY_ERRORS = '注射时间输入不正确';
    const ADDRESS_ERRORS = '地址输入不正确';
    const IS_SCLEROMA_ERRORS = '硬结输入不正确';
    const IS_PATIEN_ERRORS = '是否注射胰岛素不正确';
    
    public $insertInfo = '';

    public function __construct()
    {
        parent::__construct();
        $this->model('user_model');
        $this->ci = &get_instance();
    }
    
    // /*
    // * 查询此微信用户信息
    // * liting
    // * 2016/05/06 12:30:00
    // * 传入参数：
    // * $this->open_id；（用户微信id）
    // *
    // */
    // public function checkRegister($open_id)
    // {
    // return $this->user_model->findOneByOpen($open_id);
    // }
    
    // /*
    // * 添加此微信用户信息
    // * liting
    // * 2016/05/06 12:30:00
    // * 传入参数：
    // * $this->open_id；（用户微信id）
    // *
    // */
    // public function insertUser($open_id)
    // {
    // $user = array(
    // 'open_id'=>$open_id,
    // 'status'=>'0',
    // 'qc_code'=> $this->input->get('qc_code')?trim(htmlspecialchars($this->input->get('qc_code'))):'',
    // 'type'=>$this->input->get('qc_code')?'1':'0',
    // 'add_time'=>date('Y-m-d H:i:s'),
    // );
    
    // $qc_code_info['dealer_id'] = '';
    
    // if( $this->input->get('qc_code') ){
    // $this->model('qc_code_model');
    // $qc_code_info = $this->qc_code_model->findOneByName(trim(htmlspecialchars($this->input->get('qc_code'))));
    // }
    // $this->model('doctor_model');
    // $doctor_info = $this->doctor_model->findOneByQc(trim(htmlspecialchars($this->input->get('qc_code'))));
    // if(!$doctor_info){
    // $doctor_info['id'] = '';
    // }
    // $user += array('doctor_id'=>$doctor_info['id'],'dealer_id'=>$qc_code_info['dealer_id']);
    // if($doctor_info&&!empty($doctor_info['id'])){
    // $this->saveCount($doctor_info['id']);
    // }
    // return $this->user_model->insert($user);
    // }
    
    /*
     * 医师统计更新
     * liting
     * 2016/05/23 17:30:00
     *
     */
//     public function saveCount($doctor_id)
//     {
//         $years = date('Y');
//         $months = date('m');
//         $this->model('doctor_count_model');
//         $this->doctor_count_model->saveRecom($doctor_id, $years, $months);
//     }

    /*
     * 添加此微信用户信息
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function insertUserInfo($user_id)
//     {
//         if ($this->checkInsertInfo()) {
//             $this->insertInfo['user_id'] = $user_id;
//             $this->model('user_info_model');
//             $re = $this->user_info_model->insert($this->insertInfo);
// //             if( substr($this->insertInfo['product_number'],0,4) == 'ABCD' ){
// //                  return $this->user_model->update($user_id,array('type'=>1,'status'=>4));
// //             }
//             return $re;
//         }
//         return false;
//     }

    /*
     * 用户登录账号验证
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function check_account($v)
    {
        if(preg_match(self::REG_MOBILE, $v)){
            return false;
        }
        return self::ACCOUNT_ERRORS;
    }
    
    /*
     * 密码
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function check_password($v)
    {
        if(preg_match(self::REG_PASSWORD, $v)){
            return false;
        }
        return self::PASSWORD_ERRORS;
    }
    
    /*
     * 验证码
     * liting
     * 2016/06/02 15:00:00
     *
     */
    public function check_code($v)
    {
        if(preg_match(self::REG_CODE, $v)){
            return false;
        }
        return self::CODE_ERRORS;
    }
    /*
     * 用户名验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function check_username($v)
    {
        if(preg_match(self::REG_NAME, $v)){
            return false;
        }
        return self::USERNAME_ERRORS;
    }

    /*
     * 性别验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function check_gender($v)
    {
        if ($v == '男' || $v == '女') {
            return false;
        }
        return self::GENDER_ERRORS;
    }

    /*
     * 注射计量验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_injected_dose($v)
//     {
//         if($v=='' || preg_match('/\d{1,3}+$/u', $v) ){
//             return false;
//         }
//         return self::INJECTED_DOSE_ERRORS;
//     }


    /*
     * 病史验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_medical_history($v)
//     {
//         return false;
//     }
    
    /*
     * 是否为硬结
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_is_scleroma($v)
//     {
//         return false;
//     }

    /*
     * 胰岛素验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_insulin($v)
//     {
//         if($v=='' || preg_match(self::REG_NAME, $v) ){
//             return false;
//         }
//         return self::INSULIN;
//     }

    /*
     * 电话号码验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function check_mobile($v)
    {
        if(preg_match(self::REG_MOBILE, $v)){
            return false;
        }
        return self::MOBILE_ERRORS;
    }

    /*
     * 地址验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_address($v)
//     {
//         if(preg_match(self::REG_NAME, $v)){
//             return false;
//         }
//         return self::ADDRESS_ERRORS;
//     }
    
    /*
     * 身份验证
     * liting
     * 2016/05/06 12:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
//     public function check_is_patient($v)
//     {
//         return false;
//     }

}