<?php
require_once ('application/core/MY_Model.php');

class article_model extends MY_Model
{

    public $table_name = "article";
    
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    `id`,
                    `title`,
                    `url`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `status`
                FROM
                    `article`
            " .  $this->getSearch($search);
        $sql .= " order by  `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        return $result;
    }
    
    public function count($search){
        $sql = "
                SELECT
                    count(id) as total,
                    `id`,
                    `title`,
                    `url`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `status`
                FROM
                    `article`
            " .  $this->getSearch($search);
        return $this->queryOne($sql);
    }
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['title'] != '' ){
                $where .= "and `title` like('%" . $search['title']."%')"  ;
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
                    `title`,
                    `url`,
                    `remark`,
                    `images`,
                    `add_time`,
                    `status`
                FROM
                    `article`
                WHERE 
                    `id` = $id
            " ;
            return $this->queryOne($sql);
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
        //var_dump($data);die;
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$data);
    }
    
    
    public function findOnlyOne($title,$id='') {
        $sql = "
        SELECT
        `id`,
        `title`
        FROM
        `article`
        WHERE
        `title` = '$title'
        ";
        if($id != ''){
            $sql .= " and id != $id ";
        }
        return $this->queryOne($sql);
    }
}