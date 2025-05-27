<?php

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\ClaimModel;

class Referral extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();
        $claimModel = new ClaimModel();
        $user_id = auth()->id();

        // Get user's referrals
        $referrals = $userModel->getReferrals($user_id);

        // Get referral claims in last 30 days
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

        $referralClaims = [];
        $totalEarned = 0;

        foreach ($referrals as &$referral) {
            $claims = $claimModel->where('user_id', $referral['id'])
                ->where('created_at >=', $thirtyDaysAgo)
                ->findAll();

            $referralEarnings = (count($claims) * 0.5) + ($referral['level'] * (1 / 100)); // 10% of 5 points = 0.5 points per claim
            $totalEarned += $referralEarnings;

            $referral['claims_30days'] = count($claims);
            $referral['earnings'] = $referralEarnings;
        }

        $data = [

            'referrals' => $referrals,
            'total_referrals' => count($referrals),
            'total_earned' => $totalEarned,
            'referral_link' => base_url('?ref=' . $user_id),

        ];

        return view('user/referral/index', $data);
    }
}
