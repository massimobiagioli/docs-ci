<?php

/**
 * Document Manager System - Superclass
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
abstract class Dms_super {
    
    protected $last_error_code;
    protected $last_error_description;
    
    public function __construct() {
        $this->reset_error();
    }
    
    protected function reset_error() {
        $this->last_error_code = 0;
        $this->last_error_description = '';
    }
    
    protected function set_error($error_code, $error_description) {
        $this->last_error_code = $error_code;
        $this->last_error_description = $error_description;
    }
    
    public function last_error_code() {
        return $this->last_error_code;
    }
    
    public function last_error_description() {
        return $this->last_error_description;
    }
    
}

