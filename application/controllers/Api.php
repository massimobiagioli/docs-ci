<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
    
    public function rest_router($action) {
        call_user_func([$this, $action]);
    }
    
    private function create_index() {
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode('create_index' . ' - ' . $_SERVER['REQUEST_METHOD']));
    }
    
    private function delete_index() {
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode('delete_index' . ' - ' . $_SERVER['REQUEST_METHOD']));
    }
    
    private function index_document() {
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode('index_document' . ' - ' . $_SERVER['REQUEST_METHOD']));
    }
    
}
