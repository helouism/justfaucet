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
            'title' => 'Profile',
            'user' => $user,
            'balance' => $this->userModel->getBalance($userId),
            'totalReferrals' => $this->userModel->countReferrals($userId),
            'expToNextLevel' => $this->userModel->getExpToNextLevel($userId),
            'expRequired' => ($user->level + 1) * 100,
            'currentExp' => $user->exp
        ];

        return view('user/profile/index', $data);
    }



    public function edit()
    {
        $data = [
            'user_id' => auth()->user()->id,
            'title' => "Edit Profile"
        ];
        return view('user/profile/edit', $data);
    }

    public function updatePassword()
    {
        if ($this->isRateLimited()) {
            session()->setFlashdata('error', 'Too many failed password change attempts. Please try again later.');
            return redirect()->route('profile/edit');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'old_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Current password is required'
                ]
            ],
            'new_password' => [
                'rules' => 'required|min_length[8]|max_length[128]|strong_password[]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'Password must be at least 8 characters long',
                    'max_length' => 'Password must not exceed 128 characters',
                    'strong_password' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
                ]
            ],
            'new_password_confirm' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Password confirmation is required',
                    'matches' => 'Password confirmation does not match'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->incrementFailedAttempts();
            return redirect()->route('profile/edit')->withInput()->with('errors', $validation->getErrors());
        }

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');


        // Verify current password
        if (!$this->verifyCurrentPassword($oldPassword)) {
            $this->incrementFailedAttempts();
            session()->setFlashdata('error', 'Current password is incorrect');
            return redirect()->route('profile/edit');
        }

        // Check if new password is same as old password
        if ($this->isSameAsCurrentPassword($newPassword)) {
            session()->setFlashdata('error', 'New password must be different from current password');
            return redirect()->route('profile/edit');
        }

        try {
            // Update password using Shield's proper method
            $user = auth()->user();
            $users = auth()->getProvider();

            // Use Shield's password update method
            $user->password = $newPassword;
            $users->save($user);

            $this->clearFailedAttempts();

            // Log the password change
            log_message('info', 'Password changed for user ID: ' . $user->id . ' from IP: ' . $this->request->getIPAddress());



            session()->setFlashdata('success', 'Password updated successfully');

            // Redirect to login to force re-authentication with new password
            return redirect()->to('/profile/edit')->with('message', 'Password updated successfully. Please login with your new password.');

        } catch (\Exception $e) {
            log_message('error', 'Password update failed for user ID: ' . auth()->user()->id . ' - Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while updating your password. Please try again.');
            return redirect()->route('profile/edit');
        }
    }

    private function verifyCurrentPassword(string $password): bool
    {
        $result = auth()->check([
            'email' => auth()->user()->email,
            'password' => $password,
        ]);

        return $result->isOK();
    }

    /**
     * Check if new password is same as current password
     */
    private function isSameAsCurrentPassword(string $newPassword): bool
    {
        $user = auth()->user();
        return password_verify($newPassword, $user->password_hash);
    }

    private function isRateLimited(): bool
    {
        $key = 'password_change_attempts_' . auth()->user()->id;
        $cache = \Config\Services::cache();

        $attempts = $cache->get($key) ?? 0;

        // Allow max 3 attempts per hour
        return $attempts >= 3;
    }

    private function clearFailedAttempts(): void
    {
        $key = 'password_change_attempts_' . auth()->user()->id;
        $cache = \Config\Services::cache();
        $cache->delete($key);
    }

    private function incrementFailedAttempts(): void
    {
        $key = 'password_change_attempts_' . auth()->user()->id;
        $cache = \Config\Services::cache();

        $attempts = $cache->get($key) ?? 0;
        $cache->save($key, $attempts + 1, 3600); // Store for 1 hour
    }


}
