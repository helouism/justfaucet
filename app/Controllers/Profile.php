<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        $user = auth()->user();
        $userId = $user->id;

        $data = [
            'user' => $user,
            'balance' => $this->userModel->getBalance($userId),
            'totalReferrals' => $this->userModel->countReferrals($userId),
            'expToNextLevel' => $this->userModel->getExpToNextLevel($userId),
            'expRequired' => ($user->level + 1) * 100,
            'currentExp' => $user->exp
        ];

        return view('user/profile', $data);
    }
}
