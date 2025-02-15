<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBankFieldsToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'bank_account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'bank_account_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['bank_name', 'bank_account_name', 'bank_account_number']);
    }
}
