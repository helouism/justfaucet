<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\ClaimModel;

class Challenge extends BaseController
{
    protected $userModel;
    protected $claimModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->claimModel = new ClaimModel();
    }

    public function index()
    {
        $userId = auth()->id();

        // Get challenge progress
        $referralProgress = $this->userModel->referralChallenge($userId);
        $claimProgress = $this->claimModel->claimChallenge($userId);

        $data = [
            'challenges' => [
                [
                    'id' => 1,
                    'title' => 'Master Referrer',
                    'description' => 'Refer 10 users, users must be level 1 or higher',
                    'reward' => 200,
                    'progress' => $referralProgress,
                    'target' => 10
                ],
                [
                    'id' => 2,
                    'title' => 'Dedicated Claimer',
                    'description' => 'Claim 50 times in 24 hours',
                    'reward' => 50,
                    'progress' => $claimProgress,
                    'target' => 50
                ],
                [
                    'id' => 3,
                    'title' => 'Expert Claimer',
                    'description' => 'Claim 100 times in 24 hours',
                    'reward' => 100,
                    'progress' => $claimProgress,
                    'target' => 100
                ],
                [
                    'id' => 4,
                    'title' => 'Master Claimer',
                    'description' => 'Claim 200 times in 24 hours',
                    'reward' => 200,
                    'progress' => $claimProgress,
                    'target' => 200
                ]
            ]
        ];

        return view('user/challenge/index', $data);
    }

    public function claim($challengeId)
    {
        $userId = auth()->id();

        // Verify and reward challenge completion
        $completed = false;

        switch ($challengeId) {
            case 1:
                if ($this->userModel->referralChallenge($userId) >= 10) {
                    $reward = 200;
                    $completed = true;
                }
                break;
            case 2:
                if ($this->claimModel->claimChallenge($userId) >= 50) {
                    $reward = 50;
                    $completed = true;
                }
                break;
            case 3:
                if ($this->claimModel->claimChallenge($userId) >= 100) {
                    $reward = 100;
                    $completed = true;
                }
                break;
            case 4:
                if ($this->claimModel->claimChallenge($userId) >= 200) {
                    $reward = 200;
                    $completed = true;
                }
                break;
        }

        if ($completed) {
            // Update user points
            $this->userModel->update($userId, [
                'points' => $this->userModel->getBalance($userId) + $reward
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Congratulations! You've received $reward points!"
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Challenge requirements not met.'
        ]);
    }
}
