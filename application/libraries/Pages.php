<?php
require_once ('application/libraries/Base.php');

class Pages extends Base
{

    public $lastPage = '';

    public $nextPage = '';

    public $firstPage = '';

    public $endPage = '';

    /*
     * 分页
     * @Version: 0.0.1 alpha
     * @Created: 11:06:48 2010/11/23
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ci = &get_instance();
        $this->library('result_lib');
    }

    public function createPage($url, $count, $page_size = '', $page, $type)
    {
        // 总页数
        $pageSum = ceil($count / $page_size);
        // 偏移量
        $offest = ($page - 1) * $page_size;
        $str['page'] = $this->pageCode($url, $pageSum, $page, $type);
        $str['offest'] = $offest;
        return $str;
    }

    public function pageCode($url, $pageSum, $page, $type)
    {
        if ($pageSum <= 1) {
            return null;
        }
        
        $this->lastPage = $page - 1 > 0 ? $page - 1 : 1;
        $this->nextPage = $page + 1 < $pageSum ? $page + 1 : $pageSum;
        
        $firstUrl = $url . '?type=' . $type; // 首页
        $endUrl = $url . '?page=' . $pageSum . '&&type=' . $type; // 尾页
        $lastPage = $url . '?page=' . $this->lastPage . '&&type=' . $type; // 上一页
        $nextPage = $url . '?page=' . $this->nextPage . '&&type=' . $type; // 下一页
        
        $str = "<a href='" . $firstUrl . "'>首页</a>" . '&nbsp;&nbsp;&nbsp;';
        $str .= "<a href='" . $lastPage . "'>上一页</a>" . '&nbsp;&nbsp;&nbsp;';
        $str .= "<a href='" . $nextPage . "'>下一页</a>" . '&nbsp;&nbsp;&nbsp;';
        $str .= "<a href='" . $endUrl . "'>尾页</a>" . '&nbsp;&nbsp;&nbsp;';
        return $str;
    }
}