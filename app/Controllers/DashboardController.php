<?php

namespace App\Controllers;
use App\Models\UserModel;
class DashboardController extends BaseController
{
    public function index(): string
    {

        // get the current user's id
        $user_id = auth()->id();
        $userModel = new UserModel();

        // Count referrals and get users balance
        $user = $userModel->find($user_id);
        $referralCount = $userModel->countReferrals($user_id);
        // $balance = $userModel->getBalance($user_id);
        $expToNextLevel = $userModel->getExpToNextLevel($user_id);
        $data = [
            'user' => $user,
            'referralCount' => $referralCount,
            'balance' => $user->points, // Assuming points is the balances
            'expToNextLevel' => $expToNextLevel,
        ];

        return view('user/dashboard', $data);
    }
}
