<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Dms.php';
require_once __DIR__ . '/Dms_super.php';

use Elasticsearch\ClientBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Document Manager System - ElasticSearch Implementation
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Dms_elastic extends Dms_super implements Dms {
    
    const DOCUMENT_TYPE = 'default';
    
    private $client;
    
    public function __construct() {
        parent::__construct();
        $this->init_client();
    }

    private function init_client() {
        $hosts = [
            [
                'host' => getenv('ELASTIC_HOSTNAME'),
                'port' => getenv('ELASTIC_PORT'),
                'scheme' => getenv('ELASTIC_SCHEME'),
                'user' => getenv('ELASTIC_USERNAME'),
                'pass' => getenv('ELASTIC_PASSWORD')
            ]
        ];
        $this->client = ClientBuilder::create()
                ->setHosts($hosts)
                ->build();
    }

    public function create_index($index_name) {
        try {
            $response = $this->client->indices()->create([
                'index' => $index_name,
                'body' => [
                    'mappings' => [
                        'default' => [
                            '_source' => [
                                'excludes' => [
                                    'data'
                                ]
                            ],
                            'properties' => [
                                'document_info.original_filename' => ['type' => 'keyword'],
                                'document_info.created' => ['type' => 'keyword']
                            ]
                        ]
                    ]
                ]
            ]);
            return $this->adapt_response_create_index($response);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function adapt_response_create_index($toAdapt) {
        return [
            'status' => $toAdapt['acknowledged'] ? 1 : 0,
            'native_result' => $toAdapt
        ];                
    }
    
    public function delete_index($index_name) {
        try {
            $response = $this->client->indices()->delete([
                'index' => $index_name
            ]);
            return $this->adapt_response_delete_index($response);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function adapt_response_delete_index($toAdapt) {
        return [
            'status' => $toAdapt['acknowledged'] ? 1 : 0,
            'native_result' => $toAdapt
        ];        
    }
    
    public function index_document($index, $metadata, $id = null) {
        try {
            $params = [];
            $params['index'] = $index;
            $params['type'] = self::DOCUMENT_TYPE;
            if ($id === null) {
                $uuid4 = Uuid::uuid4();
                $params['id'] = $uuid4->getHex();
            } else {
                $params['id'] = $index;
            }
            $params['body'] = $metadata;

            // Ingest pipeline
            $params['pipeline'] = 'attachment';

            $response = $this->client->index($params);
            return $this->adapt_response_index_document($response);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function adapt_response_index_document($toAdapt) {
        return [
            'status' => strtolower($toAdapt['result']) === 'created' ? 1 : 0,
            'id' => $toAdapt['_id'],
            'native_result' => $toAdapt
        ];        
    }
    
    public function get_document($index, $id) {
        try {
            $params = [
                'index' => $index,
                'type' => self::DOCUMENT_TYPE,
                'id' => $id
            ];            
            $response = $this->client->get($params);
            return $this->adapt_response_get_document($response);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function adapt_response_get_document($toAdapt) {
        return [
            'status' => $toAdapt['found'] ? 1 : 0,
            'id' => $toAdapt['_id'],
            'doc' => $toAdapt['_source'],
            'native_result' => $toAdapt
        ];        
    }
    
    public function delete_document($index, $id) {
        try {
            $params = [
                'index' => $index,
                'type' => self::DOCUMENT_TYPE,
                'id' => $id
            ];            
            $response = $this->client->delete($params);
            return $this->adapt_response_delete_document($response);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function adapt_response_delete_document($toAdapt) {
        return [
            'status' => strtolower($toAdapt['result']) === 'deleted' ? 1 : 0,
            'id' => $toAdapt['_id'],
            'native_result' => $toAdapt
        ];        
    }
    
    public function search_documents($index, $search_info) {
        try {
            $criteria = $this->build_search_criteria($index, $search_info['free_search']);
            
            // Pagination
            if (isset($search_info['start'])) {
                $criteria['from'] = $search_info['start'];
            }
            if (isset($search_info['length'])) {
                $criteria['size'] = $search_info['length'];
            }
            
            // Sorting
            if (isset($search_info['sort_field'])) {
                $criteria['body']['sort'] = [
                    [$search_info['sort_field'] => ['order' => isset($search_info['sort_mode']) ? $search_info['sort_mode'] : 'asc']]
                ];
            }
            
            return $this->client->search($criteria);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function count_documents($index, $search_info) {
        try {
            return $this->client->count($this->build_search_criteria($index, $search_info['free_search'], false));
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    private function build_search_criteria($index, $free_search = '', $exclude_source = true) {
        $criteria = [
            'index' => $index,
            'type' => self::DOCUMENT_TYPE,
        ];
        if ($free_search) {
            $criteria['body']['query'] = [
                'multi_match' => [
                    'query' => $free_search,
                    'type' => 'phrase_prefix',
                    'fields' => [ 
                        'document_metadata.*', 
                        'attachment.content', 
                        'document_info.original_filename'
                    ] 
                ]
            ];
        }
        if ($exclude_source) {
            $criteria['body']['_source'] = ['document_*', 'attachment.content_type'];
        }
        return $criteria;
    }    

}
