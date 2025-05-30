<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClaimsTable extends Migration
{
    public function up()
    {
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
            'claim_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
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
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');

        $this->forge->createTable('claims');
    }

    public function down()
    {
        $this->forge->dropTable('claims');
    }
}