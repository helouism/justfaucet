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

    public function action()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $user_id = auth()->id();
        $claimModel = new ClaimModel();
        $userModel = new UserModel();
        $claimAmount = 5; // Base claim amount

        if (!$claimModel->canClaimFaucet($user_id)) {
            return $this->response->setJSON([
                'error' => 'Please wait 5 minutes between claims.'
            ]);
        }

        $claimData = [
            'user_id' => $user_id,
            'claim_amount' => $claimAmount
        ];

        $this->db->transStart();

        // Insert claim record
        $claimModel->insert($claimData);

        // Update claimer's points
        $this->db->query("UPDATE users SET points = points + ? WHERE id = ?", [$claimAmount, $user_id]);

        // Check if user was referred and add bonus to referrer
        $referralInfo = $userModel->checkReferral($user_id);
        if ($referralInfo) {
            $referralBonus = $claimAmount * 0.10; // 10% bonus
            $this->db->query(
                "UPDATE users SET points = points + ? WHERE id = ?",
                [$referralBonus, $referralInfo['referrer_id']]
            );


        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'error' => 'Transaction failed'
            ]);
        }

        // Get updated balance
        $newBalance = $userModel->getBalance($user_id);

        return $this->response->setJSON([
            'success' => 'Successfully claimed ' . $claimAmount . ' points!',
            'nextClaimTime' => time() + 300,
            'newBalance' => $newBalance
        ]);
    }

    public function getNextClaimTime()
    {
        $user_id = auth()->id();
        $claimModel = new ClaimModel();
        $userModel = new UserModel();

        if ($claimModel->canClaimFaucet($user_id)) {
            return $this->response->setJSON([
                'canClaim' => true,
                'balance' => $userModel->getBalance($user_id)
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
            'balance' => $userModel->getBalance($user_id)
        ]);
    }
}
