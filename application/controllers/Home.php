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
        
        // Home
        $this->load->view('home', $data);
        
        // Footer
        $this->view_manager->load_footer();
    }
    
    public function logout() {
        session_destroy();
        redirect('/');
    }

}
