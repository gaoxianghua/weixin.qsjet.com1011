<?php
require_once ('application/core/MY_Model.php');

class admin_user_model extends MY_Model
{

    public $table_name = "admin_user";

    public function checkLogin($LoginData){
        $sql = "
                    SELECT 
                        `id` as `admin_id`,
                        `account`,
                        `admin_name`,
                        `password`,
                        `type`,
                        `add_time`
                    FROM
                        `admin_user`
                    WHERE 
                       binary `account` = '".$LoginData['account']."'
                    AND        
                         `password` = '". md5($LoginData['password']) ."'
            ";
        return $this->queryOne($sql);
    }
    
    public function getPermissions($id){
        $sql = "
                    SELECT
                        `admin_id`,
                        `permissions_id`
                    FROM
                        `admin_permisssions`
                    WHERE
                        `admin_id` = '$id'
            ";
        return $this->query($sql);
    }
    //判断兑换账号表account账户是否删除
    public function checkEx($admin_id){
        $sql = "
                    SELECT
                        `id`
                    FROM
                        `ex_account`
                    WHERE
                        `admin_id` = '$admin_id'
            ";
        return $this->query($sql);
    }
    public function checkAccount($account){
        $sql = "
                    SELECT
                        `dealer_id`
                    FROM
                        `ex_account`
                    WHERE
                        `account` = '$account'
            ";
        return $this->query($sql);
    }
    public function update($id,$array,$table_name=''){
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$array);
    }
    
    // 判断账号是否注册
    public function checkInsertDealer($account,$id){
        $sql = " 
                SELECT 
                    `account`
                FROM 
                    `admin_user`
                WHERE
                    `account` = '$account'
        ";
        if( $id !='' ){
            $sql .= " and id != $id ";
        }
        return $this->queryOne($sql);
    }
    
    
    // 判断账号是否注册
    public function insert($tbname,$array){
        if($this->db->insert($tbname,$array)){
            return $this->db->insert_id();
        }
        return false;
    }
    // $this->db->insert('express',$data);
    // 删除
    public function delete($id,$table_name=''){
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }
    
    // 删除
    public function insertDealerPermisssions($array){
        //$re = '';
        $admin_id = $array['admin_id'];
        foreach($array['permissions_id'] as $k=>$v){
            $re[] = $this->db->insert('admin_permisssions',array('admin_id'=>$admin_id,'permissions_id'=>$v));
        }
       return $re;
    }
    
    
    // 总数
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    `id`,
                    `account`,
                    `admin_name`,
                    `is_login`,
                    `login_ip`,
                    `last_time`,
                    `add_time`,
                    `type`
                FROM
                    `admin_user`
                WHERE
                    `type` = 1
                AND
                    `account` != 'root'
            " . $this->getSearch($search);
        $sql .= " order by id desc  ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        if( $result ){
            foreach($result as $k=>$v){
                $result[$k]['permissions'] = $this->getPermissions($v['id']);
            }
        }
        return $result;
    }
    
    // 条数
    public function count($search){
        $sql = "
                SELECT 
                    count(id) as total,
                    `id`,
                    `account`,
                    `admin_name`,
                    `type`
                FROM 
                    `admin_user`
                WHERE
                    `type` = 1
                AND
                    `account` != 'root'
            " . $this->getSearch($search);
        return $this->queryOne($sql);
    }
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['admin_name'] != '' ){
                $where .= " and `admin_name` like ('%" . $search['admin_name']."%') "  ;
            }
            $where = trim($where,'and');
            $where = $where?$where:'' ;
        }
        return $where;
    }
    
    // 获取某位权限信息
    public function getPermissionsName($admin_id){
        $sql = "
                SELECT
                    ap.`id`,
                    ap.`name`,
                    ap.`codename`,
                    ap.`parent_id`,
                    ap.`url`,
                    
                    ad.`admin_id`,
                    ad.`permissions_id`
                    
                FROM
                    `auth_permission` as ap
                INNER JOIN
                    `admin_permisssions` as ad
                ON
                    ap.`id` = ad.`permissions_id`
                WHERE
                    ad.`admin_id` = $admin_id
                AND
                    ap.`parent_id` = 0
            ";
        return $this->query($sql);
    }
    
    // 查询单条信息
    public function findOneById($admin_id){
        $sql = "
                SELECT
                    `id`,
                    `account`,
                    `admin_name`,
                    `password`,
                    `type`
                FROM
                    `admin_user`
                WHERE
                    `account` != 'root'
                AND
                    `id` = $admin_id
                ";
        $result = $this->queryOne($sql);
        if( $result ){
            $result['permissions'] = $this->getPermissionsName($admin_id);
        }
        return $result;
    }
    
    // 更新权限
    public function updatePermissions($id,$data){
        $sql = "  
                    DELETE FROM `admin_permisssions` WHERE admin_id = $id
            ";
        $this->db->query($sql);
        $re = '';
        foreach($data as $k=>$v){
            $re[] = $this->db->insert('admin_permisssions',array('admin_id'=>$id,'permissions_id'=>$v));
        }
       return $re;
    }
    
}