<?php
require_once ('application/core/MY_Model.php');

class doctor_model extends MY_Model
{

    public $table_name = "doctor";
    public function findAll_admin($search,$page,$per_page){
        $sql = "
                SELECT
                    doc.`id`,
                    doc.`dealer_id`,
                    doc.`qc_code`,
                    doc.`add_time`,
                    i.`doctor_id`,
                    i.`position`,
                    i.`doctor_name`,
                    i.`position`,
                    i.`department`,
                    i.`hospital`,
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
                     LEFT JOIN 
                 `qc_code` as qc
                 ON 
                 doc.qc_code = qc.qc_code_name        
            " .  $this->getSearch($search);
        $sql .= " order by  doc.`add_time` DESC ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        if( $result ){
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum($v['id'],$v['qc_code']);
            }
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum_js($v['id'],$v['dealer_id']);
            }
            //总结算数量
            foreach( $result as $k=>$v ){
                $result[$k] += $this->js_Count($v['qc_code']);
            }
        }
        return $result;
    }
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    doc.`id`,
                    doc.`dealer_id`,
                    doc.`qc_code`,
                    doc.`add_time`,
                    i.`doctor_id`,
                    i.`doctor_name`,
                    i.`position`,
                    ex.`account_name`,
                    de.`dealer_name`,
                    de.`id` as dealer_id
                FROM
                    `doctor` as doc
                LEFT JOIN
                    `doctor_info` as i
                ON
                    doc.`id` = i.`doctor_id`
                LEFT JOIN
                    `dealer` as de
                ON
                    doc.`dealer_id` = de.`id`
                    LEFT JOIN 
                     `qc_code` as qc
                     ON 
                    doc.`dealer_id` = qc.`dealer_id`
                    AND 
                    de.id = qc.dealer_id
                LEFT JOIN
                    `ex_account` as ex
                ON
                    ex.`dealer_id` = de.`id`
                AND 
                      qc.`account_id` = ex.`id`
            " .  $this->getSearch($search);
        $sql .= " order by  doc.`add_time` DESC ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        if( $result ){
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum($v['id'],$v['qc_code']);
            }
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum_js($v['id'],$v['dealer_id']);
            }
            //总结算数量
            foreach( $result as $k=>$v ){
                $result[$k] += $this->js_Count($v['qc_code']);
            }
        }
        return $result;
    }
    //qc_code ->account_id
    public function findAll_qc($search,$page,$per_page,$account_id){
        $sql = "
                SELECT
                    doc.`id`,
                    doc.`dealer_id`,
                    doc.`qc_code`,
                    doc.`add_time`,
                    i.`doctor_id`,
                    i.`doctor_name`,
                    i.`position`,
                    ex.`account_name`,
                    de.`dealer_name`,
                    de.`id` as dealer_id
                FROM
                    `doctor` as doc
                LEFT JOIN
                    `doctor_info` as i
                ON
                    doc.`id` = i.`doctor_id`
                LEFT JOIN
                    `dealer` as de
                ON
                    doc.`dealer_id` = de.`id`
                LEFT JOIN
                    `qc_code` as qc
                ON
                    de.`id` = qc.`dealer_id`
                AND
                    doc.`qc_code` = qc.`qc_code_name` 
                    LEFT JOIN 
                    ex_account as ex
                    ON 
                    ex.id = qc.account_id
            " . $this->getWhere($account_id) . $this->getSearch($search);
        $sql .= " order by  doc.`add_time` DESC ";
        $sql .= $this->getLimitStr($page, $per_page);
        $result = $this->query($sql);
        if( $result ){
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum($v['id'],$v['qc_code']);
            }
            foreach( $result as $k=>$v ){
                $result[$k] += $this->getNum_js($v['id'],$v['dealer_id']);
            }
            //总结算数量
            foreach( $result as $k=>$v ){
                $result[$k] += $this->js_Count($v['qc_code']);
            }
        }
        return $result;
    }
    public function count($search){
        $sql = "
                SELECT 
                    count(doc.`id`) as total,
                    doc.`id`,
                    doc.`dealer_id`,
                    doc.`qc_code`,
                    i.`doctor_id`,
                    i.`doctor_name`,
                    i.`position`,
                    i.`department`,
                    i.`hospital`,
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
                    LEFT JOIN 
                    `qc_code` as qc
                    ON 
                    qc.dealer_id = de.id
                     LEFT JOIN 
                    `ex_account` as ex
                    ON 
                    ex.id = qc.account_id
            " .  $this->getSearch($search); 
        return $this->queryOne($sql);
    }
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($this->session->userdata('type') == '0' || $this->session->userdata('type') == '1'){
                if($search['dealer_id'] != '' ){
                    $where .= "and  doc.`dealer_id` = '" . $search['dealer_id']."'"  ;
                    $where .= " and   qc.`qc_code_name` = doc.`qc_code`";
                }
            }
            if($search['position'] != '' ){
                $where .= "and  i.`position` = '" . $search['position'] ."' ";
            }
            if($this->session->userdata('type') == '3') {
                if ($search['doctor_name'] != '') {
                    $where .= "and  i.`doctor_name` like ('%" . $search['doctor_name'] . "%') ";
                    return $where;
                }
            }else{
                if ($search['doctor_name'] != '') {
                    $where .= "and  i.`doctor_name` like ('%" . $search['doctor_name'] . "%') ";
                }
            }
            if($this->session->userdata('type') == '3') {
                if($search['start_time'] != '' ){
                    $where .= "and i.`add_time` >= '".$search['start_time'] ." 0:00:00 ' " ;
                    return $where;
                }
            }else{
                if($search['start_time'] != '' ){
                    $where .= "and i.`add_time` >= '".$search['start_time'] ." 0:00:00 ' " ;
                }
            }
            if($this->session->userdata('type') == '3') {
                if($search['end_time'] != '' ){
                    $where .= "and i.`add_time` <= '".$search['end_time'] ." 23:59:59 ' " ;
                    return $where;
                }
            }else{
                if($search['end_time'] != '' ){
                    $where .= "and i.`add_time` <= '".$search['end_time'] ." 23:59:59 ' " ;
                }
            }

            if($this->session->userdata('type') == '0'){
                if($search['account_id'] != '' ){
                    $where .= "and  ex.`id` = '" . $search['account_id']."'"  ;
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
    
    public function getNum($id,$qc_code) {
        //$sql = " select id,doctor_id,sum(recommend) as recommend , sum(deal) as deal from doctor_count where doctor_id=$id";
        //$result = $this->queryOne($sql );
        //推荐人数
        $res = $this->db->query("select * from customer where qc_code='$qc_code' and status_c !=3")->result_array();
        $num = count($res);
        //兑换券使用数量
        $res_m = $this->db->query("select * from coupon where doctor_id = '$id' and status_m = 2 and status !=3")->result_array();
        $num_m= count($res_m);
        $data['recommend'] = $num;
        $data['deal_m'] = $num_m;
        return $data;
    }
    /*
     * 结算条件查询
     * 2018-07-19
     */
    public function getNum_js($id,$dealer_id) {
        $t = time();
        $res = $this->db->query("SELECT * FROM customer 
                                  LEFT JOIN coupon
                                  ON 
                                  customer.doctor_id = coupon.doctor_id
                                  AND 
                                  customer.open_id = coupon.open_id
                              WHERE 
                              customer.dealer_id='$dealer_id' AND customer.doctor_id = '$id' AND customer.status_c !=3
                              AND (coupon.status_m = 2 OR (coupon.overtime < $t AND coupon.status_m = 1))")->result_array();
        $num = count($res);
        //可以结算数量
        $data['recommend_js'] = $num;
        return $data;
    }
    //已经结算数量
    public function js_Count($qc_code)
    {
        $res = $this->db->query("SELECT * FROM customer 
                                 
                              WHERE 
                              customer.qc_code='$qc_code' AND customer.status_c = 3")->result_array();
        $num = count($res);
        //可以结算数量
        $data['js_count'] = $num;
        return $data;

    }
    public function getNum_doctor($id) {
        //$sql = " select id,doctor_id,sum(recommend) as recommend , sum(deal) as deal from doctor_count where doctor_id=$id";
        //$result = $this->queryOne($sql );
        //推荐人数
        $res = $this->db->query("select * from customer where doctor_id = '$id' and status_c !=3")->result_array();
        $num = count($res);
        //兑换券使用数量
        $res_m = $this->db->query("select * from coupon where doctor_id = '$id' and status_m = 2 and status !=3")->result_array();
        $num_m= count($res_m);
        $data['recommend'] = $num;
        $data['deal_m'] = $num_m;
        return $data;
    }
    
    public function findOneById($id) {
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
            " ;
        $result = $this->queryOne($sql);
        return $result += $this->getNum_doctor($id);
    }
    
    public function update($id,$array){
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$array);
    }
    
    public function insert($data){
        
        $re = $this->db->insert(
             $this->table_name,
             array(
                 'qc_code'=>$data['qc_code_name'],
                 'add_time'=>date('Y-m-d H:i:s'),
                 'dealer_id'=>$data['dealer_id'],
             )
        );
        if( $re ){
            $re = $this->db->insert(
                'doctor_info',
                array(
                    'doctor_id'=>$this->db->insert_id(),
                    'doctor_name'=>$data['doctor_name'],
                    'position'=>$data['position'],
                    'department'=>$data['department'],
                    'hospital'=>$data['hospital'],
                    'add_time'=>date('Y-m-d H:i:s'),
                )
            );
            if(!$re){
                $this->db->where('id',$this->db->insert_id());
                $this->db->delete($this->table_name);
            }
        }
        return $re;
    }
    
    public function unbundling($doctor_id,$qc_code,$dealer_id){
            $this->db->trans_start();
            //删除医师表中数据
            $this->db->where('id',$doctor_id);
            $re1 = $this->db->delete($this->table_name);
//             echo $this->db->last_query();
//             echo "<br />";
            //删除医师详情表中数据
            $this->db->where('doctor_id',$doctor_id);
            $re2 = $this->db->delete('doctor_info');
//             echo $this->db->last_query();
//             echo "<br />";
            //删除客户表中数据
            $this->db->where('doctor_id',$doctor_id);
            $re3 = $this->db->delete('customer');
//             echo $this->db->last_query();
//             echo "<br />";
            //更新二维码数据
            
            $qcInfo = $this->queryOne("select id,qc_code_name,dealer_id from qc_code where qc_code_name='".$qc_code."'");
            if($dealer_id==''){
                $dealer_id = $qcInfo['dealer_id'];
            }
            $this->db->where('qc_code_name',$qc_code);
            $re4 = $this->db->update('qc_code',array('status'=>$dealer_id?'2':'1','code'=>''));
//             echo $this->db->last_query();
//             echo "<br />";
            if($re1&$re2&$re3&$re4){
                $this->db->trans_commit();
                return true;
            }
            $this->db->trans_rollback();
            return false;
    }
}





