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

}
