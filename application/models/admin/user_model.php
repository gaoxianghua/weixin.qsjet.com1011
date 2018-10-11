<?php
require_once ('application/core/MY_Model.php');

class user_model extends MY_Model
{

    public $table_name = "user";

    public function update($where,$array){
        $key = array_keys($where);
        $this->db->where($key[0],$where[$key[0]]);
        return $this->db->update($this->table_name,$array);
    }

    public function findAll($search,$pinjected_dose,$per_pinjected_dose){
        $sql = "
                SELECT
                    u.`id`,
                    u.`add_time`,
                    u.`status`,
                    u.`account`,
                    u.`mole`,
                    i.`user_id`,
                    i.`username`,
                    i.`gender`,
                    i.`injected_dose`,
                    i.`is_scleroma`,
                    i.`medical_history`,
                    i.`insulin`,
                    i.`address`,
                    i.`product_number`
                FROM
                    `user` as u
                INNER JOIN
                    `user_info` as i
                ON
                    u.`id` = i.`user_id`
            " .  $this->getSearch($search);
        $sql .= " order by  u.`add_time` desc ";
        $sql .= $this->getLimitStr($pinjected_dose, $per_pinjected_dose);
        $result = $this->query($sql);
        return $result;
    }

    public function count($search){
        $sql = "
                SELECT
                    count(u.`id`) as total,
                    u.`id`,
                    u.`add_time`,
                    u.`status`,
                    i.`user_id`,
                    i.`username`,
                    i.`gender`,
                    i.`injected_dose`,
                    i.`is_scleroma`,
                    i.`medical_history`,
                    i.`insulin`,
                    u.`account`,
                    i.`address`,
                    i.`product_number`
                FROM
                    `user` as u
                INNER JOIN
                    `user_info` as i
                ON
                    u.`id` = i.`user_id`
            " .  $this->getSearch($search);
        return $this->queryOne($sql);
    }

    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['gender'] != '' ){
                $where .= "and  i.`gender` = '" . $search['gender'] ."' ";
            }
//             if($search['status'] != null ){
//                 $where .= "and  u.`status` = '" . $search['status'] ."' ";
//             }
//             if($search['is_scleroma'] != '' ){
//                 $where .= "and  i.`is_scleroma` = '" . $search['is_scleroma'] ."' ";
//             }
            if($search['username'] != '' ){
                $where .= "and  i.`username` like ('%" . $search['username'] ."%') ";
            }
            $where = trim($where,'and');
            $where = $where?' WHERE ' .$where:'' ;
        }
        return $where;
    }

    public function findOneById($id) {
        $sql = "
            SELECT
                   u.`id`,
                    u.`add_time`,
                    u.`status`,
                    u.`account`,
                    u.`mole`,
                    i.`user_id`,
                    i.`username`,
                    i.`gender`,
                    i.`injected_dose`,
                    i.`is_scleroma`,
                    i.`medical_history`,
                    i.`insulin`,
                    i.`address`
                FROM
                    `user` as u
                INNER JOIN
                    `user_info` as i
                ON
                    u.`id` = i.`user_id`
                WHERE 
                    u.id = $id
            " ;
        return $this->queryOne($sql);
    }

    public function unbundling( $id ){
        $this->db->where('id',$id);
        return $this->db->update('user',array('doctor_id'=>''));
    }
    
    public function delete( $id ){
        $this->db->where_in('id',$id);
        if($this->db->delete($this->table_name)){
            $this->db->where_in('user_id',$id);
            $this->db->delete('user_info');

            $this->db->where_in('user_id',$id);
            return $this->db->delete('user_project');
        }
        return false;
    }
}