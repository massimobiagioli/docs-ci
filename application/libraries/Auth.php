<?php

/**
 * Authentication
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Auth {

    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    /**
     * Return Current user
     * @param string $username Username
     * @param string $password Password
     * @return array User
     */
    public function get_user($username = null, $password = null) {
        // 1. Check user by username and password
        if ($username !== null && $password !== null) {
            return $this->load_user($username, $password);
        }

        // 2. Check the user in request header
        $headers = $this->CI->input->request_headers();
        $basic_auth_str = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        if ($basic_auth_str) {
            $basic_auth_info = explode(' ', $basic_auth_str);
            list($username, $password) = explode(':', base64_decode($basic_auth_info[1]));
            return $this->load_user($username, $password);
        }

        // 3. Check thr user in session
        return $this->CI->session->userdata('logged_user');
    }

    /**
     * Check if the user in administrator
     * @param string $user User data
     * @return TRUE/FALSE
     */
    public function is_user_admin($user) {
        return $user['user_admin'] == 1;
    }
    
    /**
     * Check if the request is made by CodeIgniter
     * @return TRUE/FALSE
     */
    public function is_ci_request() {
        $headers = $this->CI->input->request_headers();
        return (isset($headers['X-Api-Key']));
    }
    
    private function load_user($username, $password) {
        $this->CI->load->model('user_model');
        $result = $this->CI->user_model->get([
            'user_login' => $username,
            'user_password' => hash('sha256', $password . USER_PASSWORD_SALT)
        ]);
        if (count($result) === 0) {
            return null;
        }
        return $result[0];
    }

}
