<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Storage.php';
require_once __DIR__ . '/Storage_super.php';

use Filestack\FilestackClient;
use Filestack\Filelink;

/**
 * Storage - FileStack Implementation
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Storage_filestack extends Storage_super implements Storage {
    
    private $client;
    
    public function __construct() {
        parent::__construct();
        $this->init_client();
    }
    
    private function init_client() {
        $this->client = new FilestackClient(getenv('FILESTACK_APIKEY'));
    }

    public function upload($path, $filename) {
        try {
            $link = $this->client->upload($path, ['filename' => $filename]);
            return $link;
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

    public function get_file_url($file_handle) {
        try {
            $filelink = new Filelink($file_handle, getenv('FILESTACK_APIKEY'));
            return $filelink->url();
        } catch (Exception $e) {
            $this->set_error($e->getCode(), $e->getMessage());
        }
    }

}
