<?php

/**
 * Document Manager System - Factory
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Dms_factory {
    
    private static $providers = [
        'elastic' => 'Dms_elastic'
    ];
    
    /**
     * Get DMS Client
     * @param string $provider Provider (elastic|... )
     * @return Dms or null
     */
    public static function client($provider) {
        if (!isset(self::$providers[$provider])) {
            return null;
        }
        $clazz = self::$providers[$provider];
        require_once __DIR__ . '/' . $clazz . '.php';
        $client = new $clazz;
        return $client;
    }
    
}

