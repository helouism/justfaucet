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
    protected $allowedFields = ['user_id', 'amount', 'status', 'faucetpay_payout_id'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'amount' => [
            'label' => 'Amount',
            'rules' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[100000]',
            'errors' => [
                'required' => 'Amount is required.',
                'integer' => 'Amount must be an integer.',

                'greater_than_equal_to' => 'Amount must be at least 2000 points.',
                'less_than_equal_to' => 'Amount must not exceed 100000 points.'
            ]
        ],

    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.'
        ],
        'amount' => [
            'required' => 'Amount is required.',
            'integer' => 'Amount must be an integer.',
            'greater_than' => 'You do not have enough points',
            'greater_than_equal_to' => 'Amount must be at least 2000 points.',
            'less_than_equal_to' => 'Amount must not exceed 100000 points.'
        ],
        'status' => [
            'in_list' => 'Status must be either paid or failed.'
        ],
        'faucetpay_payout_id' => [
            'required' => 'FaucetPay payout ID is required.',
            'integer' => 'FaucetPay payout ID must be an integer.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get all withdrawals for a user
     *
     * @param int $userId User ID to filter withdrawals
     * @return array List of withdrawals for the user
     */
    public function getWithdrawalsByUser(int $userId): array
    {
        $builder = $this->db->table($this->table);
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC'); // Order by newest first
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return []; // No withdrawals found for the user
        }

        return $query->getResultArray(); // Return all withdrawals as an array
    }

    /**
     * Check if user can withdraw, minimum points = 2000
     *
     * @param int $userId User ID to check
     * @return bool True if user can withdraw, false otherwise
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
     * Get All Withdrawals with user information
     *
     * @return array List of all withdrawals with usernames
     */
    public function getAllWithdrawals(): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('withdrawals.*, users.username, users.email');
        $builder->join('users', 'users.id = withdrawals.user_id', 'left');
        $builder->orderBy('withdrawals.created_at', 'DESC'); // Order by created_at in descending order
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return []; // No withdrawals found
        }

        return $query->getResultArray(); // Return all withdrawals as an array
    }

    /**
     * Convert points to USD
     *
     * @param int $points Points to convert
     * @return float USD amount
     */
    public function convertPoints(int $points): float
    {
        // Conversion rate: 10,000 points = 1 USD
        // Adjust this rate according to your system's economics
        $conversionRate = 10000.0; // 10,000 points = 1 USD
        return round(($points / $conversionRate) * 100000000, 8); // Round to 8 decimal places for precision
    }

    /**
     * Get withdrawal statistics
     *
     * @return array Statistics about withdrawals
     */
    public function getWithdrawalStats(): array
    {
        $builder = $this->db->table($this->table);

        // Total withdrawals
        $totalWithdrawals = $builder->countAllResults();

        $builder = $this->db->table($this->table);

        // Successful withdrawals
        $builder->where('status', 'paid');
        $successfulWithdrawals = $builder->countAllResults();

        $builder = $this->db->table($this->table);

        // Failed withdrawals
        $builder->where('status', 'failed');
        $failedWithdrawals = $builder->countAllResults();

        $builder = $this->db->table($this->table);

        // Total amount withdrawn (successful only)
        $builder->select('SUM(amount) as total_amount');
        $builder->where('status', 'paid');
        $query = $builder->get();
        $totalAmount = $query->getRow()->total_amount ?? 0;

        return [
            'total_withdrawals' => $totalWithdrawals,
            'successful_withdrawals' => $successfulWithdrawals,
            'failed_withdrawals' => $failedWithdrawals,
            'total_points_withdrawn' => (int) $totalAmount,
            'total_usd_withdrawn' => $this->convertPointsToUSD((int) $totalAmount)
        ];
    }

    /**
     * Get user's withdrawal summary
     *
     * @param int $userId User ID
     * @return array User's withdrawal summary
     */
    public function getUserWithdrawalSummary(int $userId): array
    {
        $builder = $this->db->table($this->table);

        // Total withdrawals for user
        $builder->where('user_id', $userId);
        $totalWithdrawals = $builder->countAllResults();

        $builder = $this->db->table($this->table);

        // Successful withdrawals for user
        $builder->where('user_id', $userId);
        $builder->where('status', 'paid');
        $successfulWithdrawals = $builder->countAllResults();

        $builder = $this->db->table($this->table);

        // Total amount withdrawn by user (successful only)
        $builder->select('SUM(amount) as total_amount');
        $builder->where('user_id', $userId);
        $builder->where('status', 'paid');
        $query = $builder->get();
        $totalAmount = $query->getRow()->total_amount ?? 0;

        return [
            'total_withdrawals' => $totalWithdrawals,
            'successful_withdrawals' => $successfulWithdrawals,
            'total_points_withdrawn' => (int) $totalAmount,
            'total_usd_withdrawn' => $this->convertPointsToUSD((int) $totalAmount)
        ];
    }
}