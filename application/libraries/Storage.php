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
    
}

