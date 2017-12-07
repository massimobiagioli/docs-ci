<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_system'] = function() {
    require 'vendor/autoload.php';
    
    // Load environment variables from .env
    if (file_exists(APPPATH . '.env')) {
        $dotenv = new Dotenv\Dotenv(APPPATH);
        $dotenv->load();        
    }
    
    // Set session parameters
    if (getenv('USE_DEFAULT_SESSION') != 1) {
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', getenv('MEMCACHEDCLOUD_SERVERS'));
        if(version_compare(phpversion('memcached'), '3', '>=')) {
            ini_set('memcached.sess_persistent', 1);
            ini_set('memcached.sess_binary_protocol', 1);
        } else {
            ini_set('session.save_path', 'PERSISTENT=myapp_session ' . ini_get('session.save_path'));
            ini_set('memcached.sess_binary', 1);
        }
        ini_set('memcached.sess_sasl_username', getenv('MEMCACHEDCLOUD_USERNAME'));
        ini_set('memcached.sess_sasl_password', getenv('MEMCACHEDCLOUD_PASSWORD'));        
    }
    
};
