<?php

namespace App\Controllers;
use App\Models\UserModel;
class Dashboard extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index(): string
    {

        // get the current user's id
        $user_id = auth()->id();


        // Count referrals and get users balance
        $user_account = $this->userModel->find($user_id);
        $referralCount = $this->userModel->countReferrals($user_id);

        $expToNextLevel = $this->userModel->getExpToNextLevel($user_id);
        $data = [
            'title' => 'Dashboard',
            'user' => $user_account, // Assuming this is the user object
            'referralCount' => $referralCount,
            'balance' => $user_account->points, // Assuming this is the user object->points, // Assuming points is the balances
            'expToNextLevel' => $expToNextLevel,
        ];

        return view('user/dashboard/index', $data);
    }


}
