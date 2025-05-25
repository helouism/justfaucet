<?php

namespace App\Models;

use CodeIgniter\Model;

class ClaimModel extends Model
{
    protected $table = 'claims';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'claim_amount']; // Add allowed fields

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
    protected $validationRules = [];
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


    // Check if user can claim faucet, Faucet can be claimed every 5 minutes
    public function canClaimFaucet(int $userId): bool
    {
        $builder = $this->db->table($this->table);
        $builder->select('created_at');
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit(1);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return true; // No previous claims, so can claim
        }

        $row = $query->getRow();
        $lastClaimed = strtotime($row->created_at);
        $currentTime = time();

        // Check if 5 minutes (300 seconds) have passed
        return ($currentTime - $lastClaimed) >= 300;
    }
}


