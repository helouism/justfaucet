<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'points' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'after' => 'deleted_at',
            ],
            'exp' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'points',
            ],
            'level' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'exp',
            ],
            'referred_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'level',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add index for referred_by
        $this->forge->addKey('referred_by');

        // Add foreign key constraint
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_referred_by_foreign FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY users_referred_by_foreign');

        // Drop the custom columns
        $this->forge->dropColumn('users', ['points', 'exp', 'level', 'referred_by']);
    }
}