<?php
require_once ('application/core/MY_Model.php');

class doctor_model extends MY_Model
{

    public $table_name = "doctor";

    public function insert($array)
    {
        if ($this->db->insert($this->table_name, $array)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function insertDetail($array)
    {
        if ($this->db->insert('doctor_info', $array)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function findOneByQc($qc_code)
    {
        $sql = "
                SELECT
                    `open_id`,
                    `status`,
                    `qc_code`,
                    `add_time`,
                    `dealer_id`,
                    `id`
                FROM
                    `$this->table_name`
                WHERE
                    `qc_code` =  '$qc_code'
            ";
        return $this->queryOne($sql);
    }

    public function findOneByOpenId($open_id)
    {
        $sql = "
        SELECT
        `open_id`,
        `status`,
        `qc_code`,
        `add_time`,
        `dealer_id`,
        `id`
        FROM
        `$this->table_name`
        WHERE
        `open_id` =  '$open_id'
        ";
        return $this->queryOne($sql);
    }

    public function findOneById($id)
    {
        $sql = "
                SELECT
                    doc.`id`,
                    doc.`dealer_id`,
                    doc.`qc_code`,
                    i.`doctor_id`,
                    i.`doctor_name`,
                    i.`add_time`,
                    de.`dealer_name`,
                    de.`id` as dealer_id
                FROM
                    `doctor` as doc
                INNER JOIN
                    `doctor_info` as i
                ON
                    doc.`id` = i.`doctor_id`
                LEFT JOIN
                    `dealer` as de
                ON
                    doc.`dealer_id` = de.`id`
                WHERE 
                    doc.`id`=$id
            ";
        $result = $this->queryOne($sql);
        return $result += $this->getNum($id);
    }
    //编号查询客户信息
    public function findAll($dealer_id,$doctor_id){
        $sql = "
                SELECT
                    c.`id`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    y.status_m,
                    y.extime_m,
                    y.overtime
                 FROM
                     `customer` as c
                Left Join 
                    `doctor_info` as d
                ON
                    c.doctor_id = d.doctor_id
                Left Join       
                    `coupon` as y
                 ON 
                 c.open_id  =  y.open_id
                 Left Join       
                    `dealer` as j
                 ON 
                 c.dealer_id  =  j.id
                 WHERE 
                 c.dealer_id = $dealer_id
                 AND 
                 c.doctor_id = $doctor_id
                 AND 
                 y.doctor_id = $doctor_id
                 AND 
                 c.status_c != 3
            " ;
        $sql .= " order by  `add_time` desc ";
        $result = $this->query($sql);
        return $result;
    }
    //客户详情
    public function findCustomer($cid){
        $sql = "
                SELECT
                    c.`id`,
                    c.`status`,
                    c.`add_time`,
                    c.`username`,
                    c.`mobile`,
                    y.status_s,
                    y.status_m,
                    y.extime_s,
                    y.extime_m,
                    y.overtime
                 FROM
                     `customer` as c
                Left Join       
                    `coupon` as y
                 ON 
                 c.open_id  =  y.open_id
                AND 
                c.doctor_id = y.doctor_id
                AND 
                c.dealer_id = y.dealer_id
                 WHERE 
                 c.id = $cid
            " ;
        $result = $this->query($sql);
        return $result;
    }

    public function getNum($id)
    {
        $sql = " select id,doctor_id,sum(recommend) as recommend , sum(deal) as deal from doctor_count where doctor_id=$id";
        $result = $this->queryOne($sql);
        $data['recommend'] = $result['recommend']?$result['recommend']:'0';
        $data['deal'] = $result['deal']?$result['deal']:'0';
        return $data;
    }

    public function update($id, $array)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_name, $array);
    }
}













