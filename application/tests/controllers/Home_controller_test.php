<?php

class Home_controller_test extends TestCase {

    public function test_index_redirect() {
        $this->request('GET', 'home/index');
        $this->assertRedirect('/', 302);
    }

}
