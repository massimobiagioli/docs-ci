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
     * @return array Result
     *      - status (0/1)
     *      - native_result (array)
     */
    public function create_index($index_name);
    
    /**
     * Delete index
     * @param string $index_name Index name
     * @return array Operation info
     * @return array Result
     *      - status (0/1)
     *      - native_result (array)
     */
    public function delete_index($index_name);
    
    /**
     * Index a document
     * @param string $index Index   
     * @param array $metadata Metadata
     * @param string $id Document Id (if null, automatically use a uuid)
     * @return array Result
     *      - status (0/1)
     *      - id
     *      - native_result (array)
     */
    public function index_document($index, $metadata, $id = null);
    
    /**
     * Get document
     * @param string $index Index   
     * @param string $id Document Id
     * @return array Result
     *      - status (0/1)
     *      - id
     *      - document
     *      - native_result (array)
     */
    public function get_document($index, $id);
    
    /**
     * Delete document
     * @param string $index Index   
     * @param string $id Document Id
     * @return array Result
     *      - status (0/1)
     *      - id
     *      - native_result (array)
     */
    public function delete_document($index, $id);
    
    /**
     * Search documents
     * @param string $index Index   
     * @param array $search_info Search info
     * @return array Documents
     */
    public function search_documents($index, $search_info);
    
    /**
     * Count documents
     * @param string $index Index   
     * @param array $search_info Search info
     * @return array Results
     */
    public function count_documents($index, $search_info);
    
    /**
     * @return Last error code
     */
    public function last_error_code();
    
    /**
     * @return Last error description
     */
    public function last_error_description();
    
}
