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
            return $this->client->indices()->create([
                'index' => $index_name,
                'body' => [
                    'mappings' => [
                        'default' => [
                            '_source' => [
                                'excludes' => [
                                    'data'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function delete_index($index_name) {
        try {
            return $this->client->indices()->delete([
                'index' => $index_name
            ]);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
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

            return $this->client->index($params);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function search_documents($index, $search_info) {
        try {
            $criteria = [
                'index' => $index,
                'type' => self::DOCUMENT_TYPE,
                "_source" => ["document_*", "attachment.content_type"]
//                'body' => [
//                    'query' => [
//                        'match' => [
//                            'testField' => 'abc'
//                        ]
//                    ]
//                ]
            ];
            
            // Pagination
            if (isset($search_info['start'])) {
                $criteria['from'] = $search_info['start'];
            }
            if (isset($search_info['length'])) {
                $criteria['size'] = $search_info['length'];
            }
            
            return $this->client->search($criteria);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function count_documents($index, $search_info) {
        try {
            $criteria = [
                'index' => $index,
                'type' => self::DOCUMENT_TYPE,
//                'body' => [
//                    'query' => [
//                        'match' => [
//                            'testField' => 'abc'
//                        ]
//                    ]
//                ]
            ];
            return $this->client->count($criteria);
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

}
