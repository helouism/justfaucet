<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NewWithdrawalTable extends Migration
{
    public function up()
    {

        //
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME DEFAULT NULL',
        ];
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addField($fields);
        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('withdrawals');
    }

    public function down()
    {
        //
        $this->forge->dropTable('withdrawals', true);
    }
}
