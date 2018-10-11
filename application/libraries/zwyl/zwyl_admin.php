<?php
require_once ('application/libraries/zwyl/base.php');

/**
 *
 * Enter description here ...
 * @auth zr
 * @date 2015-1-28 下午05:50:31
 *
 * @property admin_model $admin_model
 */
class zwyl_admin extends base
{

    public $default_password = '123456';

    public function __construct()
    {
        parent::__construct();
    }

    public function addUser($username, $name, $permissions)
    {
        $this->model('zwyl/admin_model');
        $data['username'] = $username;
        $data['name'] = $name;
        $data['token'] = $this->generateToken($username);
        $data['date_recorded'] = getMyDate();
        $user_id = $this->admin_model->save($data);
        if ($user_id) {
            if ($permissions) {
                $datas = array();
                foreach ($permissions as $permission_id) {
                    $datas[] = array(
                        'user_id' => $user_id,
                        'permission_id' => $permission_id
                    );
                }
                $this->admin_model->user_permission->saveBatch($datas);
            }
            return $user_id;
        }
        return false;
    }

    public function updateUser($user_id, $username, $name, $permissions)
    {
        $this->model('zwyl/admin_model');
        $data['name'] = $name;
        // $data['token'] = $this->generateToken($username);
        $flag = $this->admin_model->update(array(
            'id' => $user_id
        ), $data);
        $this->admin_model->user_permission->updatePermissions($user_id, $permissions) >= 0;
        return true;
    }

    public function getUser($user_id)
    {
        $this->model('zwyl/admin_model');
        $user = $this->admin_model->findOne(array(
            'id' => $user_id
        ), 'name,username,date_recorded,last_login');
        return $user;
    }

    public function deleteUser($user_id)
    {
        $this->model('zwyl/admin_model');
        return $this->admin_model->update(array(
            'id' => $user_id
        ), array(
            'is_valid' => '0'
        ));
    }

    public function getPermissions()
    {
        $this->model('zwyl/admin_model');
        $permissions = $this->admin_model->permission->find();
        return $permissions;
    }

    public function getPermissionsByUserId($user_id)
    {
        $this->model('zwyl/admin_model');
        $permissions = $this->admin_model->user_permission->find(array(
            'user_id' => $user_id
        ), 'permission_id');
        if ($permissions) {
            $data = array();
            foreach ($permissions as $permission) {
                $data[] = $permission['permission_id'];
            }
            return $data;
        }
        return false;
    }

    public function resetPwd($user_id)
    {
        $this->model('zwyl/admin_model');
        $user = $this->admin_model->findOne(array(
            'id' => $user_id
        ));
        return $this->admin_model->update(array(
            'id' => $user_id
        ), array(
            'password' => md5($this->default_password),
            'token' => $this->generateToken($user['username'])
        )) >= 0;
    }

    public function changePwd($user_id, $password, $new_password)
    {
        $this->model('zwyl/admin_model');
        $user = $this->admin_model->findOne(array(
            'id' => $user_id,
            'password' => md5($password)
        ));
        if (! $user) {
            return false;
        }
        return $this->admin_model->update(array(
            'id' => $user_id
        ), array(
            'password' => md5($new_password)
        )) >= 0;
    }

    public function addPermission()
    {}

    public function deletePermission()
    {}

    public function grantPermission($user_id, $permission_ids)
    {
        $this->library('zwyl/zwyl_form');
        $this->zwyl_form->initTable('auth_user_permissions');
        $datas = array();
        foreach ($permission_ids as $permission_id) {
            $datas[] = array(
                'user_id' => $user_id,
                'permission_id' => $permission_id
            );
        }
        $this->zwyl_form->delete($id);
        $this->zwyl_form->saveBatch($datas);
    }

    private function generateToken($username)
    {
        return md5($username . $this->default_password . getMyDate() . getMicrosecond());
    }
}

?>