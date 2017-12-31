<?php

class Auth_library_test extends TestCase {
    
    const ADMIN_USERNAME = 'sysadmin';
    const ADMIN_RIGHT_PASSWORD = '$istem';
    const DEFAULT_USERNAME = 'user01';
    const DEFAULT_PASSWORD = '$pass';
    const ADMIN_WRONG_PASSWORD = 'this_is_a_wrong_password';
    const RIGHT_AUTHORIZATION_HEADER = 'Basic c3lzYWRtaW46JGlzdGVt';
    const WRONG_AUTHORIZATION_HEADER = 'Basic c3lzYWRtaW46JGlZZZZZZZZ';
    
    private $auth;
    
    public function setUp() {
        $this->request->setHeader('Authorization', '');
        $this->auth = $this->newLibrary('Auth');
    }

    public function test_get_user_by_right_credential() {
        $user = $this->auth->get_user(self::ADMIN_USERNAME, self::ADMIN_RIGHT_PASSWORD);
        $this->assertNotNull($user);
    }
    
    public function test_get_user_by_wrong_credential() {
        $user = $this->auth->get_user(self::ADMIN_USERNAME, self::ADMIN_WRONG_PASSWORD);
        $this->assertNull($user);
    }
    
    public function test_get_user_by_right_authorization_header() {
        $this->request->setHeader('Authorization', self::RIGHT_AUTHORIZATION_HEADER);
        $user = $this->auth->get_user();
        $this->assertNotNull($user);
    }
    
    public function test_get_user_by_wrong_authorization_header() {
        $this->request->setHeader('Authorization', self::WRONG_AUTHORIZATION_HEADER);
        $user = $this->auth->get_user();
        $this->assertNull($user);
    }
    
    public function test_get_user_by_session() {
        $this->warningOff();
        set_is_cli(false);
        
        $this->set_user_in_session();
        $user = $this->auth->get_user();
        
        session_destroy();
        
        $this->warningOn();
        set_is_cli(true);
        
        $this->assertNotNull($user);
    }
    
    public function test_system_is_admin() {
        $user = $this->auth->get_user(self::ADMIN_USERNAME, self::ADMIN_RIGHT_PASSWORD);
        $this->assertTrue($this->auth->is_user_admin($user));
    }
    
    public function test_user01_is_not_admin() {
        $user = $this->auth->get_user(self::DEFAULT_USERNAME, self::DEFAULT_PASSWORD);
        $this->assertFalse($this->auth->is_user_admin($user));
    }
    
    private function set_user_in_session() {
        $user = $this->auth->get_user(self::ADMIN_USERNAME, self::ADMIN_RIGHT_PASSWORD);
        if ($user) {
            $this->CI->session->set_userdata(['logged_user' => $user]);
        }
    }
    
}
