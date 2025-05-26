<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClaimModel;
use CodeIgniter\Database\BaseConnection;

class ClaimController extends BaseController
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index(): string
    {
        return view('user/claim');
    }

    // CLAIM ACTION
    public function action()
    {

        $this->response->setHeader('Content-Type', 'application/json');

        $user_id = auth()->id();
        $ipAddress = $this->request->getIPAddress();
        $claimModel = new ClaimModel();
        $userModel = new UserModel();

        if (!$claimModel->canUserIdClaimFaucet($user_id)) {
            return $this->response->setJSON([
                'error' => 'Please wait 5 minutes between claims.'
            ]);
        } else if (!$claimModel->canIpAddressNetworkClaimFaucet($ipAddress)) {
            return $this->response->setJSON([
                'error' => 'Multiple accounts on the same network is not allowed.'
            ]);
        }

        // Get user's current level and calculate claim amount
        $userData = $userModel->find($user_id);
        $level = (int) $userData->level; // Changed from array access to object property
        $claimAmount = 5 + ($level * 0.01);

        $claimData = [
            'user_id' => $user_id,
            'claim_amount' => $claimAmount,
            'ip_address' => $ipAddress
        ];

        $this->db->transStart();

        // Insert claim record
        $claimModel->insert($claimData);

        // Update claimer's points and exp
        $newExp = (int) $userData->exp + 1; // Changed from array access to object property
        $newLevel = floor($newExp / 100);

        $updateUserStats = [
            'points' => (float) $userData->points + $claimAmount, // Changed from array access to object property
            'exp' => $newExp,
            'level' => $newLevel
        ];
        $userModel->update($user_id, $updateUserStats);


        // Check if user was referred and add bonus to referrer
        $userModel->applyReferralBonus($user_id, $claimAmount);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'error' => 'Transaction failed'
            ]);
        }

        // Get updated balance
        $newBalance = $userModel->getBalance($user_id);

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

    // Get next claim time and user stats
    public function getNextClaimTime()
    {
        $user_id = auth()->id();
        $claimModel = new ClaimModel();
        $userModel = new UserModel();

        $userData = $userModel->find($user_id);
        $exp = (int) $userData->exp; // Changed from array access to object property
        $level = (int) $userData->level; // Changed from array access to object property

        if ($claimModel->canUserIdClaimFaucet($user_id) && $claimModel->canIpAddressNetworkClaimFaucet($this->request->getIPAddress())) {
            return $this->response->setJSON([
                'canClaim' => true,
                'balance' => $userModel->getBalance($user_id),
                'exp' => $exp,
                'level' => $level,
                'nextLevelExp' => ($level + 1) * 100
            ]);
        }

        $builder = $claimModel->db->table('claims');
        $builder->select('created_at');
        $builder->where('user_id', $user_id);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit(1);
        $lastClaim = $builder->get()->getRow();

        $nextClaimTime = strtotime($lastClaim->created_at) + 300;

        return $this->response->setJSON([
            'canClaim' => false,
            'nextClaimTime' => $nextClaimTime,
            'balance' => $userModel->getBalance($user_id),
            'exp' => $exp,
            'level' => $level,
            'nextLevelExp' => ($level + 1) * 100
        ]);
    }
}
