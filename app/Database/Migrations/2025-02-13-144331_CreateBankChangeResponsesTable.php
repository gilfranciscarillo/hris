<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankChangeResponsesTable extends Migration
{
    private static string $tableName = 'bank_change_responses';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true
            ],
            'request_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'approver_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Approved', 'Rejected'],
                'null' => false,
                'default' => 'Pending'
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('request_id', 'bank_change_requests', 'id', 'NO ACTION', 'RESTRICT', 'fk_bank_change_responses_request_id');
        $this->forge->addForeignKey('approver_id', 'users', 'id', 'NO ACTION', 'RESTRICT', 'fk_users_approver_id');
        $this->forge->createTable(self::$tableName, true);
    }

    public function down()
    {
        $this->forge->dropTable(self::$tableName, true);
    }
}
