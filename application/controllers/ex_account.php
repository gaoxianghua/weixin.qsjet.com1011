<?php
require_once ('application/core/MY_Controller.php');

/**
 * 兑换账号
 *
 *
 */
class ex_account extends MY_Controller_Site
{
    public function __construct()
    {
        parent::__construct();
        $this->page_data['upload_base'] = $this->upload_base = $this->config->item('upload_base');
        $this->page_data['download_url'] = $this->download_url = $this->config->item('download_url');
    }
    public function index()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $query = $this->db->query("SELECT id,open_id FROM ex_account WHERE open_id = '$open_id'");
            $row = $query->row_array();
            if(!empty($row['open_id'])){
                echo "<script> window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";
            }else{
                $this->page_data['open_id'] = $open_id;
                $this->loadView('ex_login.html');
            }
        } else {
            echo '请在微信公众号打开';
        }
    }
    //兑换账号登录
    public function dologin()
    {
        $account = htmlspecialchars($this->input->post('account'));
        $pwd = htmlspecialchars($this->input->post('password'));
        $open_id = htmlspecialchars($this->input->post('open_id'));
        $password = md5($pwd);
        $query = $this->db->query("SELECT id,account_name FROM ex_account WHERE account = '$account' AND password = '$password'");
        $row = $query->row_array();
        if(!empty($row['id'])){
            $id =  $row['id'];
            $data = array('open_id'=>$open_id);
            $this->db->where('id',$id);
            $this->db->update('ex_account',$data);
            echo "<script> window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";
        } else{
           echo "<script>  alert('账号或密码错误！'); window.location.href='{$_SERVER['HTTP_REFERER']}';</script>";
        }

    }
    //页面
    public function exhtml()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $query = $this->db->query("SELECT id,open_id FROM ex_account WHERE open_id = '$open_id'");
            $row = $query->row_array();
            if(!empty($row['open_id'])){
                $this->page_data['open_id'] = $row['open_id'];
                $this->loadView('exhtml.html', $this->page_data);
            }
        }
    }
    //兑换操作1
    public function exchange()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $query = $this->db->query("SELECT id FROM ex_account WHERE open_id = '$open_id'")->row_array();
            $account_id = $query['id'];
            if(empty($account_id)){
                echo "<script>  alert('登录异常，请重新登录！'); window.location.href='{$_SERVER['HTTP_REFERER']}';</script>";exit;
            }
            $phone_con = htmlspecialchars($this->input->get('phone_con'));
            $nu = strlen($phone_con);
            if($nu == 11){
                $query = $this->db->query("  SELECT
                    c.username,
                    c.`mobile`,     
                    y.id,
                    y.con_code_s,
                    y.con_code_m,
                    y.status_s,
                    y.status_m,
                    ex.coupon_value,
                    y.overtime
                 FROM
                     customer as c
                Left Join       
                    coupon as y
                 ON 
                 c.open_id  =  y.open_id
                 AND 
                 c.doctor_id  =  y.doctor_id
                 LEFT Join       
                    ex_account as ex
                   ON
                  y.ex_account_id  =  ex.id
                 WHERE 
                c.mobile = '$phone_con'
                AND
                 ex.id = $account_id
                order by 
                `add_time` 
                desc
            ");
                $row = $query->row_array();
                if(empty($row)){
                    echo "<script>  alert('该手机号用户没有优惠信息！'); window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";exit;
                }
                $this->page_data['result'] = $row;
                $this->page_data['open_id'] = $open_id;
                $this->loadView('exchange.html', $this->page_data);
            } elseif ($nu == 10){
                $query = $this->db->query("  SELECT
                    c.username,
                    c.`mobile`,    
                    ex.coupon_value, 
                    y.id,
                    y.con_code_s,
                    y.status_s,
                    y.status_m,
                    y.overtime
                 FROM
                     customer as c
                Left Join       
                    coupon as y
                 ON 
                 c.open_id  =  y.open_id
                 AND 
                 c.doctor_id  =  y.doctor_id
                 RIGHT Join       
                    ex_account as ex
                 ON 
                 y.ex_account_id  =  ex.id
                 AND 
                  y.dealer_id  =  ex.dealer_id
                AND 
                 y.status  !=  2
                WHERE 
                 y.con_code_s = '$phone_con'
            ");
                $row = $query->row_array();
                if(!empty($row['con_code_s'])){
                    $this->page_data['result'] = $row;
                    $this->page_data['open_id'] = $open_id;
                    $this->loadView('exchange_s.html');
                } else {
                    $query = $this->db->query("  SELECT
                    c.username,
                    c.`mobile`, 
                    ex.coupon_value,    
                    y.id,
                    y.con_code_m,
                    y.status_s,
                    y.status_m,
                    y.overtime
                 FROM
                     customer as c
                Left Join       
                    coupon as y
                 ON 
                 c.open_id  =  y.open_id
                  AND 
                 c.doctor_id  =  y.doctor_id
                 LEFT Join       
                    ex_account as ex
                 ON 
                 y.ex_account_id  =  ex.id
                  AND 
                 y.status  !=  2
                WHERE 
                 y.con_code_m = '$phone_con'
                 AND
                 ex.id = $account_id
            ");
                    $row = $query->row_array();
                    if(!empty($row['con_code_m'])) {
                        $this->page_data['result'] = $row;
                        $this->page_data['open_id'] = $open_id;
                        $this->loadView('exchange_m.html', $this->page_data);
                    }else{
                       echo "<script>  alert('该码无效！'); window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";
                    }
                }
            } else{
                echo "<script>  alert('该码无效！'); window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";
            }

        }else{
            echo "请登录兑换账号";
        }
    }

    public function ex_check()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $query = $this->db->query("SELECT id,open_id FROM ex_account WHERE open_id = '$open_id'");
            $row = $query->row_array();
            if(!empty($row['open_id'])) {
                    //免费试用
                    if ($_GET['s'] == '兑换') {
                        $id = $_GET['id'];
                        $data = array('status_s' => 2,'extime_s'=>time());
                        $this->db->where('id', $id);
                        $re = $this->db->update('coupon', $data);
                        if ($re) {
                            echo "<script>  alert('兑换成功！');window.location.href = document.referrer;</script>";
                        } else {
                            echo "<script>  alert('兑换失败！');window.location.href = document.referrer;</script>";
                        }
                    }
                    //兑换耗材
                    if ($_GET['m'] == '兑换') {
                        $id = $_GET['id'];
                        $data = array('status_m' => 2,'extime_m'=>time());
                        $this->db->where('id', $id);
                        $re = $this->db->update('coupon', $data);
                        if ($re) {
                            echo "<script>  alert('兑换成功！'); window.location.href = document.referrer;</script>";
                        } else {
                            echo "<script>  alert('兑换失败！'); window.location.href = document.referrer;</script>";
                    }
                }
            }
        }else{
            echo "请登录兑换账号";
        }
    }

    //改密
    public function setpwd()
    {
        if(!empty($_GET['open_id'])) {
            $open_id = $_GET['open_id'];
            $query = $this->db->query("SELECT id,open_id FROM ex_account WHERE open_id = '$open_id'");
            $row = $query->row_array();
            if (!empty($row['open_id'])) {
                $this->page_data['open_id'] = $row['open_id'];
                $this->loadView('setpwd_ex.html', $this->page_data);
            } else {
                echo "请登录";
            }
        }
    }

    //修改密码
    public function dopwd()
    {
        if(!empty($_POST['open_id'])) {
            $open_id = $_POST['open_id'];
            $query = $this->db->query("SELECT id,open_id FROM ex_account WHERE open_id = '$open_id'");
            $row = $query->row_array();
            if (!empty($row['open_id'])) {
                $pwd = htmlspecialchars($this->input->post('password'));
                $password = md5($pwd);
                $id = $row['id'];
                $query = $this->db->query("SELECT id,account_name FROM ex_account WHERE id = $id AND password = '$password'");
                $row = $query->row_array();
                if (empty($row)) {
                    echo "<script>  alert('原密码不正确！');window.history.back();</script>";
                    exit;
                }
                $newp = htmlspecialchars($this->input->post('newpwd'));
                $newpwd = md5($newp);
                $arr['password'] = $newpwd;
                $this->db->where('id', $id);
                $re = $this->db->update('ex_account', $arr);
                if ($re) {
                    echo "<script>  alert('修改成功！');</script>";
                    echo "<script> window.location.href='/ex_account/exhtml?open_id=$open_id';</script>";
                } else {
                    echo "<script>  alert('修改失败！');window.history.back();</script>";
                    exit;
                }
            } else {
                echo "<script>  alert('请登录！'); window.location.href='/ex_account';</script>";
            }
        }
    }
    //退出
    public function loginout()
    {
        if(!empty($_GET['open_id'])){
            $open_id = $_GET['open_id'];
            $data = array('open_id'=>'');
            $this->db->where('open_id',$open_id);
            $this->db->update('ex_account',$data);
            echo "<script>window.location.href='/ex_account?open_id=$open_id';</script>";
        }
    }
}