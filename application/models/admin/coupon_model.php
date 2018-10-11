<?php
require_once ('application/core/MY_Model.php');

class coupon_model extends MY_Model
{

    public $table_name = "coupon";
    //优惠券查询
    public function getData(){
        $this->db->select('coupon.id,coupon.con_code_s,coupon.con_code_m,coupon.overtime,coupon.addtime,coupon.status_s,coupon.status_m,doctor_info.doctor_name,dealer.dealer_name,customer.username,customer.mobile
        ');
        $this->db->from('coupon');
        $this->db->join('customer', 'customer.open_id = coupon.open_id');
        $this->db->join('doctor_info', 'doctor_info.doctor_id = coupon.doctor_id');
        $this->db->join('dealer', 'dealer.id = coupon.dealer_id')->order_by('coupon.id','DESC');
        $query = $this->db->get();
        return $query;
    }
    //经销商优惠券查询
    public function getDatas($dealer_id){
        $this->db->select('coupon.id,coupon.con_code_s,coupon.con_code_m,coupon.overtime,coupon.addtime,coupon.status_s,coupon.status_m,doctor_info.doctor_name,dealer.dealer_name,customer.username,customer.mobile
        ');
        $this->db->from('coupon');
        $this->db->join('customer', 'customer.open_id = coupon.open_id');
        $this->db->join('doctor_info', 'doctor_info.doctor_id = coupon.doctor_id');
        $this->db->join('dealer', 'dealer.id = coupon.dealer_id');
        $this->db->where( "coupon.dealer_id =  $dealer_id");
        $this->db->order_by('coupon.id','DESC');
        $query = $this->db->get();
        return $query;
    }


    public function findAllByName(){
        $sql = "
                SELECT 
                    `id`,
                    `dealer_name`
                FROM 
                    `$this->table_name` 
        ";
        return $this->query($sql);
    }

    public function getDealerInfo($array){
        $where = '';
        if(is_array($array)){
            foreach( $array as $k=>$v ){
                $where .= "and $k='$v' ";
            }
            $where = trim($where,'and');
        }

        $sql = "
                SELECT 
                    `id`,
                    `dealer_name`,
                    `dealer_email`,
                    `area_id`,
                    `agent_area`,
                    `dealer_person`,
                    `project_person`,
                    `project_mobile`,
                    `dealer_tell`,
                    `dealer_fax`,
                    `dealer_address`,
                    `term`,
                    `contract`,
                    `add_time`,
                    `admin_id`
                 FROM 
                    `$this->table_name`                                                                                                                         
                 WHERE   
                    $where
            ";
        return $this->queryOne($sql);
    }

    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['area_id'] != '' ){
                $where .= "and  a.`area_id` = '" . $search['area_id']."' "  ;
            }
            if($search['contract'] != '' ){
                $where .= "and  a.`contract` = '" . $search['contract'] ."' ";
            }
            if($search['dealer_name'] != '' ){
                $where .= "and  a.`dealer_name` like ('%" . $search['dealer_name'] ."%') or  a.agent_area like ('%" . $search['dealer_name'] ."%') ";
            }
            $where = trim($where,'and');
            $where = $where?' HAVING ' .$where:'' ;
        }
        return $where;
    }


    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    count(a.`id`) as total,
                    a.`id`,
                    a.`con_code`,
                    a.`value`,
                    a.`overtime`,
                    a.`addtime`,
                    a.`status`,
                    
                    dea.`id` as dealer_id,
                    dea.`dealer_name` ,
                    
                    doc.`doctor_name` ,
                    doc.`doctor_hospital` ,
                   
            
                FROM
                    `coupon` as a
                LEFT JOIN
                    `dealer` as dea
                ON
                    a.`dealer_id` = dea.`id`
                 LEFT JOIN
                      `doctor_info` as doc
                ON
                    a.`doctor_id` = doc.`doctor_id`
            "  .
            $this->getSearch($search) ;
        $sql .= " order by  a.`addtime` desc";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        if( $result ){
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum($v['id']);
            }
        }
        return $result;
    }


    public function count($search){
        $sql = "
                SELECT
                    count(a.`id`) as total,
                    a.`id`,
                    a.`con_code`,
                    a.`value`,
                    a.`overtime`,
                    a.`addtime`,
                    a.`status`
                FROM
                 `coupon` as a
                " .  $this->getSearch($search);
        return $this->queryOne($sql);
    }

    public function getNum($id) {
        $qc_code = $this->queryOne(" select count(id) as total , dealer_id from qc_code where  dealer_id = $id" );
        $doctor = $this->queryOne(" select count(id) as total , dealer_id from doctor where dealer_id = $id " );
        $user = $this->queryOne(" select count(id) as total , dealer_id from customer where dealer_id = $id " );
        $data['qc_total'] = $qc_code['total'];
        $data['doctor_total'] = $doctor['total'];
        $data['user_total'] = $user['total'];
        return $data;
    }

    public function findOneById($id) {
        $sql = "
                SELECT
                    de.`id`,
                    de.`dealer_name`,
                    de.`dealer_email`,
                    de.`area_id`,
                    de.`agent_area`,
                    de.`dealer_tell`,
                    de.`dealer_person`,
                    de.`project_person`,
                    de.`project_mobile`,
                    de.`dealer_fax`,
                    de.`dealer_address`,
                    de.`term`,
                    de.`contract`,
                    de.`add_time`,
                    de.`admin_id`,
                    
                    la.`id` as area_id,
                    la.`area_person` ,
                    la.`area_name` 
            
                FROM
                    `dealer` as de
                LEFT JOIN
                    `large_area` as la
                ON
                    de.`area_id` = la.`id`
            WHERE
                de.`id`=$id
            " ;
        $result = $this->queryOne($sql);
        return $result +=$this->getNum($id);
    }

    public function insert($data) {
        if($this->db->insert($this->table_name,$data)){
            return $this->db->insert_id();
        }
        return false;
    }

    public function update($id,$array){
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$array);
    }

    // 删除
    public function delete($id){
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }
}