<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $withdrawalModel;
    protected $userModel;
    public function __construct()
    {
        $this->withdrawalModel = new WithdrawalModel();
        $this->userModel = new UserModel();
    }
    public function index(): string
    {
        $data = [
            'title' => 'Admin Dashboard',
        ];




        return view('admin/index', $data);
    }

    public function manageWithdrawals(): string
    {
        $user = auth()->user();
        if (!$user->inGroup('admin')) {
            return view('/');
        }


        $withdrawals = $this->withdrawalModel->getAllWithdrawals();

        $data = [
            'title' => 'Manage Withdrawals',
            'withdrawals' => $withdrawals,
        ];

        return view('admin/manage-withdrawals/index', $data);
    }

    public function manageUsers(): string
    {
        $user = auth()->user();
        if (!$user->inGroup('admin')) {
            return view('/');
        }


        $users = $this->userModel->getAllUsers();
        $isActive = "No";
        if ($user->isActivated()) {
            $isActive = "Yes";
        } else {
            $isActive = "No";
        }

        $data = [
            'title' => 'Manage Users',
            'isActive' => $isActive,
            'users' => $users,
        ];

        return view('admin/manage-users/index', $data);
    }


}