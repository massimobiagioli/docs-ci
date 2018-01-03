<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    public function index() {
        // Header
        $this->view_manager->load_header();
        
        // Error Messages
        $data = [];
        $data['login_error_messages'] = $this->view_manager->get_fragment('login_error_messages');
        
        // Login
        $this->load->view('login', $data);
        
        // Footer
        $footer_data = [
            'custom_scripts' => ['login.js']
        ];
        $this->view_manager->load_footer($footer_data);
    }
    
    public function verify() {
        // Get post data
        $login = $this->input->post('login');
        $password = $this->input->post('password');
        
        // Load user model
        $result = $this->auth->get_user($login, $password);
        
        // Store logged user in session
        if ($result) {
            $this->session->set_userdata(['logged_user' => $result]);
            $this->core_client->add_message([
                'type' => 'location',
                'metadata' => [
                    'href' => site_url('home')
                ]
            ]);
        } else {
            $login_error_messages_data = [
                'error_messages' => [
                    $this->lang->line('error_invalid_login')
                ]
            ];
            $this->core_client->set_fragment_data('login_error_messages', $login_error_messages_data);
        }
        
        // Handle response
        $this->core_client->xml_response();
    }

}