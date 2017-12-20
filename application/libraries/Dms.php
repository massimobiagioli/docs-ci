<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Document Manager System - Interface
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
interface Dms {
    
    /**
     * Create new index
     * @param string $index_name Index name
     * @return array Operation info
     * @return array Results
     */
    public function create_index($index_name);
    
    /**
     * Delete index
     * @param string $index_name Index name
     * @return array Operation info
     * @return array Results
     */
    public function delete_index($index_name);
    
    /**
     * Index a document
     * @param string $index Index   
     * @param array $metadata Metadata
     * @param string $id Document Id (if null, automatically use a uuid)
     * @return array Results
     */
    public function index_document($index, $metadata, $id = null);
    
    /**
     * Search documents
     * @param string $index Index   
     * @param array $params Search params
     * @return array Documents
     */
    public function search_documents($index, $params);
    
    /**
     * @return Last error code
     */
    public function last_error_code();
    
    /**
     * @return Last error description
     */
    public function last_error_description();
    
}
