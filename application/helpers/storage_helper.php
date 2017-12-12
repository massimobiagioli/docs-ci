<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_storage')) {
    
    /**
     * Get Storage client
     * @return Storage
     */
    function get_storage() {
        $CI = & get_instance();
        $CI->load->library('storage_factory');
        return $CI->storage_factory->client($CI->config->item('storage_provider'));
    }

}
