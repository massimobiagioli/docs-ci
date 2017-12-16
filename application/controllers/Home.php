<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;

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
        $filename = $uuid4->getHex();
        $result = $storage->upload($file_to_upload['tmp_name'], $filename);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_upload_document'), $storage->last_error_code(), $storage->last_error_description());
            return;
        }

        // Set metadata
        $post_data = $this->input->post();
        $metadata = [];
        foreach ($post_data as $key => $value) {
            if (strtolower(substr($key, 0, 3)) === 'key') {
                $vk = 'value' . substr($key, 3);
                $metadata['document_metadata'][$value] = $post_data[$vk];
            }
        }

        // Add document metadata
        $metadata['document_info'] = [
            'storage_filename' => $filename,
            'original_filename' => $file_to_upload['name'],
            'created' => date('Y-m-d H:i:s')
        ];

        // Add document data
        $metadata['data'] = base64_encode(file_get_contents($file_to_upload['tmp_name']));

        // Invoke DMS
        $dms = get_dms();
        $result = $dms->index_document($user['user_login'], $metadata);
        if (!$result) {
            $this->handle_internal_error($this->lang->line('error_index_document'), $dms->last_error_code(), $dms->last_error_description());
            return;
        }

        // Handle result
        $this->handle_result($this->lang->line('document_uploaded'), $result);
    }
    
    private function handle_missing_auth_error() {
        redirect('/');
    }

    private function handle_bad_parameters($error_text) {
        $this->ignition_client->set_fragment_data('home_error_messages', ['error_messages' => [$error_text]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_internal_error($error_message, $error_code, $error_description) {
        $this->ignition_client->set_fragment_data('home_error_messages', ['error_messages' => [$error_message]]);
        $this->ignition_client->xmlResponse();
    }

    private function handle_result($message, $result) {
        $this->ignition_client->set_fragment_data('home_result', ['result' => $message]);
        $this->ignition_client->xmlResponse();
    }

}
