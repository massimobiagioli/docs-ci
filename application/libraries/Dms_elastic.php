<?php

require_once __DIR__ . '/Dms.php';
require_once __DIR__ . '/Dms_super.php';

use Elasticsearch\ClientBuilder;

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
                'index' => $index_name
            ]);
            return $response;
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

}

