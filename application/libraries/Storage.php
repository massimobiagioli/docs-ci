<?php

/**
 * Storage - Interface
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
interface Storage {
    
    /**
     * Upload document
     * @param string $path Document path
     */
    public function upload($path);
    
}

