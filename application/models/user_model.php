<?php

class User_model extends CI_model {
    
    protected $table = 'user';
    protected $primary_key = 'user_id';
    
    public function get($id = null) {
        if (is_numeric($id)) {
            $this->db->where($this->primary_key, $id);    
        } elseif (is_array($id)) {
            foreach ($id as $k => $v) {
                $this->db->where($k, $v);    
            }
        }
        
        $query = $this->db->get($this->table);
        if (!$query) {
            return null;
        }
        
        return $query->result_array();
    }
    
}
