<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_storage')) {
    /**
     * Get Storage client
     * @return Storage (false on error)
     */
    function get_storage() {
        try {
            $CI = & get_instance();
            $CI->load->library('storage_factory');
            return $CI->storage_factory->client($CI->config->item('storage_provider'));
        } catch (Exception $ex) {
            log_message('error', $ex->getCode() . ' - ' . $ex->getMessage());
            return false;
        }
    }

}
