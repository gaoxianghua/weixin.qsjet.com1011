<?php
require_once ('application/core/MY_Model.php');

class ex_account_model extends MY_Model
{

    public $table_name = "ex_account";
    //兑换账号查询
    public function getData(){
        $this->db->select('ex_account.id,ex_account.account,ex_account.account_name,dealer.dealer_name,ex_account.addtime');
        $this->db->from('ex_account');
        $this->db->join('dealer', 'dealer.id = ex_account.dealer_id');
        $this->db->order_by('ex_account.id','DESC');
        $query = $this->db->get();
        return $query;
    }
    //经销商兑换账号查询
    public function getDatas($dealer_id){
       $this->db->select('ex_account.id,ex_account.account,ex_account.account_name,dealer.dealer_name,ex_account.addtime');
        $this->db->from('ex_account');
        $this->db->join('dealer', 'dealer.id = ex_account.dealer_id');
        $this->db->where( "ex_account.dealer_id =  $dealer_id");
        $this->db->order_by('ex_account.id','DESC');
        $query = $this->db->get();
        return $query;
    }
    //添加
    public function add($data)
    {
        if($this->db->insert($this->table_name,$data)) {
            return $this->db->insert_id();
        }
        return false;
    }
    public function add_admin($data_admin)
    {
        if($this->db->insert('admin_user',$data_admin)) {
            $admin_id =  $this->db->insert_id();
            $data_a['admin_id'] = $admin_id;
            $data_a['permissions_id'] = 1;
            $this->db->insert('admin_permisssions',$data_a);
            $data_b['admin_id'] = $admin_id;
            $data_b['permissions_id'] = 2;
            $this->db->insert('admin_permisssions',$data_b);
            $data_c['admin_id'] = $admin_id;
            $data_c['permissions_id'] = 3;
            $this->db->insert('admin_permisssions',$data_c);
            $data_d['admin_id'] = $admin_id;
            $data_d['permissions_id'] = 4;
            $this->db->insert('admin_permisssions',$data_d);
            $data_e['admin_id'] = $admin_id;
            $data_e['permissions_id'] = 11;
            $this->db->insert('admin_permisssions',$data_e);
            $data_f['admin_id'] = $admin_id;
            $data_f['permissions_id'] = 12;
            $this->db->insert('admin_permisssions',$data_f);
            return $admin_id;
        }
        return false;
    }
   //修改查询账号信息
    public function accountInfo($eid)
    {
        $query = $this->db->select('*')->from('ex_account')->where("ex_account.id =  $eid")->get();
        return $query;
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