<?php
header("Content-type:text/html;charset=utf-8");
require_once ('system/core/Model.php');

/**
 *
 * Enter description here ...
 *
 * @author zr
 * @since 2015年5月21日 下午12:35:46
 * @property CI_DB_query_builder $db
 */
class MY_Model extends CI_Model
{

    public $db_group = 'default';

    public $pk = 'id';

    public $table_name = '';

    const SELECT_LIST = "";

    const SELECT = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function init($table_name)
    {
        $this->table_name = $table_name;
    }

    public function initDB($db)
    {
        $this->db = $this->load->database($db, TRUE);
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $data
     *            @auth zr
     *            @time 2014-10-17 上午11:21:53
     */
    public function save($data, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($this->db->insert($table_name, $data)) {
            return $this->db->insert_id();
        } else {
            log_message('error', 'save error');
            return false;
        }
    }

    public function getPropertysString($property_name, $where_name, $where_value, $is_add_self = false, $table_name, $default_value = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $sql = "SELECT " . $property_name . " FROM " . $table_name . " WHERE " . $where_name . "={$this->db->escape($where_value)}";
        $propertys = $this->query($sql);
        $propertys_str = $this->propertysToString($propertys, $property_name, $is_add_self ? $where_value : '');
        if (empty($propertys_str)) {
            $propertys_str = $default_value;
        }
        return $propertys_str;
    }

    public function propertysToString($propertys, $property_name, $last = '')
    {
        $propertys_str = '';
        if ($propertys && is_array($propertys)) {
            foreach ($propertys as $k => $v) {
                if (empty($propertys_str)) {
                    $propertys_str .= $v[$property_name];
                } else {
                    $propertys_str .= (',' . $v[$property_name]);
                }
            }
        }
        if (! empty($last)) {
            $propertys_str .= (',' . $last);
        }
        return $propertys_str;
    }

    public function setRowNumber($page = 0, $page_size = 10)
    {
        $sql = "SET @row_number = " . (intval($page) * intval($page_size)) . ";";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getRowNumberStr()
    {
        return "(@row_number:=@row_number+1) AS row_number";
    }

    public function getLimitStr($page = 0, $page_size = 10)
    {
        return ($page_size > 0 && $page >= 0 ? " LIMIT " . (int) ($page * $page_size) . "," . (int) $page_size . "" : "");
    }

    public function saveOrUpdate($where, $data, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if (! $where || ! $this->find($where, '', '', '', '', $table_name)) {
            return $this->save($data, $table_name);
        } else {
            return $this->update($where, $data, $table_name);
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $data
     *            @auth zr
     *            @time 2014-10-17 上午11:21:36
     */
    public function saveBatch($data, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($this->db->insert_batch($table_name, $data)) {
            return true;
        } else {
            log_message('error', 'save error');
            return false;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $where            
     * @param array $data
     *            @auth zr
     *            @time 2014-10-17 上午11:20:57
     */
    public function update($where, $data, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->where($where);
        $this->db->update($table_name, $data);
        $result = $this->db->affected_rows();
        // if(!$result>=0) {
        // log_message('error', 'update error==>'.$this->db->last_query());
        // }
        return $result;
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $data            
     * @param string $key
     *            @auth zr
     *            @time 2014-10-17 上午11:20:57
     */
    public function updateBatch($data, $key, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->update_batch($table_name, $data, $key);
        $result = $this->db->affected_rows();
        // if(!$result>=0) {
        // log_message('error', 'update_batch error==>'.$this->db->last_query());
        // }
        return $result;
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $data            
     * @param array $keys
     *            @auth zr
     *            @time 2014-10-17 上午11:20:57
     */
    public function updateBatchManyKey($datas, $keys, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->trans_begin();
        foreach ($datas as $data) {
            $where = array();
            foreach ($keys as $key) {
                if (array_key_exists($key, $data)) {
                    $where[$key] = $data[$key];
                } else {
                    $this->db->trans_rollback();
                    return false;
                }
            }
            if ($where) {
                if (! $this->update($where, $data, $table_name)) {
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }
        $this->db->trans_commit();
        return true;
    }

    public function _trans_begin(){
        return $this->db->trans_begin();
    }
    
    public function _trans_rollback(){
        return $this->db->_trans_rollback();
    }
    
    public function _trans_commit(){
        return $this->db->trans_commit();
    }
    
    /**
     *
     * 根据ID删除数据
     *
     * @param int $id
     *            @auth zr
     *            @time 2014-12-30 下午08:37:00
     */
    public function delete($id, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->where('id', $id);
        $this->db->delete($table_name);
        return $this->db->affected_rows();
    }

    /**
     *
     * 根据params删除数据
     *
     * @param array $params
     *            @auth zr
     *            @time 2014-12-30 下午08:37:00
     */
    public function deleteByParams($params, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->where($params);
        $this->db->delete($table_name);
        return $this->db->affected_rows();
    }

    /**
     *
     * 清空表
     *
     * @param array $params
     *            @auth zr
     *            @time 2014-12-30 下午08:37:00
     */
    public function emptyTable($table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->empty_table($table_name);
        return $this->db->affected_rows();
    }

    public function findByPK($pk, $select = self::SELECT, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($select) {
            $this->db->select($select);
        }
        if ($id) {
            $this->db->where(array(
                $this->pk => $id
            ));
        }
        $query = $this->db->get($table_name);
        return $query->row_array();
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $where            
     * @param string $select
     *            @auth zr
     *            @time 2014-10-17 上午11:20:37
     */
    public function findById($id, $select = self::SELECT, $order_by = "", $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($select) {
            $this->db->select($select);
        }
        if ($id) {
            $this->db->where(array(
                'id' => $id
            ));
        }
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        $query = $this->db->get($table_name);
        return $query->row_array();
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $where            
     * @param string $select
     *            @auth zr
     *            @time 2014-10-17 上午11:20:37
     */
    public function findOne($where = array(), $select = self::SELECT, $order_by = "", $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($select) {
            $this->db->select($select);
        }
        $this->db->where($where);
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        $query = $this->db->get($table_name);
        return $query->row_array();
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $where            
     * @param string $group            
     * @param string $order_by            
     * @param array $limit
     *            array(0,1)
     * @param string $select
     *            @auth zr
     *            @time 2014-10-17 上午11:17:45
     */
    public function find($where = array(), $group = "", $order_by = "", $limit = array(), $select = self::SELECT_LIST, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($select) {
            $this->db->select($select);
        }
        $this->db->where($where);
        if ($group) {
            $this->db->group_by($group);
        }
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        if ($limit && is_array($limit) && sizeof($limit) == 2) {
            $page = $limit[0];
            $per_page = $limit[1];
            $this->db->limit(intval($per_page), intval($page) * intval($per_page));
        }
        $query = $this->db->get($table_name);
        // log_msg($this->db->last_query());
        return $query->result_array();
    }

    public function exists($where = '', $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        
        $query = $this->db->get_where($table_name, $where, FALSE);
        
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $where            
     * @param string $group            
     * @param string $select
     *            @auth zr
     *            @time 2014-10-17 上午11:22:09
     */
    public function count($where = array(), $group = "", $select = '', $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        if ($select) {
            $this->db->select($select);
        }
        $this->db->where($where);
        if ($group) {
            $this->db->group_by($group);
        }
        $count = $this->db->count_all_results($table_name);
        // log_info($this->db->last_query());
        return $count;
    }

    public function maxValue($filed, $table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        $this->db->select_max($filed);
        $query = $this->db->get($table_name);
        $max = $query->row_array();
        return $max ? $max[$filed] : 0;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $sql
     *            @auth zr
     *            @time 2014-10-17 上午11:22:24
     */
    public function query($sql)
    {
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $sql
     *            @auth zr
     *            @time 2014-10-17 上午11:22:24
     */
    public function queryOne($sql)
    {
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }

    public function listFields($table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        return $this->db->list_fields($table_name);
    }

    public function fieldData($table_name = '')
    {
        $table_name = $table_name ? $table_name : $this->table_name;
        return $this->db->field_data($table_name);
    }

    public function findByParentId($parent_id = 0, $select_ids = array(), $level = 0)
    {
        $level ++;
        $datas = $this->find(array(
            'parent_id' => $parent_id
        ));
        if ($datas) {
            for ($i = 0; $i < sizeof($datas); $i ++) {
                if (in_array($datas[$i]['id'], $select_ids)) {
                    $datas[$i]['selected'] = '1';
                } else {
                    $datas[$i]['selected'] = '0';
                }
                $datas[$i]['level'] = $level;
                $child_datas = $this->findByParentId($datas[$i]['id'], array(), $level);
                if ($child_datas)
                    $datas[$i]['child'] = $child_datas;
            }
        }
        return $datas;
    }
}

?>