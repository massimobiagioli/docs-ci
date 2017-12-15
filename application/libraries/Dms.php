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
     * Delete index
     * @param string $index_name Index name
     * @return array Operation info
     */
    public function delete_index($index_name);
    
    /**
     * Index a document
     * @param string $index Index
     * @param string $type Document Type     
     * @param array $metadata Metadata
     * @param string $id Document Id (if null, automatically use a uuid)
     */
    public function index_document($index, $type, $metadata, $id = null);
    
    /**
     * @return Last error code
     */
    public function last_error_code();
    
    /**
     * @return Last error description
     */
    public function last_error_description();
    
}
