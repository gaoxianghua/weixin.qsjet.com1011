<?php
require_once ('application/core/MY_Model.php');

class user_model extends MY_Model
{

    public $table_name = "user";

    public function findOneByOpenId($open_id)
    {
        $sql = " SELECT
                    	`id`,
                    	`open_id`,
                    	`type`,
                        `status`,
                    	`add_time`,
                    	`mole`,
                        `account`,
                        `token`
                    FROM
                        `user` 
                  WHERE
                    	`open_id` =  '" . $open_id . "' 	
                ";
        return $this->queryOne($sql);
    }
    
    public function findOneByUserId($user_id)
    {
        $sql = " SELECT
                    	`id`,
                    	`open_id`,
                    	`type`,
                        `status`,
                    	`add_time`,
                    	`mole`,
                        `account`,
                        `token`
                    FROM
                        `user`
                  WHERE
                    	`id` =  '" . $user_id . "'
                ";
        return $this->queryOne($sql);
    }
    
    public function findOneToken($token)
    {
        $sql = " SELECT
                    	`id`,
                    	`open_id`,
                    	`type`,
                        `status`,
                    	`add_time`,
                    	`mole`,
                        `account`,
                        `token`
                    FROM
                        `user`
                  WHERE
                    	`token` =  '" . $token . "'
                ";
        return $this->queryOne($sql);
    }
    
    public function insert($user)
    {
        if($this->db->insert($this->table_name,$user)){
            return $this->db->insert_id();
        }
        return false;
    }
    
    public function checkLogin($account,$password)
    {
        $sql = " SELECT
                    	`id`,
                    	`open_id`,
                        `account`,
                        `password`,
                        `token`,
                    	`status`,
                    	`type`,
                    	`add_time`,
                    	`mole`
                   FROM
                        `user` 
                  WHERE
                    	`account` =  '" . $account . "' 	
                  AND  	`password` = '" . $password . "' 
                ";
        return $this->queryOne($sql);
    }
    
    /*
     *  账号唯一性验证
     */
    public function findOnlyOne($account)
    {
        $sql = " SELECT
                    	`id`,
                        `account`,
                        `token`,
                    	`status`,
                    	`type`,
                    	`add_time`
                   FROM
                        `user`
                  WHERE
                    	`account` =  '" . $account . "'
                ";
        return $this->queryOne($sql);
    }
    
    /*
     *  账号唯一性验证
     */
    public function updatePassword($account,$password)
    {
        $this->db->where('account',$account);
        return $this->db->update('user',array('password'=>$password));
    }
    
    /*
     *  修改
     */
    public function update($id,$data,$table_name='')
    {
        $this->db->where('id',$id);
        return $this->db->update('user',$data);
    }
    
    /*
     *  注册时，现将其他openid清除
     */
    public function updateOpen($open_id)
    {
        $this->db->where('open_id',$open_id);
        return $this->db->update('user',array('open_id'=>''));
    }
    
    /*
     *  登录成功之后用户信息的修改
     */
    public function saveUserInfo($user_id,$open_id)
    {
        $this->db->trans_begin();
        $this->db->where('open_id',$open_id);
        $re1 = $this->db->update($this->table_name,array('open_id'=>''));
       
        $this->db->where('id',$user_id);
        $re2 = $this->db->update($this->table_name,array('open_id'=>$open_id));
        if( $re1 && $re2 ){
            $this->db->trans_commit();
            return true;
        }
        $this->db->trans_rollback();
        return false;
    }
    
    public function getUserPro($user_id){
        $sql= " select 
                    id,
                    user_id,
                    project_type,
                    project_num,
                    status,
                    add_time
                from 
                    user_project
                where 
                    user_id = $user_id
            ";
        return $this->query($sql);
    }
    //查询快递信息
    public function getEx($id){
        $sql = "
            SELECT
                   u.`id`,
                    e.`id` as `eid`,
                    e.`time`,
                    e.`exname`,
                    e.`exnum`,
                    e.`status`
                FROM
                    `user` as u
                RIGHT Join
                    `express` as e
                ON
                    u.`id` = e.`uid` 
                WHERE 
                    u.`id` = $id AND e.`exnum` != '' AND e.`status` != 4 AND e.`exname` != ''
            " ;
        return  $this->query($sql);
    }
    
}