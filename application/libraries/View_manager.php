<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * View Manager
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class View_manager {

    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }
    
    public function load_header($fragment_data = null) {
        $this->CI->load->view('fragments/header', $fragment_data);
    }
    
    public function load_footer($fragment_data = null) {
        $this->CI->load->view('fragments/footer', $fragment_data);
    }
    
    public function get_fragment($fragment_name, $fragment_data = null) {
        return $this->CI->load->view("fragments/$fragment_name", $fragment_data, true);
    }
    
}
