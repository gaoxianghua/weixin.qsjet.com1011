<?php
require_once ('application/libraries/zwyl/model_base.php');

/**
 * 用户账号相关
 * @auth zr
 * @date 2014-12-26 下午05:31:21
 */
class zwyl_user extends model_base
{

    public $salt = "zwyl$$";

    public $username = 'phone_number';

    public $single_login = false;

    public $default_passwrod = '123456';

    public function __construct()
    {
        parent::__construct('zwyl/base_model');
        $this->base_model->init('user');
    }

    /*
     * (non-PHPdoc)
     * @see base_inter::callBefore()
     */
    function callBefore()
    {
        // TODO Auto-generated method stub
        $this->base_model->init('user');
    }

    /**
     *
     * 新用户注册，并生成TOKENN
     *
     * @param string $username            
     * @param string $password            
     * @return int $user_id 用户ID
     *         @auth zr
     *         @time 2014-12-27 下午03:50:28
     */
    public function register($username, $password, $data = '')
    {
        $this->callBefore();
        $data[$this->username] = $username;
        $data['password'] = $this->encodePassword($password);
        $data['token'] = $this->generateToken($username, $password);
        $data['date_recorded'] = getMyDate();
        $user_id = $this->base_model->save($data);
        return $user_id;
    }

    /**
     * 通过ID获取用户信息
     *
     * @param int $user_id            
     * @return $user 用户账号信息
     *         @auth zr
     *         @time 2014-12-27 下午03:51:36
     */
    public function getById($id)
    {
        $this->callBefore();
        return $this->base_model->findOne(array(
            'id' => $id
        ));
    }

    /**
     *
     * 通过TOKEN获取用户信息
     *
     * @param string $token            
     * @return $user 用户账号信息
     *         @auth zr
     *         @time 2014-12-27 下午03:53:48
     */
    public function getByToken($token)
    {
        $this->callBefore();
        return $this->base_model->findOne(array(
            'token' => $token
        ));
    }

    /**
     *
     * 通过TOKEN获取用户信息
     *
     * @param string $token            
     * @return $user 用户账号信息
     *         @auth zr
     *         @time 2014-12-27 下午03:53:48
     */
    public function getByPhoneNumber($phone_number)
    {
        $this->callBefore();
        return $this->base_model->findOne(array(
            'phone_number' => $phone_number
        ));
    }

    /**
     *
     * 用户登录,并修改最后登录时间
     *
     * @param string $username            
     * @param string $password            
     * @return $user 用户账号信息
     *         @auth zr
     *         @time 2014-12-27 下午03:54:10
     */
    public function login($username, $password)
    {
        $this->callBefore();
        $user_data = array(
            $this->username => $username,
            'password' => $this->encodePassword($password)
        );
        $user = $this->base_model->findOne($user_data);
        if ($user && $this->single_login) {
            $where = array(
                'id' => $user['id']
            );
            $data = array(
                'token' => $this->generateToken($username, $password),
                'last_login' => getMyDate()
            );
            $this->base_model->update($where, $data);
        }
        return $user;
    }

    /**
     *
     * 修改密码，并修改TOKEN
     *
     * @param int $id            
     * @param string $new_password            
     * @return boolean @auth zr
     *         @time 2014-12-27 下午04:09:40
     */
    public function modifyPassword($id, $new_password)
    {
        $this->callBefore();
        $user = $this->getById($id);
        $where = array(
            'id' => $id
        );
        $data = array(
            'password' => $this->encodePassword($new_password),
            'token' => $this->generateToken($user['phone_number'], $new_password)
        );
        return $this->base_model->update($where, $data);
    }

    /**
     *
     * 重置密码，没有修改TOKEN
     *
     * @todo 修改TOKEN
     * @param int $user_id            
     * @return boolean @auth zr
     *         @time 2014-12-27 下午04:24:45
     */
    public function resetPassword($user_id)
    {
        $this->callBefore();
        $where = array(
            'id' => $user_id
        );
        $data = array(
            'password' => $this->encodePassword($this->default_passwrod)
        );
        return $this->base_model->update($where, $data);
    }

    /**
     *
     * 生成用户的TOKEN
     *
     * @param string $username            
     * @param string $password
     *            @auth zr
     *            @time 2014-12-27 下午04:02:01
     */
    private function generateToken($username, $password)
    {
        return md5($this->salt . $username . $password . getMyDate());
    }

    /**
     *
     * 对密码进行重新编码
     *
     * @param string $password
     *            @auth zr
     *            @time 2014-12-27 下午04:02:26
     */
    private function encodePassword($password)
    {
        return md5($this->salt . $password);
    }
}

?>