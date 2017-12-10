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
            show_error($this->migration->error_string());
        }
    }
    
}
