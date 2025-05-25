<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;


class AddReferredByToUsers extends Migration
{

    public function up()
    {
        $fields = [
            'referred_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'points' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'exp' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'level' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'referred_by');
        $this->forge->dropColumn('users', 'points');
        $this->forge->dropColumn('users', 'exp');
        $this->forge->dropColumn('users', 'level');
    }
}
