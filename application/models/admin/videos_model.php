<?php
require_once ('application/core/MY_Model.php');

class videos_model extends MY_Model
{

    public $table_name = "videos";
    
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    v.`id`,
                    v.`title`,
                    v.`url`,
                    v.`remark`,
                    v.`images`,
                    v.`add_time`,
                    v.`status`,
                    v.`type_id`,
                    t.`id` as type_id,
                    t.`name` as type_name
                FROM
                    `videos` as v
                LEFT JOIN
                    `videos_type` as t
                ON
                    v.`type_id` = t.`id`
            " .  $this->getSearch($search);
        $sql .= " order by  `add_time` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        return $result;
    }
    
    public function count($search){
        $sql = "
                SELECT
                    count(v.id) as total,
                    v.`id`,
                    v.`title`,
                    v.`url`,
                    v.`remark`,
                    v.`images`,
                    v.`add_time`,
                    v.`status`,
                    v.`type_id`,
                    t.`id` as type_id,
                    t.`name` as type_name
                FROM
                    `videos` as v
                LEFT JOIN
                    `videos_type` as t
                ON
                    v.`type_id` = t.`id`
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
                    v.`id`,
                    v.`title`,
                    v.`url`,
                    v.`remark`,
                    v.`images`,
                    v.`add_time`,
                    v.`status`,
                    v.`type_id`,
                    t.`id` as type_id,
                    t.`name` as type_name
                FROM
                    `videos` as v
                LEFT JOIN
                    `videos_type` as t
                ON
                    v.`type_id` = t.`id`
                WHERE
                    v.`id` = $id
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
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$data);
    }
    
    public function findType() {
        $sql = " select id , name from videos_type ";
        return $this->query($sql);
    }
    
    public function findOnlyOne($title,$id='') {
        $sql = "
        SELECT
        `id`,
        `title`
        FROM
        `videos`
        WHERE
        `title` = '$title'
        ";
        if($id != ''){
            $sql .= " and id != $id ";
        }
        return $this->queryOne($sql);
    }
    
}