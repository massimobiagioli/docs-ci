<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
    private static $SORT_MAPPING = [
        'document_info.original_filename',
        'document_info.created'
    ];
    
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
        
        // Read search input and store it in session
        $free_search = $this->input->post('free_search');
        $this->session->set_userdata('free_search', $free_search);

        // Prepare Datatable
        $params = [
            'url' => '/home/search_documents',
            'page_length' => DATATABLE_DEFAULT_PAGELEN
        ];
        $this->core_client->set_fragment_data('home_search_results', $params, 'renderDataTable', 'search_results_datatable');
        $this->core_client->xmlResponse();
    }

    public function search_documents() {
        // Init search info
        $search_info = [
            'free_search' => $this->session->userdata('free_search')
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

        // Implements search info with pagination and sorting information
        $search_info['start'] = intval($this->input->post('start'));
        $search_info['length'] = intval($this->input->post('length'));
        $order_info = $this->input->post('order');
        $search_info['sort_field'] = self::$SORT_MAPPING[$order_info[0]['column']];
        $search_info['sort_mode'] = $order_info[0]['dir'];

        // Search Documents
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
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $total_results,
            'recordsFiltered' => $total_results
        ];

        // Loop over results
        $datatable_results['data'] = [];
        foreach ($results['hits']['hits'] as $result) {
            $datatable_results['data'][] = [
                $result['_source']['document_info']['original_filename'],
                $result['_source']['document_info']['created'],
                '<div class="text-center">' .
                    '<a class="ml-3" href="#" data-docid="' . $result['_id'] . '"' .
                        ' data-toggle="modal" data-target="#dlg_document_info"><i class="fa fa-info"></i>' .
                    '</a>' .
                    '<a class="ml-3" href="home/get_document_url?file_handle=' .
                        $result['_source']['document_info']['storage_filehandle'] .
                        '" target="_blank"><i class="fa fa-cloud-download"></i>' .
                    '</a>' .
                    '<a class="ml-3" href="#" data-docid="' . $result['_id'] . '"' .
                        ' data-toggle="modal" data-target="#dlg_document_delete"><i class="fa fa-trash"></i>' .
                    '</a>' .
                '</div>'
            ];
        }

        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($datatable_results));
    }

    public function get_document_url() {
        $this->doc_service->get_document_url($this->input->get('file_handle'));
        if ($this->doc_service->get_status() != ERROR_NONE) {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
            return;
        }
        redirect($this->doc_service->get_result());
    }

    private function handle_unauthorized() {
        redirect('/');
    }

    private function handle_error($error_message, $native_status) {
        $this->core_client->set_fragment_data('home_error_messages', ['error_messages' => [$error_message]]);
        $this->core_client->xmlResponse();
    }

    private function handle_result($message, $result) {
        $this->core_client->set_fragment_data('home_result', ['result' => $message]);
        $this->core_client->xmlResponse();
    }
    
}
