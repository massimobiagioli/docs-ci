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
        $result = $dms->create_index($index_name);
        if (!$result) {
            $this->handle_internal_error($dms->last_error_code(), $dms->last_error_description());
            return;
        }
        
        // Handle result
        $this->handle_result($result);
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
            $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_text]]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output->set_status_header(400, $error_text);
        }
    }
    
    private function handle_internal_error($error_code, $error_description) {
        $error_string = $error_code . ' - ' . $error_description;
        if ($this->auth->is_ci_request()) {
            $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_string]]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output->set_status_header(5001, $error_string);
        }
    }
    
    private function handle_result($result) {
        if ($this->auth->is_ci_request()) {
            $this->ignition_client->set_fragment_data('admin_result', ['result' => $result]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result));
        }
    }
    
}
