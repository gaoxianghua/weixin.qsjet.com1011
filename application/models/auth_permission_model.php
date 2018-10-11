<?php
require_once ('application/core/MY_Model.php');

class auth_permission_model extends MY_Model
{

    public $table_name = "auth_permission";

    public function findAll()
    {
        $sql = " select id ,codename ,name ,url ,parent_id from auth_permission ";
        $result = $this->query($sql);
        $this->load->library('recursive');
        $result = $this->recursive->getId($result);
        return $result;
    }

    public function findParent()
    {
        $sql = " select id ,codename ,name ,url ,parent_id from auth_permission where parent_id = 0";
        $result = $this->query($sql);
        $this->load->library('recursive');
        $result = $this->recursive->getId($result);
        return $result;
    }
    
    public function getPermissionByName($codename)
    {
        $sql = " select id ,codename ,name ,url ,parent_id from auth_permission where parent_id = 0 and codename = '$codename' ";
        return $this->queryOne($sql);
    }
}