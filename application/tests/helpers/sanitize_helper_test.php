<?php

class sanitize_helper_test extends TestCase {
    
    public function test_sanitize_index_simple() {
        $index_name = "dummy";
        $this->assertEquals('dummy', sanitize_index($index_name));
    }
    
    public function test_sanitize_index_with_space_between() {
        $index_name = "dummy index";
        $this->assertEquals('dummy_index', sanitize_index($index_name));
    }
    
    public function test_sanitize_index_with_space_begin() {
        $index_name = " dummy";
        $this->assertEquals('_dummy', sanitize_index($index_name));
    }
    
    public function test_sanitize_index_with_space_end() {
        $index_name = "dummy ";
        $this->assertEquals('dummy_', sanitize_index($index_name));
    }
    
    public function test_sanitize_metadata_with_space_between() {
        $index_name = "dummy index";
        $this->assertEquals('dummy_index', sanitize_metadata($index_name));
    }
    
    public function test_sanitize_metadata_with_space_begin() {
        $index_name = " dummy";
        $this->assertEquals('_dummy', sanitize_metadata($index_name));
    }
    
    public function test_sanitize_metadata_with_space_end() {
        $index_name = "dummy ";
        $this->assertEquals('dummy_', sanitize_metadata($index_name));
    }
    
}
