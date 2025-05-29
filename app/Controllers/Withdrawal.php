<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;

class Withdrawal extends BaseController
{
    protected $withdrawalModel;
    public function __construct()
    {
        $this->withdrawalModel = new WithdrawalModel();
    }
    public function index(): string
    {


        $user_id = auth()->id();
        $userWithdrawals = $this->withdrawalModel->getWithdrawalsByUser($user_id);
        $canWithdraw = $this->withdrawalModel->canWithdraw($user_id);
        $data = [
            'title' => 'Withdrawals',
            'withdrawals' => $userWithdrawals,
            'canWithdraw' => $canWithdraw,
        ];

        return view('user/withdrawal/index', $data);
    }


}