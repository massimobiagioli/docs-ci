<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_user_table extends CI_Migration {

    public function up() {
        // Create user table
        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_login' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'user_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'user_admin' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'constraint' => '255',
            ),
        ));
        $this->dbforge->add_key('user_id', TRUE);
        $this->dbforge->create_table('user');
        
        // Add users
        $data = [
            [
                'user_login' => 'sysadmin',
                'user_password' => hash('sha256', '$istem' . USER_PASSWORD_SALT),
                'user_admin' => 1
            ],
            [
                'user_login' => 'user01',
                'user_password' => hash('sha256', '$pass' . USER_PASSWORD_SALT),
                'user_admin' => 0
            ]
        ];
        $this->db->insert_batch('user', $data);
    }

    public function down() {
        $this->dbforge->drop_table('user');
    }

}
