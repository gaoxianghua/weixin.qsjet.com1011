<?php
require_once ('application/core/MY_Model.php');

class days_card_model extends MY_Model
{

    public $table_name = "days_card";
    
    public function getListByUser($user_id){
        $sql = " SELECT
                    `id`,
                    `user_id`,
                    `start_time`,
                    `end_time`,
                    `add_time`,
                    `status`,
                    `num`
                FROM 
                    `days_card`
                WHERE
                    `user_id` = $user_id
                 ORDER BY 
                     `end_time`  asc 
            ";
        return $this->query($sql);
    }
    
    public function insert($cards){
        if($this->db->insert($this->table_name,$cards)){
            return $this->db->insert_id();
        }
        return false;
    }
    
    public function findOneByCards($days_id){
        $sql = "SELECT
                    `id`,
                    `status`,
                    `num`
                FROM 
                    `days_card`
                WHERE
                    `id` = $days_id
            ";
        return $this->queryOne($sql);
    }
    
    public function delete($user_id,$id){
        $mole = $this->session->userdata('mole');
        $sql = " delete from $this->table_name where user_id = $user_id and id = $id ";
        
        $re = $this->db->query($sql);
        if($re){
            $sql = " delete from `days_detail_$mole` where user_id = $user_id and days_id = $id ";
            $re = $this->db->query($sql);
        }
        return $re;
    }
    
    public function updateByNum($user_id,$id){
        $sql = " UPDATE days_card as d INNER JOIN ( 
            			SELECT
        					id,
        					user_id,
        					days_id,
        					count(id) as total
        				FROM
        					days_detail_1
        				WHERE
        					user_id = $user_id
        				AND days_id = $id
        			) as a
                SET d.num = a.total
                WHERE
	               d.id = $id
            ";
        return $this->db->query($sql);
    }
    public function deleteUserAll($user_id,$mole){
        $this->db->where('user_id',$user_id);
        if($this->db->delete('days_card')){
            $this->db->where('user_id',$user_id);
            return $this->db->delete('days_detail_'.$mole);
        }
        return false;
    }
}













