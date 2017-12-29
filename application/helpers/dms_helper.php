<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_dms')) {
    /**
     * Get dms client
     * @return Dms (false on error)
     */
    function get_dms() {
        try {
            $CI = & get_instance();
            $CI->load->library('dms_factory');
            return $CI->dms_factory->client($CI->config->item('dms_provider'));
        } catch (Exception $ex) {
            log_message('error', $ex->getCode() . ' - ' . $ex->getMessage());
            return false;
        }
    }

}

