<?php
require_once ('application/core/MY_Model.php');

class admin_permisssions_model extends MY_Model
{

    public $table_name = "admin_permisssions";
    
    
    
    public function findOneById($id) {
    }
    public function findOneByDealer() {
        $sql = "  
                    SELECT id,name,codename,url,parent_id FROM auth_permission where codename='doctor' or codename='customer' or codename='qc_code'
            "  ;
        RETURN $this->query($sql);
    }
}