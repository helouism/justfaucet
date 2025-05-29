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

    public function editUser(int $userId)
    {
        return view('admin/manage-users/edit', [
            'title' => 'Edit User',
            'userId' => $userId,
            'username' => auth()->getProvider()->find($userId)->username,
            'email' => auth()->getProvider()->find($userId)->email,
        ]);
    }

    public function updateUser(int $userId)
    {
        try {
            $users = auth()->getProvider();
            $userData = $this->request->getPost();

            $rules = [
                'username' => [
                    'label' => 'Username',
                    'rules' => 'required|max_length[30]|min_length[3]|regex_match[/\A[a-zA-Z0-9\.]+\z/]|is_unique[users.username,id,' . $userId . ']',
                    'errors' => [
                        'is_unique' => 'Username is already taken.',
                        'regex_match' => 'Username can only contain letters, numbers, and dots.',
                        'max_length' => 'Username cannot exceed 30 characters.',
                        'min_length' => 'Username must be at least 3 characters long.'
                    ]
                ],
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|max_length[254]|valid_email|is_unique[auth_identities.secret,id,' . $userId . ']',
                    'errors' => [
                        'is_unique' => 'Email is already registered.',
                        'valid_email' => 'Please enter a valid email address.',
                        'max_length' => 'Email cannot exceed 254 characters.'
                    ]
                ],
            ];
            // Validate input data
            if (!$this->validate($rules)) {
                $response = [
                    'error' => 'Error.',
                    'message' => implode('<br>', $this->validator->getErrors()),
                ];
                return $this->response->setJSON($response);
            }


            // Update the user
            $user = $users->update($userId, $userData);

            if ($user) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => true,
                    'message' => 'Failed to update user'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => true,
                'message' => 'An error occurred while updating the user'
            ]);
        }
    }


    public function banUser(int $userId): \CodeIgniter\HTTP\RedirectResponse
    {
        $users = auth()->getProvider();


        $user = $users->ban();
        return redirect()->back()->with('success', 'User banned successfully.');
    }


}