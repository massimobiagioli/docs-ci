<?php

class Login_controller_test extends TestCase {

    public function test_index() {
        $output = $this->request('GET', 'login/index');
        $this->assertContains('<form id="form-login"', $output);
    }

}
