<?php

class Core_client_library_test extends TestCase {
    
    private $client;
    
    public function setUp() {        
        $this->client = $this->newLibrary('Core_client');
        $_POST['update'] = '';
    }
    
    public function test_empty_xml_response() {
        $this->client->xml_response();       
        $this->assertEquals('application/xml', $this->CI->output->get_content_type());
        $this->assertContains('<response><messages></messages></response>', 
                $this->CI->output->get_output());
    }
    
    public function test_xml_response_with_console_message() {
        $this->client->add_message([
            'type' => 'console',
            'metadata' => [
                'message' => 'test message'
            ]
        ]);
        $this->client->xml_response();               
        $this->assertEquals('application/xml', $this->CI->output->get_content_type());        
        $this->assertContains('<response><messages><console level="log"><![CDATA[test message]]></console></messages></response>', 
        $this->CI->output->get_output());
    }
    
    public function test_xml_response_with_location_message() {
        $this->client->add_message([
            'type' => 'location',
            'metadata' => [
                'href' => site_url('home')
            ]
        ]);
        $this->client->xml_response();               
        $this->assertEquals('application/xml', $this->CI->output->get_content_type());
        $this->assertContains('<response><messages><location><![CDATA[/home]]></location></messages></response>', 
                $this->CI->output->get_output());
    }
    
    public function test_xml_response_with_output_message() {
        $_POST['update'] = 'login_error_messages';
        $login_error_messages_data = [
            'error_messages' => [
                'error test message'
            ]
        ];
        $this->client->set_fragment_data('login_error_messages', $login_error_messages_data);         
        $this->client->xml_response();               
        $this->assertEquals('application/xml', $this->CI->output->get_content_type());
        $this->assertContains('<response><fragments><login_error_messages>', 
                $this->CI->output->get_output());
        $this->assertContains('error test message', 
                $this->CI->output->get_output());
    }
    
    public function test_xml_response_with_output_message_and_post_update_action() {
        $_POST['update'] = 'home_search_results';
        $params = [
            'url' => '/home/search_documents',
            'page_length' => DATATABLE_DEFAULT_PAGELEN
        ];
        $this->client->set_fragment_data('home_search_results', $params, 'renderDataTable', 'search_results_datatable');
        $this->client->xml_response();               
        $this->assertEquals('application/xml', $this->CI->output->get_content_type());
        $this->assertContains('<response><fragments><home_search_results post_update_action="renderDataTable" post_update_action_params="search_results_datatable">', 
                $this->CI->output->get_output());                
    }
    
}
