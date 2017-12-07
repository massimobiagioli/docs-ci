<?php

/**
 * Document Manager System - Interface
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
interface Dms {
    
    /**
     * Create new index
     * @param string $index_name Index name
     * @return array Operation info
     */
    public function create_index($index_name);
    
    /**
     * @return Last error code
     */
    public function last_error_code();
    
    /**
     * @return Last error description
     */
    public function last_error_description();
    
}
