<?php
require_once ('application/core/MY_Model.php');

class project_model extends MY_Model
{

    public $table_name = "project";
    
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    `id`,
                    `name`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `project_url`,
                    `status`
                FROM
                    `project`
            " .$this->getSearch($search);
        
        $sql .= " order by  `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        return $result;
    }
    
    public function count($search){
        $sql = "
                SELECT
                    `id`,
                    `name`,
                    count(id) as total,
                    `add_time`
                FROM 
                    `project`
            " .$this->getSearch($search);
        $result = $this->queryOne($sql);
        return $result;
    }
    
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['name'] != '' ){
                $where .= "and `name` like('%" . $search['name']."%')"  ;
            }
            if($search['start_time'] != '' ){
                $where .= "and `add_time` >= '".$search['start_time'] ." 0:00:00 ' " ;
            }
            if($search['end_time'] != '' ){
                $where .= "and `add_time` <= '".$search['end_time'] ." 23:59:59 ' " ;
            }
            $where = trim($where,'and');
            $where = $where?' WHERE ' .$where:'' ;
        }
        return $where;
    }
    
    public function findOneById($id) {
        $sql = "
                SELECT
                    `id`,
                    `name`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `project_url`,
                    `status`
                FROM
                    `project`
                WHERE
                    `id` = $id
            " ;
        return  $this->queryOne($sql);
    }
    
    public function delete($id) {
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }
    
    public function insert($data) {
        if($this->db->insert($this->table_name,$data)) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    public function update($id,$data) {
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$data);
    }
    
    public function findOnlyOne($name,$id='') {
        $sql = "
                    SELECT
                    `id`,
                    `name`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `project_url`,
                    `status`
                FROM
                    `project`
                WHERE
                    `name` = '$name'
            ";
        if($id != ''){
            $sql .= " and id != $id ";            
        }
        return $this->queryOne($sql);
    }
   
    
}