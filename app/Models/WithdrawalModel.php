<?php

namespace App\Models;

use CodeIgniter\Model;

class WithdrawalModel extends Model
{
    protected $table = 'withdrawals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'amount',
        'usdt_amount',
        'status',
        'email',
        'faucetpay_reference',
        'error_message',
        'response_data',
        'processed_at',
        'cancelled_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'amount' => 'decimal',
        'usdt_amount' => 'decimal',
        'user_id' => 'integer'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'amount' => 'required|decimal|greater_than[0]',
        'usdt_amount' => 'permit_empty|decimal|greater_than[0]',
        'status' => 'in_list[pending,completed,failed,cancelled]',
        'email' => 'permit_empty|valid_email'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be an integer'
        ],
        'amount' => [
            'required' => 'Amount is required',
            'decimal' => 'Amount must be a valid decimal number',
            'greater_than' => 'Amount must be greater than 0'
        ],
        'status' => [
            'in_list' => 'Status must be one of: pending, completed, failed, cancelled'
        ],
        'email' => [
            'valid_email' => 'Please provide a valid email address'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Set default values before insert
     */
    protected function setDefaultValues(array $data): array
    {
        if (isset($data['data']['status']) && empty($data['data']['status'])) {
            $data['data']['status'] = 'pending';
        }

        return $data;
    }

    /**
     * Get all withdrawals for a user with proper ordering
     *
     * @param int $userId User ID to filter withdrawals
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of withdrawals for the user
     */
    public function getWithdrawalsByUser(int $userId, int $limit = 50, int $offset = 0): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Get withdrawal by ID and user ID for security
     *
     * @param int $withdrawalId Withdrawal ID
     * @param int $userId User ID for security check
     * @return array|null Withdrawal data or null if not found
     */
    public function getWithdrawalByUser(int $withdrawalId, int $userId): ?array
    {
        return $this->where('id', $withdrawalId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Check if user can withdraw (minimum points = 2000)
     *
     * @param int $userId User ID to check
     * @return bool True if user can withdraw
     */
    public function canWithdraw(int $userId): bool
    {
        $builder = $this->db->table('users');
        $builder->select('points');
        $builder->where('id', $userId);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return false; // User not found
        }

        $row = $query->getRow();
        return (float) $row->points >= 2000.0; // Check if points are at least 2000
    }

    /**
     * Get pending withdrawals count for a user
     *
     * @param int $userId User ID
     * @return int Number of pending withdrawals
     */
    public function getPendingWithdrawalsCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('status', 'pending')
            ->countAllResults();
    }

    /**
     * Get total withdrawn amount for a user
     *
     * @param int $userId User ID
     * @param string $status Filter by status (optional)
     * @return float Total withdrawn amount
     */
    public function getTotalWithdrawnByUser(int $userId, string $status = 'completed'): float
    {
        $builder = $this->where('user_id', $userId);

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $result = $builder->selectSum('amount', 'total')->first();

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Get withdrawal statistics for a user
     *
     * @param int $userId User ID
     * @return array Statistics including total, completed, pending, failed counts and amounts
     */
    public function getWithdrawalStats(int $userId): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            COUNT(*) as total_count,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
            SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_completed_amount,
            SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as total_pending_amount
        ');
        $builder->where('user_id', $userId);

        $result = $builder->get()->getRowArray();

        return [
            'total_withdrawals' => (int) $result['total_count'],
            'completed_count' => (int) $result['completed_count'],
            'pending_count' => (int) $result['pending_count'],
            'failed_count' => (int) $result['failed_count'],
            'cancelled_count' => (int) $result['cancelled_count'],
            'total_completed_amount' => (float) $result['total_completed_amount'],
            'total_pending_amount' => (float) $result['total_pending_amount']
        ];
    }

    /**
     * Get recent withdrawals across all users (for admin)
     *
     * @param int $limit Number of records to return
     * @param string|null $status Filter by status
     * @return array Recent withdrawals with user information
     */
    public function getRecentWithdrawals(int $limit = 20, ?string $status = null): array
    {
        $builder = $this->db->table($this->table . ' w');
        $builder->select('w.*, u.username, u.email as user_email');
        $builder->join('users u', 'u.id = w.user_id', 'left');

        if ($status) {
            $builder->where('w.status', $status);
        }

        $builder->orderBy('w.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Update withdrawal status with timestamp
     *
     * @param int $withdrawalId Withdrawal ID
     * @param string $status New status
     * @param array $additionalData Additional data to update
     * @return bool Success status
     */
    public function updateStatus(int $withdrawalId, string $status, array $additionalData = []): bool
    {
        $updateData = array_merge($additionalData, ['status' => $status]);

        // Add appropriate timestamp based on status
        switch ($status) {
            case 'completed':
                $updateData['processed_at'] = date('Y-m-d H:i:s');
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = date('Y-m-d H:i:s');
                break;
        }

        return $this->update($withdrawalId, $updateData);
    }
}