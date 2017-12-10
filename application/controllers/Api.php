<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
    
    public function create_index() {
        // Check auth
        $user = $this->auth->get_user();
        if (!$user || !$this->auth->is_user_admin($user)) {
            $this->handle_missing_auth_error();
            return;
        }
        
        // Check params
        $index_name = $this->input->post('index_name');
        if (!$index_name) {
            $this->handle_bad_parameters(
                    $this->lang->line('error_missing_parameter') . ': index_name');
            return;
        }
        
        // Invoke dms
        $dms = get_dms();
        $result = $dms->create_index('demo');
        
        // Handle result
        echo var_dump($result);
    }
    
    private function handle_missing_auth_error() {
        if ($this->auth->is_ci_request()) {
            redirect('/');
        } else {
            $this->output->set_status_header(403);
        }
    }
    
    private function handle_bad_parameters($error_text) {
        if ($this->auth->is_ci_request()) {
            // TODO
        } else {
            $this->output->set_status_header(400, $error_text);
        }
    }
    
}
