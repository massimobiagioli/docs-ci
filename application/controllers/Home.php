<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $logged_user = $this->session->userdata('logged_user');
        if (!$logged_user) {
            redirect('/');
            return;
        }
        
        // Header
        $this->view_manager->load_header();

        // Navbar
        $data = [];
        $navbar_data = [
            'logged_user' => $logged_user,
            'current' => 'home'
        ];
        $data['navbar'] = $this->view_manager->get_fragment('navbar', $navbar_data);

        // Result
        $data['home_result'] = $this->view_manager->get_fragment('home_result');

        // Error Messages
        $data['home_error_messages'] = $this->view_manager->get_fragment('home_error_messages');

        // Home
        $this->load->view('home', $data);

        // Footer
        $footer_data = [
            'custom_scripts' => ['home.js']
        ];
        $this->view_manager->load_footer($footer_data);
    }

    public function logout() {
        session_destroy();
        redirect('/');
    }

    public function index_document() {
        $this->doc_service->index_document($_FILES['file_to_upload'], $this->input->post());
        if ($this->doc_service->get_status() == ERROR_NONE) {
            $this->handle_result($this->doc_service->get_message(), $this->doc_service->get_result());
        } else {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
        }
    }

    public function prepare_search_documents() {
        // Clear session data
        $this->session->unset_userdata('total_results');
        
        // Prepare Datatable
        $params = [
            'url' => '/home/search_documents',
            'page_length' => DATATABLE_DEFAULT_PAGELEN
        ];
        $this->ignition_client->set_fragment_data('home_search_results', $params, 'renderDataTable', 'search_results_datatable');
        $this->ignition_client->xmlResponse();
    }
    
    public function search_documents() {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        
        // Init search info
        $search_info = [
            'free_search' => $this->input->post('free_search')
        ];
        
        // Count documents
        $total_results = $this->session->userdata('total_results');
        if (!$total_results) {
            $this->doc_service->count_documents($search_info);
            if ($this->doc_service->get_status() != ERROR_NONE) {
                if ($this->doc_service->get_status() === ERROR_AUTH) {
                    $this->handle_unauthorized();
                } else {
                    $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
                }
                return;
            }
            $total_results = $this->doc_service->get_result();
            $this->session->set_userdata('total_results', $total_results);
        }
        
        // Search Documents
        $search_info['start'] = $start;
        $search_info['length'] = $length;
        $this->doc_service->search_documents($search_info);
        if ($this->doc_service->get_status() != ERROR_NONE) {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
            return;
        }
        $results = $this->doc_service->get_result();
        
        // Prepare Datatable Results
        $datatable_results = [
            'draw' => $draw,
            'recordsTotal' => $total_results['count'],
            'recordsFiltered' => $total_results['count']
        ];
        
        // Loop over results
        foreach ($results['hits']['hits'] as $result) {
            $datatable_results['data'][] = [
                $result['_source']['document_info']['original_filename'],
                $result['_source']['document_info']['created'],
                '<div class="document-download text-center"><a href="home/download_document?storage_id=' . 
                    $result['_source']['document_info']['storage_filename'] . 
                    '"><i class="fa fa-cloud-download"></i><div></a>'
            ];
        }
       
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($datatable_results));
    }
    
    public function download_document() {
        $storage_filename = $this->input->get('storage_id');
        
        // TODO ....
        $this->output
                ->set_content_type('text/plain')
                ->set_output(json_encode($storage_filename));
    }
    
    private function handle_unauthorized() {
        redirect('/');
    }

    private function handle_error($error_message, $native_status) {
        $this->ignition_client->set_fragment_data('home_error_messages', ['error_messages' => [$error_message]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_result($message, $result) {
        $this->ignition_client->set_fragment_data('home_result', ['result' => $message]);
        $this->ignition_client->xmlResponse();
    }

}
