<?php

/*
 * 无限级分类
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
class Recursive
{

    public static function getId($arr, $pid = 0)
    {
        $tree = array();
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if ($v['parent_id'] == $pid) {
                    $v['child'] = Recursive::getId($arr, $v['id']);
                    $tree[] = $v;
                }
            }
        }
        return $tree;
    }
}