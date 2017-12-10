<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_dms')) {
    
    /**
     * Get dms client
     * @return Dms
     */
    function get_dms() {
        $CI = & get_instance();
        $CI->load->library('dms_factory');
        return $CI->dms_factory->client($CI->config->item('dms_provider'));
    }

}

