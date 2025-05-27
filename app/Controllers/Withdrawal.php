<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;

class Withdrawal extends BaseController
{
    public function index(): string
    {

        $withdrawalModel = new WithdrawalModel();
        $user_id = auth()->id();
        $userWithdrawals = $withdrawalModel->getWithdrawalsByUser($user_id);
        $canWithdraw = $withdrawalModel->canWithdraw($user_id);
        $data = [
            'withdrawals' => $userWithdrawals,
            'canWithdraw' => $canWithdraw,
        ];

        return view('user/withdrawal', $data);
    }
}
