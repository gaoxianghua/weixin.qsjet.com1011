<?php
require_once ('application/core/MY_Model.php');

class qc_code_model extends MY_Model
{

    public $table_name = "qc_code";
    
    public function findAllByDealer($dealer_id){
        $sql = "
                SELECT 
                    `id`,
                    `qc_code_name`,
                    `add_time`,
                    `status`,
                    `dealer_id`
                FROM 
                    `$this->table_name`
                WHERE
                    `dealer_id` = $dealer_id
            ";
        return $this->query($sql);
    }
    
    public function findAllById($id){
        $sql = "
                SELECT
                    `id`,
                    `qc_code_name`
                FROM
                    `$this->table_name`
                WHERE
                    `id` in ($id)
                ";
        return $this->query($sql);
    }
    
    public function findOneById($id){
        $sql = "
        SELECT
            `id`,
            `qc_code_name`,
            `add_time`,
            `status`,
            `dealer_id`
        FROM
            `$this->table_name`
        WHERE
            `id` = $id
        ";
        return $this->queryOne($sql);
    }
    
    public function findAll($search,$page,$per_page){
        $sql = "
                SELECT
                    qc.`id`,
                    qc.`qc_code_name`,
                    qc.`add_time`,
                    qc.`status`,
                    qc.`dealer_id`,
                    
                    de.`id` dealer_id,
                    de.`admin_id`,
                    de.`dealer_name`,
                    
                    ex.`account_name`,
                    
                    doc.`id` as doctor_id,
                    doc.`dealer_id` as doc_dealer_id ,
                    doc.`qc_code`,
                    
                    dci.`doctor_id` as dci_doctor_id,
                    dci.`doctor_name`
                FROM
                    `qc_code` as qc
                LEFT JOIN
                    `dealer` as de
                ON
                     qc.dealer_id = de.`id`
                LEFT JOIN
                    doctor as doc
                ON 
                    doc.qc_code = qc.`qc_code_name` 
                LEFT JOIN
                    doctor_info as dci
                ON 
                    doc.id = dci.`doctor_id`  
                LEFT JOIN 
                    ex_account as  ex
                ON 
                    ex.id = qc.account_id   
                " . $this->getSearch($search);
        $sql .= " order by  `add_time` desc , qc.`id` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        return $this->query($sql);
    }

    public function findAll_qc($search,$page,$per_page,$account_id){
        $sql = "
                SELECT
                    qc.`id`,
                    qc.`qc_code_name`,
                    qc.`add_time`,
                    qc.`status`,
                    qc.`dealer_id`,
                    
                    de.`id` dealer_id,
                    de.`admin_id`,
                    de.`dealer_name`,
                    
                    ex.`account_name`,
                    
                    doc.`id` as doctor_id,
                    doc.`dealer_id` as doc_dealer_id ,
                    doc.`qc_code`,
                    
                    dci.`doctor_id` as dci_doctor_id,
                    dci.`doctor_name`
                FROM
                    `qc_code` as qc
                LEFT JOIN
                    `dealer` as de
                ON
                     qc.dealer_id = de.`id`
                LEFT JOIN
                    doctor as doc
                ON 
                    doc.qc_code = qc.`qc_code_name` 
                LEFT JOIN
                    doctor_info as dci
                ON 
                    doc.id = dci.`doctor_id`
                LEFT JOIN 
                    ex_account as  ex
                ON 
                    ex.id = qc.account_id     
                WHERE 
                  qc.account_id = $account_id
                " . $this->getSearch($search);
        $sql .= " order by  `add_time` desc , qc.`id` desc ";
        $sql .= $this->getLimitStr($page, $per_page);
        return $this->query($sql);
    }
    
    public function getSearch($search) {
        $where = '';
        if( $search != '' ){
            if($search['status'] != '' ){
                $where .= "and qc.`status` = '" . $search['status']."' "  ;
            }
            if($search['dealer_id'] != '' ){
                $where .= "and qc.`dealer_id` = '" . $search['dealer_id']."' "  ;
            }
            if($search['start_time'] != '' ){
                $where .= "and qc.`add_time` >= '".$search['start_time'] ." 0:00:00 ' " ;
            }
            if($search['end_time'] != '' ){
                $where .= "and qc.`add_time` <= '".$search['end_time'] ." 23:59:59 ' " ;
            }
            $where = trim($where,'and');
            $where = $where?' WHERE ' .$where:'' ;
        }
        return $where;
    }
    
    public function count($search){
        $sql = "
                SELECT
                    count(qc.id) as total,
                    qc.`id`,
                    qc.`qc_code_name`,
                    qc.`add_time`,
                    qc.`status`,
                    qc.`dealer_id`,
                    
                    de.`id` dealer_id,
                    de.`dealer_name`,
                    de.`admin_id`,
                    
                    doc.`id` as doctor_id,
                    doc.`dealer_id` as doc_dealer_id,
                    
                    dci.`doctor_id`,
                    dci.`doctor_name`
                FROM
                    `qc_code` as qc
                LEFT JOIN
                    `dealer` as de
                ON
                     qc.dealer_id = de.`id`
                LEFT JOIN
                    doctor as doc
                ON 
                    doc.id = de.`id` 
                LEFT JOIN
                    doctor_info as dci
                ON 
                    doc.id = dci.`doctor_id`  
            " .  $this->getSearch($search);
        return $this->queryOne($sql);
    }
    
    public function update($id,$data) {
        $this->db->where('id',$id);
        return $this->db->update($this->table_name,$data);
    }
    
    public function cencel($id) {
        $result = $this->findOneById($id);
        $this->db->trans_begin();
        //修改二维码表数据
        $re1 = $this->update(
            $result['id'],
            array(
                'status'=>1,
                'dealer_id'=>'',
                'code'=>''
            )
        );
        
        
        //删除医师
        $re2 = $re4 = true;
        $doctorInfo = $this->queryOne("select id,qc_code from doctor where qc_code='".$result['qc_code_name']."'");
        if(!empty($doctorInfo)){
            $this->db->where('id',$doctorInfo['id']);
            $re4 = $this->db->delete('doctor');
            
            //删除医师信息
            $this->db->where('doctor_id',$doctorInfo['id']);
            $re2 = $this->db->delete('doctor_info');
        }
        
        //修改用户表
        $this->db->where('qc_code',$result['qc_code_name']);
        $re3 = $this->db->delete('customer');
        if( $re1 && $re2 && $re3 && $re4){
            $this->db->trans_commit();
            return true;
        }
        $this->db->trans_rollback();
        return false;
    }
    
    public function unbinding($id) {        //二维码的id
        $result = $this->findOneById($id);
        $this->db->trans_begin();

        //修改二维码表数据
        $re1 = $this->update(
            $result['id'],
            array(
                'status'=>2,
            )
        );
        
        //删除医师
        $doctorInfo = $this->queryOne("select id,qc_code from doctor where qc_code='".$result['qc_code_name']."'");
        $this->db->where('id',$doctorInfo['id']);
        $re1 = $this->db->delete('doctor');
        
        $this->db->where('doctor_id',$doctorInfo['id']);
        $re2 = $this->db->delete('doctor_info');
        
        
        //修改用户表
        $this->db->where('qc_code',$result['qc_code_name']);
        $re3 = $this->db->update('user',array('qc_code'=>'','doctor_id'=>''));
        if( $re1 && $re2 && $re3 ){
            $this->db->trans_commit();
            return true;
        }
        $this->db->trans_rollback();
        return false;
    }
    
    public function assign($dealer_id,$qc_code_id) {
         $this->db->where('id',$qc_code_id);
         return $this->db->update($this->table_name,array('dealer_id'=>$dealer_id,'status'=>2));
    }
    
    public function insert($array) {
        $num = '0';
        if( is_array($array)  && !empty( $array )){
            foreach(  $array as $k=>$v ){
                $re = $this->db->insert($this->table_name,array('qc_code_name'=>$v,'status'=>2,'dealer_id'=>11,'add_time'=>date('Y-m-d H:i:s')));
                if( $re ){
                    $num += 1;
                }
            }
        }
        return $num;
    }
    public function insertDealer($array,$account_id,$dealer_id) {
        $num = '1';
        $v = $array['qc_code_name'];
        //var_dump($dealer_id);die;
        $this->db->insert($this->table_name,array('qc_code_name'=>$v,'status'=>2,'dealer_id'=>$dealer_id,'account_id'=>$account_id,'add_time'=>date('Y-m-d H:i:s')));
        return $num;
    }

    public function deletecode( $id ){
        $idArr = explode(',',$id);
        $this->db->where_in('id',$idArr);
        return $this->db->delete($this->table_name);
    }
}







