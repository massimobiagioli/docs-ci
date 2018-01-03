<?php

class Storage_factory_library_test extends TestCase {
    
    const GOOD_PROVIDER = 'filestack';
    const BAD_PROVIDER = 'the_beautiful_provider';
    
    private $factory;
    
    public function setUp() {
        $this->factory = $this->newLibrary('Storage_factory');
    }
    
    public function test_client_with_existing_provider() {
        $client = $this->factory->client(self::GOOD_PROVIDER);
        $this->assertNotNull($client);
    }
    
    public function test_client_with_invalid_provider() {
        $client = $this->factory->client(self::BAD_PROVIDER);
        $this->assertNull($client);
    }
    
}
