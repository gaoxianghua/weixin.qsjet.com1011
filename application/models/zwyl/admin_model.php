<?php
require_once ('application/core/MY_Model.php');

class admin_model extends MY_Model
{

    public $table_name = 'auth_user';

    public $user_permission;

    public $user_permission_view;

    public $permission;

    public function __construct()
    {
        parent::__construct();
        $this->user_permission = new user_permission();
        $this->user_permission_view = new user_permission_view();
        $this->permission = new permission();
    }

    public function createUser($username, $name)
    {
        $data['username'] = $username;
        $data['name'] = $name;
        $data['token'] = $this->generateToken($username);
        $data['date_recorded'] = getMyDate();
        return $this->save($data);
    }
}

class user_permission extends MY_Model
{

    public $table_name = 'auth_user_permissions';

    public function updatePermissions($user_id, $permissions)
    {
        $this->db->trans_start();
        $this->deleteByParams(array(
            'user_id' => $user_id
        ));
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
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}

class user_permission_view extends MY_Model
{

    public $table_name = 'auth_user_permission_view';
}

class permission extends MY_Model
{

    public $table_name = 'auth_permission';
}
?>