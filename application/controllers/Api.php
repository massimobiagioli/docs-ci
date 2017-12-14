<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;

class Api extends CI_Controller {
    
    public function test_upload() {
        $display = ['FILES' => print_r($_FILES, true),
                    'POST' => print_r($_POST, true)];
        $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(print_r($display, true));
    }
    
    public function create_index() {
        // Check auth
        $user = $this->auth->get_user();
        if (!$user || !$this->auth->is_user_admin($user)) {
            $this->handle_missing_auth_error();
            return;
        }
        
        // Check params
        $index_name = $this->input->post('index_name');
        if (!$index_name) {
            $this->handle_bad_parameters(
                    $this->lang->line('error_missing_parameter') . ': index_name');
            return;
        }
        
        // Invoke dms
        $dms = get_dms();
        $result = $dms->create_index($index_name);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_create_index'),
                    $dms->last_error_code(), $dms->last_error_description());
            return;
        }
        
        // Handle result
        $this->handle_result($this->lang->line('index_created'), $result);
    }
    
    public function delete_index() {
        // Check auth
        $user = $this->auth->get_user();
        if (!$user || !$this->auth->is_user_admin($user)) {
            $this->handle_missing_auth_error();
            return;
        }
        
        // Check params
        $index_name = $this->input->post('index_name');
        if (!$index_name) {
            $this->handle_bad_parameters(
                    $this->lang->line('error_missing_parameter') . ': index_name');
            return;
        }
        
        // Invoke dms
        $dms = get_dms();
        $result = $dms->delete_index($index_name);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_delete_index'),
                    $dms->last_error_code(), $dms->last_error_description());
            return;
        }
        
        // Handle result
        $this->handle_result($this->lang->line('index_deleted'), $result);
    }
    
    public function upload_document() {
        // Check auth
        $user = $this->auth->get_user();
        if (!$user || !$this->auth->is_user_admin($user)) {
            $this->handle_missing_auth_error();
            return;
        }
        
        // Check params
        $file_to_upload = $_FILES['file_to_upload'];
        if (!$file_to_upload) {
            $this->handle_bad_parameters(
                    $this->lang->line('error_missing_parameter') . ': file_to_upload');
            return;
        }
        
        // Invoke storage
        $storage = get_storage();
        $uuid4 = Uuid::uuid4();
        $filename = $uuid4->toString();
        $result = $storage->upload($file_to_upload['tmp_name'], $filename);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_upload_document'),
                    $storage->last_error_code(), $storage->last_error_description());
            return;
        }
        
        // Invoke DMS
        // TODO ...
        
        // Handle result
        $this->handle_result($this->lang->line('document_uploaded'), $result);
    }
    
    private function handle_missing_auth_error() {
        if ($this->auth->is_ci_request()) {
            redirect('/');
        } else {
            $this->output->set_status_header(403);
        }
    }
    
    private function handle_bad_parameters($error_text) {
        if ($this->auth->is_ci_request()) {
            $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_text]]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output->set_status_header(400, $error_text);
        }
    }
    
    private function handle_internal_error($error_message, $error_code, $error_description) {
        if ($this->auth->is_ci_request()) {
            $this->ignition_client->set_fragment_data('admin_error_messages', ['error_messages' => [$error_message]]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output->set_status_header(500, $error_message . ' (' . $error_code . ' - ' . $error_description . ')');
        }
    }
    
    private function handle_result($message, $result) {
        if ($this->auth->is_ci_request()) {
            $this->ignition_client->set_fragment_data('admin_result', ['result' => $message]);
            $this->ignition_client->xmlResponse();
        } else {
            $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result));
        }
    }
    
}
