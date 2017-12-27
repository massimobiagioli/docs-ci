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
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }

            // Check params
            if (!$index_name) {
                $msg = $this->CI->lang->line('error_missing_parameter_index_name');
                $this->set_status(ERROR_PRECONDITION, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Sanitize input
            $index_name = sanitize_index($index_name);
            
            // Invoke dms
            $dms = get_dms();
            $result = $dms->create_index($index_name);
            if (!$result) {
                $msg = $this->CI->lang->line('error_create_index') . ':' . $index_name;
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $msg = $this->CI->lang->line('index_created') . ':' . $index_name;
            $this->set_status(ERROR_NONE, $msg, $result);
            log_message('info', $msg . ' (' . json_encode($result) . ')');
        } catch (Exception $e) {
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
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
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }

            // Check params
            if (!$index_name) {
                $msg = $this->CI->lang->line('error_missing_parameter_index_name');
                $this->set_status(ERROR_PRECONDITION, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Sanitize input
            $index_name = sanitize_index($index_name);
            
            // Invoke dms
            $dms = get_dms();
            $result = $dms->delete_index($index_name);
            if (!$result) {
                $msg = $this->CI->lang->line('error_delete_index') . ':' . $index_name;
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $msg = $this->CI->lang->line('index_deleted') . ':' . $index_name;
            $this->set_status(ERROR_NONE, $msg, $result);
            log_message('info', $msg . ' (' . json_encode($result) . ')');
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
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
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }

            // Check params
            if (!$file_to_upload) {
                $msg = $this->CI->lang->line('error_missing_parameter_filename');
                $this->set_status(ERROR_PRECONDITION, $msg);
                log_message('error', $msg);
                return;
            }

            // Invoke storage
            $storage = get_storage();
            $uuid4 = Uuid::uuid4();
            $filename = $uuid4->getHex();
            $result = $storage->upload($file_to_upload['tmp_name'], $filename);
            if (!$result) {
                $msg = $this->CI->lang->line('error_upload_document');
                $this->set_status(ERROR_STORAGE, $msg, null, [
                    'error_code' => $storage->last_error_code(),
                    'error_description' => $storage->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $storage->last_error_code() . ' - ' . $storage->last_error_description() . ')');
                return;
            }

            // Set metadata
            $metadata = [];
            foreach ($data as $key => $value) {
                if (strtolower(substr($key, 0, 3)) === 'key') {
                    $vk = 'value' . substr($key, 3);
                    
                    // sanitize metadata
                    $value = sanitize_metadata($value);
                    
                    // add metadata key-value
                    $metadata['document_metadata'][$value] = $data[$vk];
                }
            }

            // Add document metadata
            $metadata['document_info'] = [
                'storage_filename' => $filename,
                'storage_filehandle' => $result->handle,
                'original_filename' => $file_to_upload['name'],
                'created' => date('Y-m-d H:i:s')
            ];

            // Add document data
            $metadata['data'] = base64_encode(file_get_contents($file_to_upload['tmp_name']));

            // Invoke DMS
            $dms = get_dms();
            $result = $dms->index_document($user['user_login'], $metadata);
            if (!$result) {
                $msg = $this->CI->lang->line('error_index_document');
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $storage->last_error_code(),
                    'error_description' => $storage->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $msg = $this->CI->lang->line('document_indexed_successfully');
            $this->set_status(ERROR_NONE, $msg, $result);
            log_message('info', $msg . ' (' . json_encode($result) . ')');    
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
        }
    }
    
    /**
     * Get document
     * @param $id Document Id
     */
    public function get_document($id) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Invoke DMS
            $dms = get_dms();
            $result = $dms->get_document($user['user_login'], $id);
            if (!$result) {
                $msg = $this->CI->lang->line('error_get_document');
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, '', $result);
            log_message('info', json_encode($result));    
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
        }        
    }
    
    /**
     * Delete document
     * @param $id Document Id
     */
    public function delete_document($id) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Invoke DMS
            $dms = get_dms();
            $result = $dms->delete_document($user['user_login'], $id);
            if (!$result) {
                $msg = $this->CI->lang->line('error_delete_document');
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, '', $result);
            log_message('info', json_encode($result));    
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
        }        
    }
    
    /**
     * Search documents
     * @param string $search_info Search info
     */
    public function search_documents($search_info) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Invoke DMS
            $dms = get_dms();
            $result = $dms->search_documents($user['user_login'], $search_info);
            if (!$result) {
                $msg = $this->CI->lang->line('error_search_documents');
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, '', $result);
            log_message('info', json_encode($result));    
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
        }
    }
    
    /**
     * Count documents
     * @param string $search_info Search info
     */
    public function count_documents($search_info) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }
            
            // Invoke DMS
            $dms = get_dms();
            $result = $dms->count_documents($user['user_login'], $search_info);
            if (!$result) {
                $msg = $this->CI->lang->line('error_count_documents');
                $this->set_status(ERROR_DMS, $msg, null, [
                    'error_code' => $dms->last_error_code(),
                    'error_description' => $dms->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $dms->last_error_code() . ' - ' . $dms->last_error_description() . ')');
                return;
            }

            // Handle result
            $this->set_status(ERROR_NONE, '', $result['count']);
            log_message('info', json_encode($result['count']));    
        } catch (Exception $e) {
            $this->set_status(ERROR_UNHANDLED, $e->getCode() . ' - ' . $e->getMessage());
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
        }
    }
    
    /**
     * Download document
     * @param string $storage_filehandle Storage file handle
     */
    public function get_document_url($storage_filehandle) {
        $this->reset_status();

        try {
            // Check auth
            $user = $this->CI->auth->get_user();
            if (!$user) {
                $msg = $this->CI->lang->line('unauthorized');
                $this->set_status(ERROR_AUTH, $msg);
                log_message('error', $msg);
                return;
            }

            // Check params
            if (!$storage_filehandle) {
                $msg = $this->CI->lang->line('error_missing_parameter_filename');
                $this->set_status(ERROR_PRECONDITION, $msg);
                log_message('error', $msg);
                return;
            }

            // Invoke storage
            $storage = get_storage();
            $document_url = $storage->get_file_url($storage_filehandle);
            if (!$document_url) {
                $msg = $this->CI->lang->line('error_getting_url_document');
                $this->set_status(ERROR_STORAGE, $msg, null, [
                    'error_code' => $storage->last_error_code(),
                    'error_description' => $storage->last_error_description()
                ]);
                log_message('error', $msg . ' (' . $storage->last_error_code() . ' - ' . $storage->last_error_description() . ')');
                return;
            }

            // Handle result
            $msg = $this->CI->lang->line('got_file_url') . ':' . $document_url;
            $this->set_status(ERROR_NONE, $msg, $document_url);
            log_message('info', $msg);
        } catch (Exception $e) {
            $msg = $e->getCode() . ' - ' . $e->getMessage();
            $this->set_status(ERROR_UNHANDLED, $msg);
            log_message('error', $msg);
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
