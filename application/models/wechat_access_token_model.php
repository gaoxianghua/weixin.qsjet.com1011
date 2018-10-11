<?php
require_once ('application/core/MY_Model.php');

class wechat_access_token_model extends MY_Model
{

    public $table_name = "wechat_access_token";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function find()
    {
        $sql = "select access_token,expires_in from wechat_access_token ";
        $re = $this->query($sql);
        if ($re) {
            return $re[0];
        }
        return false;
    }

    public function add($data)
    {
        $sql_del = "delete from wechat_access_token";
        $sql_add = "insert into wechat_access_token(`access_token`,`expires_in`)values('" . $data['access_token'] . "','" . $data['expires_in'] . "')";
        $this->db->trans_begin();
        if ($this->db->query($sql_del) && $this->db->query($sql_add)) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }
}