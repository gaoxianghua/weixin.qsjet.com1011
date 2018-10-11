<?php
require_once ('application/libraries/zwyl/model_base.php');

class zwyl_form extends model_base
{

    public $table_name;

    function __construct($params = '')
    {
        parent::__construct('zwyl/base_model');
        if ($params && isset($params['table_name']))
            $this->initTable($params['table_name']);
    }

    public function initTable($table_name)
    {
        $this->table_name = $table_name;
        $this->base_model->init($table_name);
    }

    /*
     * (non-PHPdoc)
     * @see base_inter::callBefore()
     */
    public function callBefore()
    {
        // TODO Auto-generated method stub
        if (! $this->table_name)
            throw new Exception('必须先初始化table 调用initTable()方法');
        $this->base_model->init($this->table_name);
    }

    public function create($data)
    {
        $this->callBefore();
        $fields = $this->base_model->listFields();
        foreach (array_keys($data) as $key) {
            if (! in_array($key, $fields)) {
                // log_msg($key);
                unset($data[$key]);
            }
        }
        return $this->base_model->save($data);
    }

    public function update($id, $data)
    {
        $this->callBefore();
        $fields = $this->base_model->listFields();
        foreach (array_keys($data) as $key) {
            if (! in_array($key, $fields)) {
                unset($data[$key]);
            }
        }
        return $this->base_model->update(array(
            'id' => $id
        ), $data);
    }

    public function updateBatch($data, $key)
    {
        $this->callBefore();
        return $this->base_model->updateBatch($data, $key);
    }

    public function save($where, $data)
    {
        $this->callBefore();
        $fields = $this->base_model->listFields();
        foreach (array_keys($data) as $key) {
            if (! in_array($key, $fields)) {
                // log_msg($key);
                unset($data[$key]);
            }
        }
        return $this->base_model->saveOrUpdate($where, $data);
    }

    public function saveBatch($data)
    {
        $this->callBefore();
        return $this->base_model->saveBatch($data);
    }

    public function getById($id)
    {
        $this->callBefore();
        return $this->base_model->findOne(array(
            'id' => $id
        ));
    }

    public function get($where, $select = '', $order_by = '')
    {
        $this->callBefore();
        return $this->base_model->findOne($where, $select, $order_by);
    }

    public function getAll($where, $group = "", $order_by = "", $select = "")
    {
        $this->callBefore();
        return $this->base_model->find($where, $group, $order_by, array(), $select);
    }

    public function getList($where = array(), $group = "", $order_by = "", $limit = array(), $select = "")
    {
        $this->callBefore();
        return $this->base_model->find($where, $group, $order_by, $limit, $select);
    }

    public function delete($id)
    {
        $this->callBefore();
        return $this->base_model->delete($id);
    }

    public function deleteByParams($params)
    {
        $this->callBefore();
        return $this->base_model->deleteByParams($params);
    }

    public function getByParentId($parent_id = 0)
    {
        $this->callBefore();
        return $this->base_model->findByParentId($parent_id);
    }

    public function sum($where = array(), $sum_field, $group = "")
    {
        $this->callBefore();
        return $this->base_model->sum($where, $sum_field, $group);
    }

    public function count($where = array(), $group = "", $select = "")
    {
        $this->callBefore();
        return $this->base_model->count($where, $group, $select);
    }
}

?>