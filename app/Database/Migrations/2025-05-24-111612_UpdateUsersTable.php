<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        //
        $fields = [
            'points' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ]
        ];
        $this->forge->modifyColumn('users', $fields);
    }


    public function down()
    {
        //
        $this->forge->dropColumn('users', 'points');
    }
}
