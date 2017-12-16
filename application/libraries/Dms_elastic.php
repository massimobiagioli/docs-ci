<?php

require_once __DIR__ . '/Dms.php';
require_once __DIR__ . '/Dms_super.php';

use Elasticsearch\ClientBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Document Manager System - ElasticSearch Implementation
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Dms_elastic extends Dms_super implements Dms {
    
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
                            ]
                        ]
                    ]
                ]
            ]);
            return $response;
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }
    
    public function delete_index($index_name) {
        try {
            $response = $this->client->indices()->delete([
                'index' => $index_name
            ]);
            return $response;
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function index_document($index, $metadata, $id = null) {
        try {
            $params = [];
            $params['index'] = $index;
            $params['type'] = 'default';
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
            return $response;
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

}

