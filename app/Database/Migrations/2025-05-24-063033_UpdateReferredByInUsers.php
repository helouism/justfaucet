<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
class UpdateReferredByInUsers extends Migration
{
    public function up()
    {

        // Add the referred_by column with foreign key constraint
        $fields = [
            'referred_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ];

        $this->forge->addColumn('users', $fields);

        // Add Foreign Key
        $this->forge->addForeignKey('referred_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->processIndexes('users');
    }

    public function down()
    {


        // Drop the referred_by column
        $this->forge->dropColumn('users', 'referred_by');
    }
}
