<?php
require_once ('application/core/MY_Model.php');

class doctor_count_model extends MY_Model
{

    public $table_name = "doctor_count";

    public function findDoctorYears($doctor_id,$years)
    {
        $sql = " select
                    id,doctor_id,years,month,month,recommend,deal
                from
                    doctor_count
                where
                    doctor_id = $doctor_id
                and
                    years  = '$years'
            "  ;
        return $this->query($sql);
    }
    
    
    public function findDoctorOne($doctor_id,$years,$month)
    {
        $sql = " select 
                    id,doctor_id,years,month,month,recommend,deal 
                from 
                    doctor_count 
                where  
                    doctor_id = $doctor_id 
                and
                    years  = '$years'
                and
                    month = '$month' 
                    "  ;
        return $this->queryOne($sql);
    }
    
    
    
    //设置成交数
    public function saveDeal($doctor_id,$years,$month)
    {
        if($result = $this->findDoctorOne($doctor_id,$years,$month)){
            return $this->update($result['id'],'0',1);
        }else{
            return $this->insert($doctor_id,$years,$month,'0',1);
        }
    }
    
    //设置推荐数
    public function saveRecom($doctor_id,$years,$month)
    {
        if($result = $this->findDoctorOne($doctor_id,$years,$month)){
            return $this->update($result['id'],1,'0');
        }else{
            return $this->insert($doctor_id,$years,$month,1,'0');
        }
    }
    
    public function update($id,$recommend='0',$deal='0')
    {
        $sql = "  update  
                    $this->table_name 
                set
                    `recommend` = 'recommend+'.$recommend,
                    `deal` = 'deal+'.$deal
                where
                    id = $id
            ";
        return $this->db->query($sql);
    }
    
    public function insert($doctor_id,$years,$month,$recommend='0',$deal='0')
    {
       return $this->db->insert(
            $this->table_name,
            array(
                'doctor_id'=>$doctor_id,
                'years'=>$years,
                'month'=>$month,
                'recommend'=>'recommend+'.$recommend,
                'deal'=>'deal+'.$deal,
            )
        );
    }
}