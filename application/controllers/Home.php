<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $logged_user = $this->session->userdata('logged_user');
        if (!$logged_user) {
            redirect('/');
            return;
        }
        
        // Header
        $this->view_manager->load_header();
        
        // Navbar
        $data = [];
        $navbar_data = [
            'logged_user' => $logged_user,
            'current' => 'home'
        ];
        $data['navbar'] = $this->view_manager->get_fragment('navbar', $navbar_data);
        
        // Result
        $data['home_result'] = $this->view_manager->get_fragment('home_result');
        
        // Error Messages
        $data['home_error_messages'] = $this->view_manager->get_fragment('home_error_messages');
        
        // Home
        $this->load->view('home', $data);
        
        // Footer
        $footer_data = [
            'custom_scripts' => ['home.js']
        ];
        $this->view_manager->load_footer($footer_data);
    }
    
    public function logout() {
        session_destroy();
        redirect('/');
    }
    
    public function index_document() {
        $this->doc_service->index_document($_FILES['file_to_upload'], $this->input->post());
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
        $this->ignition_client->set_fragment_data('home_error_messages', ['error_messages' => [$error_message]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_result($message, $result) {
        $this->ignition_client->set_fragment_data('home_result', ['result' => $message]);
        $this->ignition_client->xmlResponse();
    }

}
