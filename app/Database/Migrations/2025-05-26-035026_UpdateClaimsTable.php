<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateClaimsTable extends Migration
{
    public function up()
    {
        $fields = [
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ]
        ];
        $this->forge->addColumn('claims', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('claims', 'ip_address');
    }
}
