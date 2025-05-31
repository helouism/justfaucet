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
     * Enhanced multiple account detection
     * Checks various patterns to detect multi-accounting
     */
    public function checkMultipleAccounts(int $userId, string $ipAddress): array
    {
        // 1. Check network-based claims (existing functionality)
        if (!$this->canIpAddressNetworkClaimFaucet($ipAddress)) {
            return [
                'allowed' => false,
                'reason' => 'Network cooldown active'
            ];
        }

        // 2. Check for suspicious patterns
        $suspiciousPatterns = $this->detectSuspiciousPatterns($userId, $ipAddress);
        if (!empty($suspiciousPatterns)) {
            return [
                'allowed' => false,
                'reason' => 'Suspicious activity detected: ' . implode(', ', $suspiciousPatterns)
            ];
        }

        // 3. Check account age and claim frequency
        $accountRisk = $this->assessAccountRisk($userId, $ipAddress);
        if ($accountRisk['risk_level'] === 'high') {
            return [
                'allowed' => false,
                'reason' => 'High risk account behavior: ' . $accountRisk['reason']
            ];
        }

        return ['allowed' => true, 'reason' => 'All checks passed'];
    }

    /**
     * Detect suspicious patterns that might indicate multi-accounting
     */
    private function detectSuspiciousPatterns(int $userId, string $ipAddress): array
    {
        $patterns = [];

        // Pattern 1: Too many different users from same network in short time
        $networkUsers = $this->getNetworkUsersInTimeframe($ipAddress, 3600); // 1 hour
        if (count($networkUsers) > 3) { // More than 3 different users in 1 hour
            $patterns[] = 'Multiple users from same network';
        }

        // Pattern 2: Sequential user IDs from same network (possible mass registration)
        $recentNetworkUsers = $this->getNetworkUsersInTimeframe($ipAddress, 86400); // 24 hours
        if ($this->hasSequentialUserIds($recentNetworkUsers)) {
            $patterns[] = 'Sequential account creation detected';
        }

        // Pattern 3: Identical claim timing patterns
        if ($this->hasIdenticalTimingPatterns($userId, $ipAddress)) {
            $patterns[] = 'Identical timing patterns detected';
        }

        // Pattern 4: Same browser fingerprint (if available)
        // This would require storing browser fingerprint data

        return $patterns;
    }

    /**
     * Get all users who claimed from the same network in a timeframe
     */
    private function getNetworkUsersInTimeframe(string $ipAddress, int $seconds): array
    {
        $networkRange = $this->getNetworkRange($ipAddress);

        $builder = $this->db->table($this->table);
        $builder->select('user_id, ip_address, created_at');
        $builder->distinct();

        if ($networkRange['type'] === 'ipv4') {
            $builder->where("INET_ATON(ip_address) >= {$networkRange['start']}");
            $builder->where("INET_ATON(ip_address) <= {$networkRange['end']}");
        } else {
            $builder->like('ip_address', $networkRange['prefix'], 'after');
        }

        $builder->where('created_at >=', date('Y-m-d H:i:s', time() - $seconds));
        $builder->orderBy('created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if user IDs are sequential (indicating mass registration)
     */
    private function hasSequentialUserIds(array $users): bool
    {
        if (count($users) < 3)
            return false;

        $userIds = array_column($users, 'user_id');
        sort($userIds);

        $sequential = 0;
        for ($i = 1; $i < count($userIds); $i++) {
            if ($userIds[$i] - $userIds[$i - 1] === 1) {
                $sequential++;
            }
        }

        // If more than 50% of users have sequential IDs, it's suspicious
        return ($sequential / (count($userIds) - 1)) > 0.5;
    }

    /**
     * Check for identical timing patterns between accounts
     */
    private function hasIdenticalTimingPatterns(int $userId, string $ipAddress): bool
    {
        // Get recent claim times for current user
        $userClaims = $this->getUserRecentClaims($userId, 10);
        if (count($userClaims) < 3)
            return false;

        // Get recent claims from same network by other users
        $networkUsers = $this->getNetworkUsersInTimeframe($ipAddress, 86400);

        foreach ($networkUsers as $networkUser) {
            if ($networkUser['user_id'] == $userId)
                continue;

            $otherUserClaims = $this->getUserRecentClaims($networkUser['user_id'], 10);
            if (count($otherUserClaims) < 3)
                continue;

            // Check if timing patterns are too similar
            if ($this->calculateTimingSimilarity($userClaims, $otherUserClaims) > 0.8) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get recent claims for a user
     */
    private function getUserRecentClaims(int $userId, int $limit): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('created_at');
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Calculate similarity between two sets of claim times
     */
    private function calculateTimingSimilarity(array $claims1, array $claims2): float
    {
        if (count($claims1) < 2 || count($claims2) < 2)
            return 0;

        // Calculate intervals between claims
        $intervals1 = $this->calculateIntervals($claims1);
        $intervals2 = $this->calculateIntervals($claims2);

        if (empty($intervals1) || empty($intervals2))
            return 0;

        // Compare intervals - simplified similarity calculation
        $minCount = min(count($intervals1), count($intervals2));
        $matches = 0;

        for ($i = 0; $i < $minCount; $i++) {
            $diff = abs($intervals1[$i] - $intervals2[$i]);
            if ($diff < 60) { // Within 1 minute
                $matches++;
            }
        }

        return $matches / $minCount;
    }

    /**
     * Calculate intervals between claims
     */
    private function calculateIntervals(array $claims): array
    {
        if (count($claims) < 2)
            return [];

        $intervals = [];
        for ($i = 1; $i < count($claims); $i++) {
            $time1 = strtotime($claims[$i - 1]['created_at']);
            $time2 = strtotime($claims[$i]['created_at']);
            $intervals[] = abs($time1 - $time2);
        }

        return $intervals;
    }

    /**
     * Assess account risk level
     */
    private function assessAccountRisk(int $userId, string $ipAddress): array
    {
        $riskFactors = [];

        // Check account age vs claim frequency
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->find($userId);

        if ($userData) {
            $accountAge = time() - strtotime($userData->created_at);
            $totalClaims = $this->getTotalClaimsCount($userId);

            // Very new account with many claims
            if ($accountAge < 3600 && $totalClaims > 5) { // Less than 1 hour old, more than 5 claims
                $riskFactors[] = 'New account with high activity';
            }

            // Unrealistic claim frequency
            if ($accountAge > 0) {
                $claimsPerHour = ($totalClaims * 3600) / $accountAge;
                if ($claimsPerHour > 10) { // More than 10 claims per hour on average
                    $riskFactors[] = 'Unrealistic claim frequency';
                }
            }
        }

        // Check IP reputation
        $ipClaims = $this->getIpClaimsInTimeframe($ipAddress, 86400); // 24 hours
        if (count($ipClaims) > 50) { // More than 50 claims from this IP in 24 hours
            $riskFactors[] = 'High IP activity';
        }

        $riskLevel = count($riskFactors) > 0 ? 'high' : 'low';

        return [
            'risk_level' => $riskLevel,
            'reason' => implode(', ', $riskFactors),
            'factors' => $riskFactors
        ];
    }

    /**
     * Get all claims from an IP in a timeframe
     */
    private function getIpClaimsInTimeframe(string $ipAddress, int $seconds): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_id, created_at');
        $builder->where('ip_address', $ipAddress);
        $builder->where('created_at >=', date('Y-m-d H:i:s', time() - $seconds));

        return $builder->get()->getResultArray();
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
     * Get the number of claims made by a user in the current UTC day.
     *
     * @param int $userId The user ID to check.
     * @return int Number of claims made today (00:00:00 UTC to 23:59:59 UTC)
     */
    public function claimChallenge(int $userId): int
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(*) as total_claims');
        $builder->where('user_id', $userId);
        // Get today's date in UTC
        $utcStartOfDay = gmdate('Y-m-d 00:00:00');
        $utcEndOfDay = gmdate('Y-m-d 23:59:59');
        $builder->where('created_at >=', $utcStartOfDay);
        $builder->where('created_at <=', $utcEndOfDay);
        $query = $builder->get();

        $row = $query->getRow();
        return $row ? (int) $row->total_claims : 0;
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