<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWithdrawalsTable extends Migration
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
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'comment' => 'Amount in points'
            ],
            'usdt_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,8',
                'null' => true,
                'comment' => 'Amount in USDT'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed', 'cancelled'],
                'default' => 'pending',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'FaucetPay email address'
            ],
            'faucetpay_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'FaucetPay transaction reference'
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Error message if withdrawal failed'
            ],
            'response_data' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Full API response from FaucetPay'
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the withdrawal was processed'
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the withdrawal was cancelled'
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
                'comment' => 'Soft delete timestamp'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addKey('faucetpay_reference');

        // Add foreign key constraint
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('withdrawals');

        // Add indexes for better performance
        $this->db->query('CREATE INDEX idx_withdrawals_user_status ON withdrawals (user_id, status)');
        $this->db->query('CREATE INDEX idx_withdrawals_status_created ON withdrawals (status, created_at)');
    }

    public function down()
    {
        $this->forge->dropTable('withdrawals');
    }
}