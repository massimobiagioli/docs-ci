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
        
        $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($_FILES));
        
//        $this->doc_service->index_document($_FILES['file_to_upload'], $this->input->post());
//        if ($this->doc_service->get_status() == ERROR_NONE) {
//            $this->handle_result($this->doc_service->get_message(), $this->doc_service->get_result());
//        } else {
//            if ($this->doc_service->get_status() === ERROR_AUTH) {
//                $this->handle_unauthorized();
//            } else {
//                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
//            }
//        }
    }

    public function prepare_search_documents($flush_xml_response = true) {
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
        if ($flush_xml_response) {
            $this->core_client->xmlResponse();
        }
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
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();
        foreach ($results['docs'] as $result) {
            $datatable_results['data'][] = [
                '<a class="ml-3" href="#" data-action="' . site_url('home/prepare_document_info_dialog/' . $result['id']) . '"' .
                    ' data-update="home_dlg_document_info_body"' .
                    ' data-csrftokenname="' . $csrf_token_name . '"' .
                    ' data-csrfhash="' . $csrf_hash . '"' .
                    ' data-toggle="modal" data-target="#home_dlg_document_info">' . $result['document_info']['original_filename'] .
                '</a>',
                $result['document_info']['created'],
                '<div class="text-center">' .
                    '<a class="ml-3" href="#" data-action="' . site_url('home/prepare_document_info_dialog/' . $result['id']) . '"' .
                        ' data-update="home_dlg_document_info_body"' .
                        ' data-csrftokenname="' . $csrf_token_name . '"' .
                        ' data-csrfhash="' . $csrf_hash . '"' .
                        ' data-toggle="modal" data-target="#home_dlg_document_info"><i class="fa fa-info"></i>' .
                    '</a>' .
                    '<a class="ml-3" href="home/get_document_url?file_handle=' .
                        $result['document_info']['storage_filehandle'] .
                        '" target="_blank"><i class="fa fa-cloud-download"></i>' .
                    '</a>' .
                    '<a class="ml-3" href="#" data-action="' . site_url('home/prepare_document_delete_dialog/' . $result['id']) . '"' .
                        ' data-update="home_dlg_document_delete_body"' .
                        ' data-csrftokenname="' . $csrf_token_name . '"' .
                        ' data-csrfhash="' . $csrf_hash . '"' .                
                        ' data-toggle="modal" data-target="#home_dlg_document_delete"><i class="fa fa-trash"></i>' .
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
    
    public function prepare_document_info_dialog($document_id) {        
        $data = [];
        $this->doc_service->get_document($document_id);
        if ($this->doc_service->get_status() != ERROR_NONE) {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $data['error_message'] = $this->doc_service->get_message();                
            }            
        } else {
            $result = $this->doc_service->get_result();
            $data['document'] = $result['doc'];
        }
        $this->core_client->set_fragment_data('home_dlg_document_info_body', $data);
        $this->core_client->xmlResponse();
    }
    
    public function prepare_document_delete_dialog($document_id) {
        $data = [];
        $this->doc_service->get_document($document_id);
        if ($this->doc_service->get_status() != ERROR_NONE) {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $data['error_message'] = $this->doc_service->get_message();                
            }            
        } else {
            $result = $this->doc_service->get_result();
            $data['document'] = $result['doc'];
        }
        $this->core_client->set_fragment_data('home_dlg_document_delete_body', $data);
        $this->core_client->xmlResponse();
    }
    
    public function confirm_delete_document() {
        $this->doc_service->delete_document($this->input->post('docid'));
        if ($this->doc_service->get_status() == ERROR_NONE) {
            $this->prepare_search_documents(false);
            $this->handle_result($this->doc_service->get_message(), $this->doc_service->get_result());
        } else {
            if ($this->doc_service->get_status() === ERROR_AUTH) {
                $this->handle_unauthorized();
            } else {
                $this->handle_error($this->doc_service->get_message(), $this->doc_service->get_native_status());
            }
        }
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
