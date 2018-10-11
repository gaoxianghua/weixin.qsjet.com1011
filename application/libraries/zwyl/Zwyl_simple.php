<?php
require_once ('application/libraries/zwyl/Zwyl_base.php');

class Zwyl_simple extends Zwyl_base
{

    public $table_name;

    public $model = 'zwyl/base_model';

    function __construct()
    {
        parent::__construct();
        $model_name = $this->model;
        $this->load->model($model_name);
        $arr = explode('/', $model_name);
        $model_name = end($arr);
        $this->$model_name = $this->ci->$model_name;
        $this->model_name = $model_name;
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
    private function callBefore()
    {
        // TODO Auto-generated method stub
        if (! $this->table_name)
            throw new Exception('必须先初始化table 调用initTable()方法');
            // log_msg($this->table_name);
        $this->base_model->init($this->table_name);
    }

    public function create($data)
    {
        $this->callBefore();
        $fields = $this->base_model->listFields();
        foreach (array_keys($data) as $key) {
            if (! in_array($key, $fields)) {
                log_msg($key);
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

    public function updateByParam($where, $data)
    {
        $this->callBefore();
        $fields = $this->base_model->listFields();
        foreach (array_keys($data) as $key) {
            if (! in_array($key, $fields)) {
                unset($data[$key]);
            }
        }
        return $this->base_model->update($where, $data);
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

    public function saveBatch($datas)
    {
        $this->callBefore();
        return $this->base_model->saveBatch($datas);
    }

    public function getById($id)
    {
        $this->callBefore();
        return $this->base_model->findOne(array(
            'id' => $id
        ));
    }

    public function get($where, $select = '')
    {
        $this->callBefore();
        return $this->base_model->findOne($where, $select);
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

    public function getListByIn($field = '', $where_in = array(), $group = "", $order_by = "", $limit = array(), $select = "")
    {
        $this->callBefore();
        return $this->base_model->findByIn($field, $where_in, $group, $order_by, $limit, $select);
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

    public function getByParentId($parent_id = 0, $select_ids = array())
    {
        $this->callBefore();
        return $this->base_model->findByParentId($parent_id, $select_ids);
    }

    public function count($where = array(), $group = "", $select = "")
    {
        $this->callBefore();
        return $this->base_model->count($where, $group, $select);
    }

    public function maxValue($filed)
    {
        $this->callBefore();
        return $this->base_model->maxValue($filed);
    }

    public function increase($id, $field, $value)
    {
        $this->callBefore();
        $sql = "update {$this->table_name} set {$field} = {$field}+{$value} where id = {$id};";
        return $this->base_model->execute($sql);
    }
}

?>