<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankChangeRequestsTable extends Migration
{
    private static string $tableName = 'bank_change_requests';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'null' => false,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'employee_id' => [
                'type' => 'INT',
                'null' => false,
                'unsigned' => true
            ],
            'old_bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'new_bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'old_account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'new_account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'old_account_no' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'new_account_no' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        //CONSTRAINT fk_bank_change_request_employee_id FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE NO ACTION
        $this->forge->addForeignKey('employee_id', 'users', 'id', 'NO ACTION', 'RESTRICT', 'fk_bank_change_request_employee_id');
        $this->forge->createTable(self::$tableName, true);
    }

    public function down()
    {
        $this->forge->dropTable(self::$tableName, true);
    }
}
