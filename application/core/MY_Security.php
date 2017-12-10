<?php

class MY_Security extends CI_Security {
    
    private static $DUMMY_API_KEY = '123456789';
            
    function csrf_verify() {
        // Check for X-API-KEY in http request for csrf security bypass
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
        if ($api_key) {
            if (!$this->check_api_key($api_key)) {
                $this->csrf_show_error();
            }
            return $this;
        }
        parent::csrf_verify();
    }
    
    private function check_api_key($api_key) {
        // TODO
        return ($api_key === self::$DUMMY_API_KEY);
    }
    
}
