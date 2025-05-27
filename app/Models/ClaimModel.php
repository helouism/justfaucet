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
    protected $allowedFields = ['user_id', 'claim_amount', 'ip_address'];

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
        'claim_amount' => 'required|decimal',
        'ip_address' => 'required|valid_ip'
    ];
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.'
        ],
        'claim_amount' => [
            'required' => 'Claim amount is required.',
            'decimal' => 'Claim amount must be a decimal value.'
        ],
        'ip_address' => [
            'required' => 'IP address is required.',
            'valid_ip' => 'IP address must be valid.'
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

    // Check if user ID can claim faucet, Faucet can be claimed every 5 minutes
    public function canUserIdClaimFaucet(int $userId): bool
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

    /**
     * Get the next claim time for a user (moved from controller)
     *
     * @param int $userId The user ID
     * @return int|null Next claim timestamp or null if no previous claims
     */
    public function getNextClaimTime(int $userId): ?int
    {
        $builder = $this->db->table($this->table);
        $builder->select('created_at');
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit(1);
        $lastClaim = $builder->get()->getRow();

        if (!$lastClaim) {
            return null; // No claims found
        }

        return strtotime($lastClaim->created_at) + 300;
    }

    /**
     * Get the last claim for a user
     *
     * @param int $userId The user ID
     * @return object|null Last claim data or null
     */
    public function getLastClaim(int $userId): ?object
    {
        $builder = $this->db->table($this->table);
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit(1);

        return $builder->get()->getRow();
    }

    /**
     * Get total claims count for a user
     *
     * @param int $userId The user ID
     * @return int Total claims count
     */
    public function getTotalClaimsCount(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Get total amount claimed by a user
     *
     * @param int $userId The user ID
     * @return float Total amount claimed
     */
    public function getTotalClaimedAmount(int $userId): float
    {
        $builder = $this->db->table($this->table);
        $builder->select('SUM(claim_amount) as total');
        $builder->where('user_id', $userId);
        $result = $builder->get()->getRow();

        return $result ? (float) $result->total : 0.0;
    }

    /**
     * Get claims history for a user with pagination
     *
     * @param int $userId The user ID
     * @param int $limit Number of records per page
     * @param int $offset Starting record offset
     * @return array Claims history
     */
    public function getClaimsHistory(int $userId, int $limit = 10, int $offset = 0): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('claim_amount, created_at, ip_address');
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResultArray();
    }

    /**
     * Check if the given IP address (or its network) can claim the faucet.
     * Faucet can be claimed every 5 minutes per network to prevent abuse.
     *
     * @param string $ipAddress The IP address to check.
     * @return bool True if the IP address/network can claim, false otherwise.
     */
    public function canIpAddressNetworkClaimFaucet(string $ipAddress): bool
    {
        // Get the network range for the IP address
        $networkRange = $this->getNetworkRange($ipAddress);

        $builder = $this->db->table($this->table);
        $builder->select('created_at, ip_address');

        // Check for any claims from the same network range
        if ($networkRange['type'] === 'ipv4') {
            $builder->where("INET_ATON(ip_address) >= {$networkRange['start']}");
            $builder->where("INET_ATON(ip_address) <= {$networkRange['end']}");
        } else {
            // For IPv6, we'll use a simpler prefix match approach
            $builder->like('ip_address', $networkRange['prefix'], 'after');
        }

        $builder->orderBy('created_at', 'DESC');
        $builder->limit(1);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return true; // No previous claims from this network, so can claim
        }

        $row = $query->getRow();
        $lastClaimed = strtotime($row->created_at);
        $currentTime = time();

        // Check if 5 minutes (300 seconds) have passed
        return ($currentTime - $lastClaimed) >= 300;
    }

    /**
     * Get the network range for an IP address.
     * Uses /24 subnet for IPv4 (Class C) and /64 for IPv6.
     *
     * @param string $ipAddress The IP address.
     * @return array Network range information.
     */
    private function getNetworkRange(string $ipAddress): array
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // IPv4 handling - use /24 subnet (Class C network)
            $ip = ip2long($ipAddress);
            $subnet = 24; // /24 means 256 addresses in the same network
            $mask = -1 << (32 - $subnet);
            $networkStart = $ip & $mask;
            $networkEnd = $networkStart + pow(2, (32 - $subnet)) - 1;

            return [
                'type' => 'ipv4',
                'start' => $networkStart,
                'end' => $networkEnd,
                'network' => long2ip($networkStart) . '/' . $subnet
            ];
        } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // IPv6 handling - use /64 subnet
            $ipParts = explode(':', $ipAddress);
            // Take first 4 groups (64 bits) for network identification
            $networkPrefix = implode(':', array_slice($ipParts, 0, 4));

            return [
                'type' => 'ipv6',
                'prefix' => $networkPrefix,
                'network' => $networkPrefix . '::/64'
            ];
        }

        // Fallback to exact IP match if IP format is not recognized
        return [
            'type' => 'exact',
            'ip' => $ipAddress
        ];
    }
}