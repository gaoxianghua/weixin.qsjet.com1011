<?php
require_once ('application/core/MY_Model.php');

class large_area_model extends MY_Model
{

    public $table_name = "large_area";
    
    public function findAll(){
        $sql = "
                SELECT
                    `id`,
                    `area_name`,
                    `area_person`,
                    `add_time`
                FROM
                    `$this->table_name`
            " ;
        return  $this->query($sql);
    }
    
    
    public function count($key){
        $sql = "
            SELECT
                count(`id`) as total,
                `id`,
                `area_name`,
                `area_person`,
                `add_time`
            FROM
                `$this->table_name`
            " ;
        if( $key != '' ){
            $sql .= " where area_name like('%$key%') ";
        }
        return  $this->queryOne($sql);
    }
    
    public function findAllLimit($key,$page,$per_page){
        $sql = "
            SELECT
                `id`,
                `area_name`,
                `area_person`,
                `add_time`
            FROM
                `$this->table_name`
            " ;
        if( $key != '' ){
            $sql .= " where area_name like('%$key%') ";
        }
        $sql .= " order by `add_time` desc ,id desc";
        $sql .= $this->getLimitStr($page, $per_page);
        return  $this->query($sql);
    }
    
    public function findOneById($id){
        $sql = "
                SELECT
                    `id`,
                    `area_name`,
                    `area_person`,
                    `add_time`
                FROM
                    `$this->table_name`
                WHERE
                    `id`=$id
                " ;
        return  $this->queryOne($sql);
    }
    
    public function findOneByName($area_name,$area_id=''){
        $sql = "
                SELECT
                    `id`,
                    `area_name`,
                    `area_person`,
                    `add_time`
                FROM
                    `$this->table_name`
                WHERE
                    `area_name`='$area_name'
        " ;
        if($area_id!=''){
            $sql .= "  and id != $area_id";
        }
        return  $this->queryOne($sql);
    }
    
    public function insert($data){
        $data['add_time'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table_name,$data);
    }
    
    public function update($id,$data){
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$data);
    }
    
    public function delete($id) {
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }
}