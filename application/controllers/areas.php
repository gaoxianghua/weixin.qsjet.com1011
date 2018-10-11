<?php
require_once ('application/core/MY_Controller.php');
/**
 * 
 * Enter description here ...
 * @auth zr
 * @date 2015-1-12 下午09:07:52
 * @property zwyl_form $zwyl_form
 * @property zwyl_admin $zwyl_admin
 * @property zwyl_table $zwyl_table
 * @property auth_user_permission_view $auth_user_permission_view
 */
class areas extends MY_Controller_Site {
            public function __construct(){
                    parent::__construct();
                    $this->load->library('result_lib');
                    $this->page_data['menu_flag'] = "user";
                    $this->page_data['second_menu_flag'] = "spaceuser";
                    $this->page_data['open_id'] = $this->input->get('open_id');
            }
	
            public function getList() {
                    $parent_id = $this->input->get( 'parent_id' ) ? $this->input->get( 'parent_id' ) : '1';
                    $this->load->model('areas_model');
                    $result = $this->areas_model->findByParent( $parent_id );
                    echo $this->result_lib->setInfoJson($result);
            }
            
            public function getSecondList() {

                $this->load->model('areas_model');
                $result = $this->areas_model->findBySecond(  );
                $result = $this->result_lib->setInfo($result);
                echo json_encode($result);
            }
            
            public function showAreasSelect() {
                
                $this->loadView('addr_select.html');
            }
}
