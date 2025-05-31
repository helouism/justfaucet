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
        'user_id' => 'required|integer',
        'amount' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[100000]',
        'status' => 'in_list[paid,failed]'
    ];
    protected $validationMessages = [];
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
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return []; // No withdrawals found for the user
        }

        return $query->getResultArray(); // Return all withdrawals as an array
    }




    // function to check if user can withdraw, minimum points = 2000
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

    // Get All Withdrawals
    public function getAllWithdrawals(): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('withdrawals.*, users.username');
        $builder->join('users', 'users.id = withdrawals.user_id', 'left');
        $builder->orderBy('withdrawals.created_at', 'DESC'); // Order by created_at in descending order
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return []; // No withdrawals found
        }

        return $query->getResultArray(); // Return all withdrawals as an array
    }

    public function convertPointsToUSD(int $points): float
    {
        // Assuming 1 USD = 10000 Points
        $conversionRate = 10000.0; // 1 USD = 10000 Points
        return $points / $conversionRate; // Convert points to USD

    }

}
