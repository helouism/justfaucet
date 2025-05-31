<?php

namespace App\Models;

use CodeIgniter\Model;

class FraudUserModel extends Model
{
    protected $table = 'fraud_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'abuse_type',
        'ip_address',
        'detection_method',
        'severity_level'
    ];

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
        'abuse_type' => 'required|string|max_length[100]',
        'ip_address' => 'permit_empty|valid_ip',
        'severity_level' => 'permit_empty|in_list[low,medium,high,critical]'
    ];
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.'
        ],
        'abuse_type' => [
            'required' => 'Abuse type is required.',
            'string' => 'Abuse type must be a string.',
            'max_length' => 'Abuse type cannot exceed 100 characters.'
        ],
        'ip_address' => [
            'valid_ip' => 'IP address must be valid.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setSeverityLevel'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Check if user is flagged as fraud
     * 
     * @param int $userId The user ID to check
     * @return bool True if user is flagged as fraud
     */
    public function isUserFraud(int $userId): bool
    {
        // Get the latest high/critical severity entry
        $latestFraud = $this->where('user_id', $userId)
            ->where("(severity_level = 'high' OR severity_level = 'critical')")
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($latestFraud) {
            // Get the current user and ban them
            $user = auth()->user();
            if ($user) {
                $user->ban($latestFraud['abuse_type']);
            }
            return true;
        }

        return false;
    }







    /**
     * Get fraud statistics by abuse type
     * 
     * @param int $days Number of days to look back (default 30)
     * @return array Statistics by abuse type
     */
    public function getFraudStatsByType(int $days = 30): array
    {
        $startDate = date('Y-m-d H:i:s', time() - ($days * 86400));

        return $this->select('abuse_type, COUNT(*) as count, COUNT(DISTINCT user_id) as unique_users')
            ->where('created_at >=', $startDate)
            ->groupBy('abuse_type')
            ->orderBy('count', 'DESC')
            ->findAll();
    }

    /**
     * Get top fraud IPs
     * 
     * @param int $days Number of days to look back
     * @param int $limit Number of IPs to return
     * @return array Top fraud IPs
     */
    public function getTopFraudIPs(int $days = 7, int $limit = 20): array
    {
        $startDate = date('Y-m-d H:i:s', time() - ($days * 86400));

        return $this->select('ip_address, COUNT(*) as count, COUNT(DISTINCT user_id) as unique_users')
            ->where('created_at >=', $startDate)
            ->where('ip_address IS NOT NULL')
            ->groupBy('ip_address')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Check if IP address has suspicious activity
     * 
     * @param string $ipAddress The IP address to check
     * @param int $hours Hours to look back (default 24)
     * @return array IP risk assessment
     */
    public function assessIPRisk(string $ipAddress, int $hours = 24): array
    {
        $startDate = date('Y-m-d H:i:s', time() - ($hours * 3600));

        $reports = $this->where('ip_address', $ipAddress)
            ->where('created_at >=', $startDate)
            ->findAll();

        $uniqueUsers = array_unique(array_column($reports, 'user_id'));
        $abuseTypes = array_count_values(array_column($reports, 'abuse_type'));

        $riskLevel = 'low';
        if (count($reports) > 10) {
            $riskLevel = 'high';
        } elseif (count($reports) > 5) {
            $riskLevel = 'medium';
        }

        return [
            'ip_address' => $ipAddress,
            'total_reports' => count($reports),
            'unique_users' => count($uniqueUsers),
            'risk_level' => $riskLevel,
            'abuse_types' => $abuseTypes,
            'timeframe_hours' => $hours
        ];
    }


    /**
     * Callback to set severity level before insert
     */
    protected function setSeverityLevel(array $data): array
    {
        if (isset($data['data']['severity_level'])) {
            return $data; // Already set
        }

        $abuseType = $data['data']['abuse_type'] ?? '';

        // Set severity based on abuse type
        switch ($abuseType) {
            case 'Using VPN/Proxy/Tor':
                $data['data']['severity_level'] = 'high';
                break;
            case 'Using multiple accounts':
                $data['data']['severity_level'] = 'high';
                break;
            case 'Use scripts':
                $data['data']['severity_level'] = 'medium';
                break;
            case 'Manual block':
                $data['data']['severity_level'] = 'critical';
                break;
            default:
                $data['data']['severity_level'] = 'low';
                break;
        }

        return $data;
    }


}