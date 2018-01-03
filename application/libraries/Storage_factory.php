<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Storage - Factory
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Storage_factory {
    
    private static $providers = [
        'filestack' => 'Storage_filestack'
    ];
    
    /**
     * Get Storage Client
     * @param string $provider Provider (filestack|... )
     * @return Storage or null
     */
    public function client($provider) {
        if (!isset(self::$providers[$provider])) {
            return null;
        }
        $clazz = self::$providers[$provider];
        require_once __DIR__ . '/' . $clazz . '.php';
        $client = new $clazz;
        return $client;
    }
    
}
