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
                $this->core_client->set_fragment_data('admin_error_messages', ['error_messages' => [$this->migration->error_string()]]);
            }            
        } else {
            if (!is_cli()) {
                $this->core_client->set_fragment_data('admin_result', ['result' => $this->lang->line('migrations_executed')]);
            }            
        }
        
        $this->core_client->xml_response();
    }
    
    public function create_index() {
        $this->doc_service->create_index($this->input->post('index_name'));
        if ($this->doc_service->get_status() == ERROR_NONE) {
            $this->handle_result($this->doc_service->get_message(), $this->doc_service->get_result());
        } else {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
        }
    }

    public function delete_index() {
        $this->doc_service->delete_index($this->input->post('index_name'));
        if ($this->doc_service->get_status() == ERROR_NONE) {
            $this->handle_result($this->doc_service->get_message(), $this->doc_service->get_result());
        } else {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
        }
    }
    
    private function handle_unauthorized() {
        redirect('/');
    }

    private function handle_error($error_message, $native_status) {
        $this->core_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_message]]);
        $this->core_client->xml_response();
    }

    private function handle_result($message, $result) {
        $this->core_client->set_fragment_data('admin_result', ['result' => $message]);
        $this->core_client->xml_response();
    }
}
