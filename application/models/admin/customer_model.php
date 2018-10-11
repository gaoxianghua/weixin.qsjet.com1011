<?php
require_once ('application/core/MY_Model.php');

class customer_model extends MY_Model
{

    public $table_name = "customer";

    public function update($where,$array){
        $key = array_keys($where);
        $this->db->where($key[0],$where[$key[0]]);
        return $this->db->update($this->table_name,$array);
    }
    public function findAll_admin($search,$pinjected_dose,$per_pinjected_dose){
        $sql = "
                SELECT
                    c.`id`,
                    c.`qc_code`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    c.`doctor_id`,
                    c.`dealer_id`,
                    c.`fangtime`,
                    c.`status_c`,
                    j.`dealer_name`,
                    d.doctor_id ,
                    d.doctor_name,
                    y.extime_m,
                    y.status_m,
                    y.overtime
                 FROM
                     $this->table_name as c
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id
                Left Join       
                    `coupon` as y
                 ON 
                 c.open_id  =  y.open_id
                 AND 
                 c.doctor_id  =  y.doctor_id
                 Left Join       
                    `dealer` as j
                 ON 
                 c.dealer_id  =  j.id
                 LEFT JOIN 
                 `qc_code` as qc
                 ON 
                 c.account_id = qc.account_id
                 AND 
                 c.qc_code = qc.qc_code_name
                  
            " .  $this->getSearch($search);
        $sql .= " order by `add_time` desc ";
        $sql .= $this->getLimitStr($pinjected_dose, $per_pinjected_dose);
        $result = $this->query($sql);
        return $result;
    }

    public function findAll($search,$pinjected_dose,$per_pinjected_dose){
        $sql = "
                SELECT
                    c.`id`,
                    c.`qc_code`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    c.`doctor_id`,
                    j.`id` as dealer_id,
                    c.`fangtime`,
                    c.`status_c`,
                    j.`dealer_name`,
                    ex.`account_name`,
                    d.doctor_id ,
                    d.doctor_name,
                    y.extime_m,
                    y.status_m,
                    y.overtime
                 FROM
                     $this->table_name as c
                LEFT JOIN
                    `ex_account` as ex
                ON
                   c.`account_id` = ex.`id`
                LEFT JOIN 
                    `qc_code` as qc
                ON 
                    qc.account_id = ex.id
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id
                Left Join       
                    `coupon` as y
                 ON 
                    c.open_id  =  y.open_id
                 AND 
                    c.doctor_id  =  y.doctor_id
                 Left Join       
                    `dealer` as j
                 ON 
                    c.account_id  =  ex.id
            " .  $this->getSearch($search);
        $sql .= " order by `add_time` desc ";
        $sql .= $this->getLimitStr($pinjected_dose, $per_pinjected_dose);
        $result = $this->query($sql);
        return $result;
    }
    //qc_code
    public function findAll_qc($search,$pinjected_dose,$per_pinjected_dose,$account_id){
        $sql = "
                SELECT
                    c.`id`,
                    c.`qc_code`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    c.`doctor_id`,
                    c.`dealer_id`,
                    c.`fangtime`,
                    c.`status_c`,
                    j.`dealer_name`,
                    ex.`account_name`,
                    d.doctor_id ,
                    d.doctor_name,
                    y.extime_m,
                    y.status_m,
                    y.overtime
                 FROM
                     $this->table_name as c
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id
                Left Join       
                    `coupon` as y
                 ON 
                 c.open_id  =  y.open_id
                 AND 
                 c.doctor_id  =  y.doctor_id
                 Left Join       
                    `dealer` as j
                 ON 
                 c.dealer_id  =  j.id
                  Left Join       
                    `qc_code` as qc
                 ON 
                 qc.dealer_id  =  j.id
                 AND 
                 qc.qc_code_name = c.qc_code
                 LEFT JOIN 
                  ex_account as ex
                  ON 
                  ex.id = qc.account_id
            " .  $this->getWhere($account_id) . $this->getSearch($search);
        $sql .= " order by `add_time` desc ";
        $sql .= $this->getLimitStr($pinjected_dose, $per_pinjected_dose);
        $result = $this->query($sql);
        return $result;
    }

    public function count($search){
        $sql = "
                SELECT
                    count(c.`id`) as total,
                    c.`id`,
                    c.`qc_code`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    c.`doctor_id`,
                    d.doctor_id ,
                    d.doctor_name 
                 FROM
                     $this->table_name as c
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id
                 LEFT JOIN
                    `ex_account` as ex
                ON
                   c.account_id  =  ex.id
                LEFT JOIN 
                     `qc_code` as qc
                ON 
                    c.`id` = qc.`dealer_id`
                    LEFT JOIN 
                    `dealer` as j
                    ON 
                     j.`id` = qc.`dealer_id`
                    
            " .  $this->getSearch($search);
        return $this->queryOne($sql);
    }

    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['gender'] != '' ){
                $where .= "and  c.`gender` = '" . $search['gender'] ."' ";
            }
            if($search['dealer_id'] != '' ){
                $where .= "and  c.`dealer_id` = '" . $search['dealer_id'] ."' ";
                $where .= "and  j.`id` = '" . $search['dealer_id'] ."' ";
                $where .= "and  c.`qc_code` = qc.`qc_code_name`";
                $where .= "and  c.account_id = ex.id ";
            }
            if($search['status'] != null ){
                $where .= "and  c.`status` = '" . $search['status'] ."' ";
            }
            if($search['status_c'] != null ){
                //$where .= "and  c.`status_c` != 3";
            }
            if($this->session->userdata('type') == '3') {
                if($search['username'] != '' ){
                    $admin_id = $this->session->userdata('admin_id');
                    $res = $this->db->query("select dealer_id from ex_account where admin_id = $admin_id")->result_array();
                    $dealer_id = $res[0]['dealer_id'];
                    $where .= "and  c.`mobile` like ('%" . $search['username'] ."%') or (d.doctor_name  like ('%" . $search['username'] ."%') or c.`username`  like ('%" . $search['username'] ."%')) ";
                    //$where .= "and  c.`dealer_id` = ex.`dealer_id`";
                    $where .= "and  ex.`dealer_id` = '" . $dealer_id ."' ";
                    $where .= "and  c.`qc_code` = qc.`qc_code_name`";
                    return $where;
                }
            }else if($this->session->userdata('type') == '1'){
                if($search['username'] != '' ){
                    $where .= "and  c.`mobile` like ('%" . $search['username'] ."%') or (d.doctor_name  like ('%" . $search['username'] ."%') or c.`username`  like ('%" . $search['username'] ."%')) ";
                    $where .= "and  c.`qc_code` = qc.`qc_code_name`";

                }
            }else{
                if($search['username'] != '' ){
                    $where .= "and  c.`mobile` like ('%" . $search['username'] ."%') or (d.doctor_name  like ('%" . $search['username'] ."%') or c.`username`  like ('%" . $search['username'] ."%')) ";
                    $where .= "and  c.`dealer_id` = '" . $search['dealer_id'] ."' ";
                    $where .= "and  j.`id` = '" . $search['dealer_id'] ."' ";
                    $where .= "and  c.`qc_code` = qc.`qc_code_name`";
                    //$where .= "and  d.`doctor_name` like ('%" . $search['username'] ."%') ";
                }
            }

            $where = trim($where,'and');
            $where = $where?' WHERE ' .$where:'' ;
        }
        return $where;
    }
    public  function  getWhere($account_id)
    {
        // if($this->session->userdata('type') == '3'){
        //$where = "and qc.`account_id` = $account_id " ;
        //$where = $where?' WHERE ' .$where:'' ;
        //return $where;
        //}else{
        $where = "qc.`account_id` = $account_id " ;
        $where = $where?' WHERE ' .$where:'' ;
        return $where;
        // }

    }
    public  function  getSs()
    {
        $where = "qc.`account_id` = ex.`id` " ;
        $where = $where?' WHERE ' .$where:'' ;
        return $where;
    }

    public function findOneById($id) {
        $sql = "
            SELECT
                   c.`id`,
                    c.`qc_code`,
                    c.`add_time`,
                    c.`status`,
                    c.`username`,
                    c.`mobile`,
                    c.`doctor_id`,
                    c.`dealer_id`,
                    d.doctor_id ,
                    d.doctor_name 
                 FROM
                     $this->table_name as c
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id  
                WHERE 
                    c.id = $id
            " ;
        return $this->queryOne($sql);
    }

    public function unbundling( $id ){
        $this->db->where('id',$id);
        return $this->db->update('user',array('doctor_id'=>''));
    }

    public function delete( $id ){
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }

}