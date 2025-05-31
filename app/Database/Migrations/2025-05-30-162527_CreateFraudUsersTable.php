<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFraudUsersTable extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
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
            'abuse_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'detection_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'severity_level' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'DEFAULT' => 'low',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->createTable('fraud_users');
    }

    public function down()
    {
        //
        $this->forge->dropTable('fraud_users');
    }
}
