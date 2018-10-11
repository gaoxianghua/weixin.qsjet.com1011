<?php
require_once ('application/core/MY_Model.php');

class days_detail_model extends MY_Model
{

    public $table_name = "";
    
    public $mole = "";
    
    public function __construct(){
    }
    
    
    public function getCardsDataList($user_id,$days_id,$mole){
        $sql = "SELECT
                    `id`,
                    `days_time`,
                    `user_id`,
                    `days_id`,
                    `sugar_morning`,
                    `sugar_noon`,
                    `sugar_night`,
                    `pressure_morning`,
                    `pressure_noon`,
                    `pressure_night`
                FROM 
                    `days_detail_$mole`
                WHERE
                    `user_id` = $user_id
                    and
                    `days_id` = $days_id
                    ";
        return $this->query($sql);
    }
    
    public function checkDataCards($days_time,$user_id,$days_id,$mole){
        $sql = "SELECT
                    `id`,
                    `days_time`,
                    `days_id`,
                    `user_id`
                FROM 
                    `days_detail_$mole`
                WHERE
                    `days_time` = '$days_time'
                     and
                    `user_id` = $user_id
                     and
                    `days_id` = $days_id
                    ";
        return $this->queryOne($sql);
    }
    
    public function insertCards($string,$mole){
        $sql = " insert into days_detail_$mole (days_time,user_id,days_id)values " .$string;
        return $this->db->query($sql);
    }
    
    public function insert($cards,$mole){
         $re = $this->db->insert("days_detail_$mole",$cards);
         if( $re ){
             return $this->db->insert_id();
         }
         return false;
    }
    
    public function update($id,$cards,$mole){
        $this->db->where('id',$id);
        return $this->db->update("days_detail_$mole",$cards);
    }

    public function delete($user_id,$id,$mole){
        $sql = " delete from days_detail_$mole where user_id = $user_id and days_id = $id ";
        return $this->db->query($sql);
    }
    
    public function findMax($user_id,$mole){
        $sql = " SELECT
                	a.`id`,
                	a.`days_time`,
                	a.`user_id`,
                	a.`days_id`,
                	a.`sugar_morning`,
                	a.`sugar_noon`,
                	a.`sugar_night`,
                	a.`pressure_morning`,
                	a.`pressure_noon`,
                	a.`pressure_night`
                FROM
                	days_detail_$mole AS a,
                	(
                		SELECT
                			max(days_time) AS max_time,
                			`id`,
                			`days_time`,
                			`user_id`
                		FROM
                			days_detail_$mole
                		WHERE
                			user_id = $user_id
                	) AS b
                WHERE
                	a.user_id = $user_id
                AND a.days_time = b.max_time ";
        return $this->queryOne($sql);
    }
}



