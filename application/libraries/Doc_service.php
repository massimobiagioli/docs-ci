<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;

/**
 * Document Service Facade
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Doc_service {

    private $CI;
    private $status;
    private $message;
    private $result;
    private $native_status;

    public function __construct() {
        $this->CI = & get_instance();
    }

    /**
     * Create index on DMS
     * @param string $index_name Index name
     */
    public function create_index($index_name) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user || !$this->CI->auth->is_user_admin($user)) {
                $this->set_status(ERROR_AUTH, $this->CI->lang->line('unauthorized'));
                return;
            }

            // Check params
            if (!$index_name) {
                $this->set_status(ERROR_PRECONDITION, $this->CI->lang->line('error_missing_parameter_index_name'));
                return;
            }

            // Invoke dms
            $dms = get_dms();
            $result = $dms->create_index($index_name);
            if (!$result) {
                $this->set_status(ERROR_DMS, $this->CI->lang->line('error_create_index') . ':' . $index_name, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, $this->CI->lang->line('index_created') . ':' . $index_name, $result);
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    /**
     * Create index on DMS
     * @param string $index_name Index name
     */
    public function delete_index($index_name) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user || !$this->CI->auth->is_user_admin($user)) {
                $this->set_status(ERROR_AUTH, $this->CI->lang->line('unauthorized'));
                return;
            }

            // Check params
            if (!$index_name) {
                $this->set_status(ERROR_PRECONDITION, $this->CI->lang->line('error_missing_parameter_index_name'));
                return;
            }

            // Invoke dms
            $dms = get_dms();
            $result = $dms->delete_index($index_name);
            if (!$result) {
                $this->set_status(ERROR_DMS, $this->CI->lang->line('error_delete_index') . ':' . $index_name, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
            }

            // Handle result
            $this->set_status(ERROR_NONE, $this->CI->lang->line('index_deleted') . ':' . $index_name, $result);
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
        }
    }
    
    /**
     * Upload and index document
     * @param array $file_to_upload File to upload
     * @param array $data Document Metadata
     */
    public function index_document($file_to_upload, $data) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user || !$this->CI->auth->is_user_admin($user)) {
                $this->set_status(ERROR_AUTH, $this->CI->lang->line('unauthorized'));
                return;
            }

            // Check params
            if (!$file_to_upload) {
                $this->set_status(ERROR_PRECONDITION, $this->CI->lang->line('error_missing_parameter_filename'));
                return;
            }

            // Invoke storage
            $storage = get_storage();
            $uuid4 = Uuid::uuid4();
            $filename = $uuid4->getHex();
            $result = $storage->upload($file_to_upload['tmp_name'], $filename);
            if (!$result) {
                $this->set_status(ERROR_STORAGE, $this->CI->lang->line('error_upload_document'), null, [
                    'error_code' => $storage->last_error_code(),
                    'error_description' => $storage->last_error_description()
                ]);
                return;
            }

            // Set metadata
            $metadata = [];
            foreach ($data as $key => $value) {
                if (strtolower(substr($key, 0, 3)) === 'key') {
                    $vk = 'value' . substr($key, 3);
                    $metadata['document_metadata'][$value] = $data[$vk];
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
                $this->set_status(ERROR_DMS, $this->CI->lang->line('error_index'), null, [
                    'error_code' => $storage->last_error_code(),
                    'error_description' => $storage->last_error_description()
                ]);
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, $this->CI->lang->line('document_indexed_successfully'), $result);
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    private function reset_status() {
        $this->set_status(ERROR_NONE, '');
    }

    private function set_status($status, $message, $result = null, $native_status = []) {
        $this->status = $status;
        $this->message = $message;
        $this->result = $result;
        $this->native_status = $native_status;
    }

    public function get_status() {
        return $this->status;
    }

    public function get_message() {
        return $this->message;
    }

    public function get_result() {
        return $this->result;
    }

    public function get_native_status() {
        return $this->native_status;
    }

}
