<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateClaimsTable extends Migration
{
    public function up()
    {
        //
        $fields = [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ];
        $this->forge->addColumn('claims', $fields);
    }

    public function down()
    {
        //
    }
}
