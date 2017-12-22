<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Storage - Interface
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
interface Storage {
    
    /**
     * Upload document
     * @param string $path Document path
     * @param string $filename Document filename
     */
    public function upload($path, $filename);
    
    /**
     * Download document
     * @param string $file_handle File handle
     * @return string File url
     */
    public function get_file_url($file_handle);
    
}

