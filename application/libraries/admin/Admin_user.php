<?php
require_once ('application/libraries/Base.php');

/*
 * 管理端 - 账号管理
 * @Version: 0.0.1 alpha
 * @Created: 2016/05/10 12:00:00
 */
class Admin_user extends Base
{

    const REG_NAME = "/^[\s\x{4e00}-\x{9fa5}A-Za-z0-9]{2,30}+$/u";

    const REG_MOBILE = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|189[0-9]{8}|147[0-9]{8}|17[07]{1}[0-9]{8}$/";

    public function __construct()
    {
        parent::__construct();
        $this->model('admin/admin_user_model');
        $this->ci = &get_instance();
    }

    /*
     * 管理端登录
     * liting
     * 2016/05/10 12:00:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkLogin()
    {
        if ($adminInfo = $this->admin_user_model->checkLogin($this->input->get())) {
            // 查询此用户权限
            $result = $this->admin_user_model->getPermissions($adminInfo['admin_id']);
            $adminInfo['permissions'] = $this->handlPermissions($result);
            $adminInfo['dealer_id'] = '';
            if ($adminInfo['type'] == '0') {
                $dealer_info = $this->getDealer($adminInfo['admin_id']);
                $adminInfo['dealer_id'] = $dealer_info['id'];
            }
            //判断兑换账号是否存在，如果兑换账号删除则子账号不能登录
            if ($adminInfo['type'] == '3') {
                $admin_id = $adminInfo['admin_id'];
                $query = $this->admin_user_model->checkEx($admin_id);
                if(empty($query)){
                    return $this->result_lib->setErrorsJson('此账户失效!');exit;
                }else{
                    $account =  $adminInfo['account'];
                    $query_account = $this->admin_user_model->checkAccount($account);
                    $dealer_id = $query_account[0]['dealer_id'];
                    //$this->session->set_userdata('dealer_id',$dealer_id);
                    $_SESSION['dealer_id'] = $dealer_id;
                }
            }
            $this->ci->session->set_userdata($adminInfo);
            // 设置此用户的登录信息
            $this->handlLogin($adminInfo['admin_id']);
            return $this->result_lib->setInfoJson($adminInfo);

            exit();
        }

        exit();
    }

    /*
     * 经销商登录信息查询
     * liting
     * 2016/05/10 15:30:00
     *
     */
    public function getDealer($admin_id)
    {
        $this->library('admin/admin_dealer');
        return $this->admin_dealer->getDealerInfo(array(
            'admin_id' => $admin_id
        ));
    }

    /*
     * 检测用户信息
     * liting
     * 2016/05/10 12:00:00
     * 传入参数：
     *
     */
    public function checkData()
    {
        if (! $this->input->get('account') || empty($this->input->get('account'))) {
            return false;
        }
        if (! $this->input->get('password') || empty($this->input->get('password'))) {
            return false;
        }
        return true;
    }

    /*
     * 权限处理
     * liting
     * 2016/05/10 12:00:00
     * 传入参数：
     *
     */
    public function handlPermissions($permissions)
    {
        $array[] = '';
        if (! empty($permissions)) {
            foreach ($permissions as $k => $v) {
                $array[] = $v['permissions_id'];
            }
            return $array;
        }
        return '';
    }

    /*
     * 管理员登录信心处理
     * liting
     * 2016/05/10 14:00:00
     * 传入参数：
     *
     */
    public function handlLogin($admin_id)
    {
        $array = array(
            'is_login' => 1,
            'login_ip' => $_SERVER["REMOTE_ADDR"],
            'last_time' => date('Y-m-d h:i:s')
        );
        $this->admin_user_model->update($admin_id, $array);
    }

    /*
     * 判断管理员身份
     * liting
     * 2016/05/10 14:00:00
     * 传入参数：
     *
     */
    public function checkType()
    {
        $this->model('admin/qc_code_model');
        $this->qc_code_model->findAllByDealer($this->ci->session->userdata('admin_id'));
    }

    /*
     * 管理员添加
     * liting
     * 2016/05/11 16:30:00
     * 传入参数：
     *
     */
    public function insertDealer($data)
    {
        $data['account'] = isset($data['account']) ? $data['account'] : $data['dealer_email'];
        $data['admin_name'] = isset($data['admin_name']) ? $data['admin_name'] : $data['dealer_name'];
        
        $this->model('admin/admin_user_model');
        if ($this->checkInsertDealer($data['account'])) {
            return $this->result_lib->setErrors('邮箱账号已存在');
            exit();
        }
        $array = $this->createInfo($data);
        $tbname = 'admin_user';
        $insert_id = $this->admin_user_model->insert($tbname,$array);
        //$insert_id = $this->db->insert('admin_user',$array);
        return $this->result_lib->setInfo($insert_id);
    }

    /*
     * 管理员权限增加
     * liting
     * 2016/05/11 18:30:00
     * 传入参数：
     *
     */
    public function insertDealerPermisssions($id)
    {
        //$arr = '';
        $this->model('admin/admin_permisssions_model');
        $result = $this->admin_permisssions_model->findOneByDealer();
        foreach ($result as $k => $v) {
            $arr[] = $v['id'];
        }
        $array = array(
            'permissions_id' => $arr,
            'admin_id' => $id
        );
        $re = $this->admin_user_model->insertDealerPermisssions($array);
        if (is_array($re) && in_array(false, $re)) {
            return false;
        }
        return true;
    }

    /*
     * 管理员权限增加
     * liting
     * 2016/05/12 12:30:00
     * 传入参数：
     *
     */
    public function insertPermisssions($id, $data = '')
    {
        if (empty($data)) {
            $array = array(
                'permissions_id' => array(
                    1,
                    2,
                    4,
                    10,
                    13,
                    14
                ),
                'admin_id' => $id
            );
        } else {
            $array = array(
                'permissions_id' => $data,
                'admin_id' => $id
            );
        }
       // var_dump($array);die;
        $re = $this->admin_user_model->insertDealerPermisssions($array);
        if (is_array($re) && in_array(false, $re)) {
            return false;
        }
        return true;
    }

    /*
     * 管理员修改
     * liting
     * 2016/05/11 18:00:00
     * 传入参数：
     *
     */
    public function updateDealer($id, $data)
    {
        $data['account'] = isset($data['account']) ? $data['account'] : $data['dealer_email'];
        $data['admin_name'] = isset($data['admin_name']) ? $data['admin_name'] : $data['dealer_name'];
        
        $this->model('admin/admin_user_model');
        if ($this->checkInsertDealer($data['account'], $id)) {
            return $this->result_lib->setErrors('邮箱账号已存在');
            exit();
        }
        $array = $this->createInfo($data);
        unset($array['add_time']);
        unset($array['last_time']);
        unset($array['password']);
        if ($this->admin_user_model->update($id, $array)) {
            return $this->result_lib->setInfo('修改成功');
            exit();
        }
        return $this->result_lib->setErrors('修改失败');
        exit();
    }

    /*
     * 管理员删除
     * liting
     * 2016/05/11 18:00:00
     * 传入参数：
     *
     */
    public function delete($id)
    {
        $this->model('admin/admin_user_model');
        return $this->admin_user_model->delete($id);
    }

    /*
     * 管理员查询
     * liting
     * 2016/05/11 16:30:00
     * 传入参数：
     *
     */
    public function checkInsertDealer($account, $id = '')
    {
        $this->model('admin/admin_user_model');
        return $this->admin_user_model->checkInsertDealer($account, $id);
    }

    /*
     * 管理员添加信息构建
     * liting
     * 2016/05/11 16:30:00
     * 传入参数：
     *
     */
    public function createInfo($data)
    {
        $array = array(
            'account' => $data['account'],
            'admin_name' => $data['admin_name'],
            'password' => md5('123456'),
            'is_login' => '0',
            'login_ip' => $_SERVER["REMOTE_ADDR"],
            'last_time' => date('Y-m-d H:i:s'),
            'type' => isset($data['type']) ? '1' : '0',
            'add_time' => date('Y-m-d H:i:s')
        );
        return $array;
    }

    /*
     * 管理员权限修改
     * liting
     * 2016/05/12 13:00:00
     * 传入参数：
     *
     */
    public function updatePermissions($id, $data)
    {
        if (! is_array($data)) {
            return $this->result_lib->setErrors('权限选择错误');
            exit();
        }
        
        $re = $this->admin_user_model->updatePermissions($id, $data);
        if (! in_array(false, $re)) {
            return true;
        }
        return false;
        ;
    }
}



