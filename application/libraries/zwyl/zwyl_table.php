<?php
require_once ('application/libraries/zwyl/Zwyl_base.php');

class zwyl_table extends Zwyl_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function createPagination($url, $count, $per_page = 10, $uri_segment = 4)
    {
        $this->library('pagination');
        $config = array();
        $config['base_url'] = $url;
        $config['suffix'] = '?' . $this->input->server('QUERY_STRING');
        $config['use_page_numbers'] = true;
        $config['uri_segment'] = $uri_segment;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
        $config['per_page'] = $per_page;
        $config['total_rows'] = $count;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['display_pages'] = true;
        // $config['page_query_string'] = true;
        $config['num_tag_open'] = "<li>";
        $config['num_tag_close'] = "</li>";
        $config['next_link'] = "&raquo;";
        $config['prev_link'] = "&laquo;";
        $config['full_tag_open'] = "<nav><ul class='pagination'>";
        $config['full_tag_close'] = "</ul></nav>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tag_close'] = "</li>";
        $config['cur_tag_open'] = "<li class='active'><a href='javascript:void(0);'>";
        $config['cur_tag_close'] = "</a></li>";
        $this->pagination->initialize($config);
    }

    public function createTable($titles, $datas)
    {
        $this->library('table');
        $tmpl = array(
            'table_open' => '<table class="table table-bordered table-striped">',
            
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            
            'table_close' => '</table>'
        );
        
        $this->table->set_template($tmpl);
        
        $this->table->set_heading($titles);
        foreach ($datas as $data) {
            $this->table->add_row($data);
        }
        return $this->table->generate();
    }

    public function createOperateButton($buttons)
    {
        $html = '';
        if ($buttons) {
            $html = '<div class="btn-group" role="group" aria-label="...">';
            foreach ($buttons as $button) {
                $html .= '<button type="button" class="btn ' . ($button['class'] ? $button['class'] : 'btn-default') . '" 
								onclick="javascript:' . (isset($button['onclick']) ? $button['onclick'] : 'operate') . '(\'' . $button['url'] . '\')" >' . $button['text'] . '</button>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}

?>