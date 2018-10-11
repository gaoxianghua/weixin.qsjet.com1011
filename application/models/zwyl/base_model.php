<?php
require_once ('application/core/MY_Model.php');

class base_model extends MY_Model
{

    public $table_name = '';

    private $fields = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function initTable($table_name)
    {
        $this->table_name = $table_name;
        parent::init($table_name);
        $this->fields = $this->listFields();
    }

    /*
     * (non-PHPdoc)
     * @see base_inter::callBefore()
     */
    private function callBefore()
    {
        // TODO Auto-generated method stub
        if (! $this->table_name)
            throw new Exception('必须先初始化table 调用initTable()方法');
    }

    public function bmSave($data)
    {
        $this->callBefore();
        $this->checkParams($data);
        if (in_array('date_recorded', $this->fields)) {
            $data['date_recorded'] = getMyDate();
        }
        return $this->save($data);
    }

    public function bmUpdate($id, $data)
    {
        $this->callBefore();
        $this->checkParams($data);
        return $this->update(array(
            'id' => $id
        ), $data);
    }

    public function bmUpdateByParam($where, $data)
    {
        $this->callBefore();
        $this->checkParams($data);
        return $this->update($where, $data);
    }

    public function bmSaveOrUpdate($where, $data)
    {
        $this->callBefore();
        $this->checkParams($data);
        return $this->saveOrUpdate($where, $data);
    }

    public function bmSaveBatch($datas)
    {
        $this->callBefore();
        return $this->saveBatch($datas);
    }

    public function bmGetById($id, $select = '')
    {
        $this->callBefore();
        return $this->findOne(array(
            'id' => $id
        ), $select);
    }

    public function bmGet($where, $select = '')
    {
        $this->callBefore();
        $result = $this->findOne($where, $select);
        if ($result) {
            $this->completeResult($result, $this->fields);
        }
        return $result;
    }

    public function bmGetAll($where, $group = "", $order_by = "", $select = "")
    {
        $this->callBefore();
        $result = $this->find($where, $group, $order_by, array(), $select);
        if ($result) {
            $this->completeResult($result, $this->fields);
        }
        return $result;
    }

    public function bmGetList($where = array(), $group = "", $order_by = "", $limit = array(), $select = "")
    {
        $this->callBefore();
        $result = $this->find($where, $group, $order_by, $limit, $select);
        if ($result) {
            $this->completeResult($result, $this->fields);
        }
        return $result;
    }

    public function bmGetListByIn($field = '', $where_in = array(), $group = "", $order_by = "", $limit = array(), $select = "")
    {
        $this->callBefore();
        $result = $this->findByIn($field, $where_in, $group, $order_by, $limit, $select);
        if ($result) {
            $this->completeResult($result);
        }
        return $result;
    }

    public function bmDelete($id)
    {
        $this->callBefore();
        return $this->delete($id);
    }

    public function bmDeleteByParams($params)
    {
        $this->callBefore();
        return $this->deleteByParams($params);
    }

    public function bmGetByParentId($parent_id = 0, $select_ids = array())
    {
        $this->callBefore();
        return $this->findByParentId($parent_id, $select_ids);
    }

    public function bmCount($where = array(), $group = "", $select = "")
    {
        $this->callBefore();
        return $this->count($where, $group, $select);
    }

    public function bmMaxValue($filed)
    {
        $this->callBefore();
        return $this->maxValue($filed);
    }

    private function completeResult(&$result)
    {
        $img_fields = array();
        foreach ($this->fields as $field) {
            if (strpos($field, 'img') !== FALSE) {
                $img_fields[] = $field;
            }
        }
        completeImgUrl($result, $img_fields);
    }

    private function checkParams(&$params)
    {
        foreach (array_keys($params) as $key) {
            if (! in_array($key, $this->fields)) {
                unset($params[$key]);
            }
        }
    }
}

?>