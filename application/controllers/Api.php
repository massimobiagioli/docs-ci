<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api Restful Controller
 */
class Api extends CI_Controller {
    
    private static $ROUTES = [
        'create_index' => 'post',
        'delete_index' => 'delete',
        'index_document' => 'post',
        'count_documents' => 'get',
        'get_document' => 'get',
        'get_document_url' => 'get',
        'delete_document' => 'delete',
        'search_documents' => 'get'
    ];
    
    public function rest_router($action) {
        if (!array_key_exists(strtolower($action), self::$ROUTES)) {
            $this->handle_not_found();
        }
        if (strtolower($this->input->server('REQUEST_METHOD')) !== self::$ROUTES[$action]) {
            $this->handle_not_allowed();
        }
        call_user_func([$this, $action]);
    }
    
    private function create_index() {
        $this->doc_service->create_index($this->input->post('index_name'));
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
    
    private function delete_index() {
        $this->doc_service->delete_index($this->input->input_stream('index_name'));
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
    
    private function index_document() {
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
    
    private function count_documents() {
        // Init search info
        $search_info = [
            'free_search' => $this->input->get('free_search')
        ];
        // Invoke doc service
        $this->doc_service->count_documents($search_info);
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
    
    private function get_document() {        
        // Invoke doc service
        $this->doc_service->get_document($this->input->get('id'));
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
    
    private function get_document_url() {        
        // Invoke doc service
        $this->doc_service->get_document_url($this->input->get('file_handle'));
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
    
    private function delete_document() {        
        // Invoke doc service
        $this->doc_service->delete_document($this->input->input_stream('id'));
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
    
    private function search_documents() {
        // Init search info
        $search_info = [
            'free_search' => $this->input->get('free_search')
        ];
        // Invoke doc service
        $this->doc_service->search_documents($search_info);
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
    
    private function handle_unauthorized() {
        $this->output->set_status_header(401);
    }
    
    private function handle_not_allowed() {
        $this->output->set_status_header(403);
    }
    
    private function handle_not_found() {
        $this->output->set_status_header(404);
    }
    
    private function handle_error($error_message, $native_status) {
        $error_data = [
            'internal_error' => $error_message,
            'native_error' => $native_status 
        ];
        $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode($error_data));
    }

    private function handle_result($message, $result) {
        $response_data = [
            'message' => $message,
            'result' => $result 
        ];
        $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response_data));
    }
    
}
