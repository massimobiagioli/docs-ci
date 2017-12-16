<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    public function index() {
        $logged_user = $this->session->userdata('logged_user');
        if (!$logged_user || $logged_user['user_admin'] == 0) {
            redirect('/');
            return;
        }
        
        // Header
        $this->view_manager->load_header();
        
        // Navbar
        $data = [];
        $navbar_data = [
            'logged_user' => $logged_user,
            'current' => 'admin'
        ];
        $data['navbar'] = $this->view_manager->get_fragment('navbar', $navbar_data);
        
        // Result
        $data['admin_result'] = $this->view_manager->get_fragment('admin_result');
        
        // Error Messages
        $data['admin_error_messages'] = $this->view_manager->get_fragment('admin_error_messages');
        
        // Home
        $this->load->view('admin', $data);
        
        // Footer
        $footer_data = [
            'custom_scripts' => ['admin.js']
        ];
        $this->view_manager->load_footer($footer_data);
    }

    public function migrate() {
        $logged_user = $this->session->userdata('logged_user');
        if (!$logged_user || $logged_user['user_admin'] == 0) {
            redirect('/');
            return;
        }
        
        $this->load->library('migration');
        
        if ($this->migration->current() === FALSE) {
            if (is_cli()) {
                show_error($this->migration->error_string());
            } else {
                $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$this->migration->error_string()]]);
            }            
        } else {
            if (!is_cli()) {
                $this->ignition_client->set_fragment_data('admin_result', ['result' => $this->lang->line('migrations_executed')]);
            }            
        }
        
        $this->ignition_client->xmlResponse();
    }
    
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
            $this->handle_internal_error($this->lang->line('error_create_index'), $dms->last_error_code(), $dms->last_error_description());
            return;
        }

        // Handle result
        $this->handle_result($this->lang->line('index_created'), $result);
    }

    public function delete_index() {
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
        $result = $dms->delete_index($index_name);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_delete_index'), $dms->last_error_code(), $dms->last_error_description());
            return;
        }

        // Handle result
        $this->handle_result($this->lang->line('index_deleted'), $result);
    }
    
    private function handle_missing_auth_error() {
        redirect('/');
    }

    private function handle_bad_parameters($error_text) {
        $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_text]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_internal_error($error_message, $error_code, $error_description) {
        $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_message]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_result($message, $result) {
        $this->ignition_client->set_fragment_data('admin_result', ['result' => $message]);
        $this->ignition_client->xmlResponse();
    }
}
