<?php

class User_model_test extends TestCase {
    
    private $user_model;
    
    public function setUp() {
        $this->user_model = $this->newModel('User_model');
    }
    
    public function test_check_preload_user_sysadmin() {
        $result = $this->user_model->get([
            'user_login' => 'sysadmin',
            'user_password' => hash('sha256', '$istem' . USER_PASSWORD_SALT)
        ]);
        $this->assertEquals(1, count($result));
    }
    
    public function test_check_preload_user_user01() {
        $result = $this->user_model->get([
            'user_login' => 'user01',
            'user_password' => hash('sha256', '$pass' . USER_PASSWORD_SALT)
        ]);
        $this->assertEquals(1, count($result));
    }
    
}
