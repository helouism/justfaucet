<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClaimModel;
use CodeIgniter\Database\BaseConnection;
use IconCaptcha\Challenge\ValidationResult;
use IconCaptcha\IconCaptcha;

class Claim extends BaseController
{
    protected BaseConnection $db;
    protected $userModel;
    protected $claimModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new UserModel();
        $this->claimModel = new ClaimModel();
    }

    public function index(): string
    {
        return view('user/claim');
    }

    // STORE CLAIM ACTION - RESTful naming convention
    public function store()
    {
        $this->response->setHeader('Content-Type', 'application/json');


        $user_id = auth()->id();
        $ipAddress = $this->request->getIPAddress();

        if (!$this->claimModel->canUserIdClaimFaucet($user_id)) {
            return $this->response->setJSON([
                'error' => 'Please wait 5 minutes between claims.'
            ]);
        } else if (!$this->claimModel->canIpAddressNetworkClaimFaucet($ipAddress)) {
            return $this->response->setJSON([
                'error' => 'Multiple accounts on the same network is not allowed.'
            ]);
        }

        // Get user's current level and calculate claim amount
        $userData = $this->userModel->find($user_id);
        $level = (int) $userData->level;
        $claimAmount = 5 + ($level * 0.01);

        $claimData = [
            'user_id' => $user_id,
            'claim_amount' => $claimAmount,
            'ip_address' => $ipAddress
        ];

        $this->db->transStart();

        // Insert claim record
        $this->claimModel->insert($claimData);

        // Update claimer's points and exp
        $newExp = (int) $userData->exp + 1;
        $newLevel = floor($newExp / 100);

        $updateUserStats = [
            'points' => (float) $userData->points + $claimAmount,
            'exp' => $newExp,
            'level' => $newLevel
        ];
        $this->userModel->update($user_id, $updateUserStats);

        // Check if user was referred and add bonus to referrer
        $this->userModel->applyReferralBonus($user_id, $claimAmount);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'error' => 'Transaction failed'
            ]);
        }

        // Get updated balance
        $newBalance = $this->userModel->getBalance($user_id);

        $response = [
            'success' => 'Successfully claimed ' . number_format($claimAmount, 2) . ' points! and get 1 exp',
            'nextClaimTime' => time() + 300,
            'newBalance' => $newBalance,
            'exp' => $newExp,
            'level' => $newLevel,
            'nextLevelExp' => ($newLevel + 1) * 100
        ];

        // Check if level up occurred
        if ($newLevel > $level) {
            $response['levelUp'] = true;
            $response['newLevel'] = $newLevel;
        }

        return $this->response->setJSON($response);
    }

    // SHOW CLAIM STATUS - RESTful naming convention
    public function show()
    {
        $user_id = auth()->id();
        $ipAddress = $this->request->getIPAddress();

        $userData = $this->userModel->find($user_id);
        $exp = (int) $userData->exp;
        $level = (int) $userData->level;

        if ($this->claimModel->canUserIdClaimFaucet($user_id) && $this->claimModel->canIpAddressNetworkClaimFaucet($ipAddress)) {
            return $this->response->setJSON([
                'canClaim' => true,
                'balance' => $this->userModel->getBalance($user_id),
                'exp' => $exp,
                'level' => $level,
                'nextLevelExp' => ($level + 1) * 100
            ]);
        }

        // Use model method instead of direct query builder
        $nextClaimTime = $this->claimModel->getNextClaimTime($user_id);

        if ($nextClaimTime === null) {
            // If no claims found, user can claim immediately
            return $this->response->setJSON([
                'canClaim' => true,
                'balance' => $this->userModel->getBalance($user_id),
                'exp' => $exp,
                'level' => $level,
                'nextLevelExp' => ($level + 1) * 100
            ]);
        }

        return $this->response->setJSON([
            'canClaim' => false,
            'nextClaimTime' => $nextClaimTime,
            'balance' => $this->userModel->getBalance($user_id),
            'exp' => $exp,
            'level' => $level,
            'nextLevelExp' => ($level + 1) * 100
        ]);
    }

    /**
     * Validate the IconCaptcha
     */

}