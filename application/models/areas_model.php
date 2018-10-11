<?php
require_once ('application/core/MY_Model.php');

class areas_model extends MY_Model
{

    public $table_name = "areas";

    public function findAll()
    {
        $sql = " select area_id,area_name,py_name,index_name,area_type from areas where area_type = 2 order by index_name asc";
        return $this->query($sql);
    }

    public function findByParent($parent_id)
    {
        $sql = " select area_id,parent_id,area_name,py_name,area_type,index_name from areas2 where parent_id = $parent_id ";
        return $this->query($sql);
    }

    public function findBySecond($p = 1)
    {
        $sql = " select area_id,parent_id,area_name,py_name,area_type,index_name from areas2 where area_type>0 and area_type<3 order by  index_name ";
        $result = $this->query($sql);
        $result = $this->recursion($result);
        return $result;
    }

    public function recursion($result, $p = 1)
    {
        $tree = array();
        foreach ($result as $k => $v) {
            if (! empty($v['parent_id']) && $v['parent_id'] == $p) {
                $v['child'] = $this->recursion($result, $v['area_id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}