<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Log extends Log {

    public function write_log($level, $msg) {
        if ($this->_enabled === FALSE) {
            return FALSE;
        }

        $level = strtoupper($level);

        if ((!isset($this->_levels[$level]) OR ( $this->_levels[$level] > $this->_threshold)) && !isset($this->_threshold_array[$this->_levels[$level]])) {
            return FALSE;
        }

        if (getenv('LOG_MODE') == 1) {  // Stderr
            file_put_contents('php://stderr', $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date($this->_date_fmt) . ' --> ' . $msg . "\n");
        } else {    // Standard
            parent::write_log($level, $msg);
        }

        return TRUE;
    }

}
