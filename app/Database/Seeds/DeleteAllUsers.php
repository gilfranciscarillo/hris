<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeleteAllUsers extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        $this->db->table('auth_groups_users')->truncate();
        $this->db->table('auth_identities')->truncate();
        $this->db->table('auth_logins')->truncate();
        $this->db->table('auth_permissions_users')->truncate();
        $this->db->table('auth_remember_tokens')->truncate();
        $this->db->table('auth_token_logins')->truncate();
        $this->db->table('users')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}
