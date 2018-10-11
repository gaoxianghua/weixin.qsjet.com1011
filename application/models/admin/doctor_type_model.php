<?php
require_once ('application/core/MY_Model.php');

class doctor_type_model extends MY_Model
{

    public $table_name = "doctor_type";
    
    public function findAll(){
        $sql = "
                SELECT 
                    `id`,
                    `name`    
                FROM
                    `doctor_type`
                
            " ;
        return $this->query($sql);
    }
    
}





