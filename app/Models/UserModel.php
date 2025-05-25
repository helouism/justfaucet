<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        parent::initialize();

        $this->allowedFields = [
            ...$this->allowedFields,

            'referred_by',

        ];


    }
    // Get User Balance from points column in users table
    public function getBalance(int $userId): float
    {
        $builder = $this->db->table($this->table);
        $builder->select('points');
        $builder->where('id', $userId);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return 0.0; // User not found, return 0 balance
        }

        $row = $query->getRow();
        return (float) $row->points; // Return the points as a float
    }

    /**
     * Check if user was referred by another user
     *
     * @param int $userId The user ID to check
     * @return bool|array Returns false if not referred, or array with referrer data if referred
     */
    public function checkReferral(int $userId): bool|array
    {
        $builder = $this->db->table($this->table);
        $builder->select('users.*, referrer.username as referrer_username');
        $builder->join('users as referrer', 'users.referred_by = referrer.id', 'left');
        $builder->where('users.id', $userId);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return false;
        }

        $row = $query->getRow();

        if (empty($row->referred_by)) {
            return false;
        }

        return [
            'referrer_id' => $row->referred_by,
            'referrer_username' => $row->referrer_username,
            'referred_at' => $row->created_at // Assuming referral happened at user creation
        ];
    }

    /**
     * Get all referrals for a user
     *
     * @param int $userId The referrer's user ID
     * @return array Array of users referred by this user
     */
    public function getReferrals(int $userId): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, username, created_at, last_active, level');
        $builder->where('referred_by', $userId);
        $builder->orderBy('created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Count total referrals for a user
     *
     * @param int $userId The referrer's user ID
     * @return int Number of referrals
     */
    public function countReferrals(int $userId): int
    {
        return $this->where('referred_by', $userId)->countAllResults();
    }

    public function getExpToNextLevel(int $userId): int
    {
        // Assuming each level requires 100 exp to level up
        $builder = $this->db->table($this->table);
        $builder->select('exp, level');
        $builder->where('id', $userId);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return 0; // User not found
        }

        $row = $query->getRow();
        return (int) (($row->level + 1) * 100 - $row->exp); // Calculate exp needed for next level
    }
}
