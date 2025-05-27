<?php

namespace App\Controllers;

use App\Models\WithdrawalModel;
use App\Models\UserModel;
use App\Libraries\FaucetPay\FaucetPay;
use Exception;
use InvalidArgumentException;

class Withdrawal extends BaseController
{
    protected WithdrawalModel $withdrawalModel;
    protected UserModel $userModel;
    protected FaucetPay $faucetPay;

    public function __construct()
    {
        $this->withdrawalModel = new WithdrawalModel();
        $this->userModel = new UserModel();
        $this->faucetPay = new FaucetPay();
    }

    public function index(): string
    {
        $user_id = auth()->id();
        $userWithdrawals = $this->withdrawalModel->getWithdrawalsByUser($user_id);
        $canWithdraw = $this->withdrawalModel->canWithdraw($user_id);

        $data = [
            'withdrawals' => $userWithdrawals,
            'canWithdraw' => $canWithdraw,
        ];

        return view('user/withdrawal/index', $data);
    }

    /**
     * Process withdrawal request
     */
    public function request()
    {
        // Only accept POST requests
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $user_id = auth()->id();
        $user = auth()->user();

        // Validate CSRF token
        if (!$this->validate(['csrf_token' => 'required'])) {
            return redirect()->back()->with('error', 'Invalid CSRF token');
        }

        // Validate input
        $rules = [
            'amount' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Amount is required',
                    'numeric' => 'Amount must be a valid number',
                    'greater_than' => 'Amount must be greater than 0'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $amount = (float) $this->request->getPost('amount');

        try {
            // Check if user can withdraw
            if (!$this->withdrawalModel->canWithdraw($user_id)) {
                return redirect()->back()->with('error', 'Insufficient balance. Minimum 2000 points required.');
            }

            // Get user's current balance
            $currentBalance = $this->userModel->getBalance($user_id);

            if ($amount > $currentBalance) {
                return redirect()->back()->with('error', 'Insufficient balance for this withdrawal amount.');
            }

            // Convert points to USDT (assuming 1000 points = 1 USDT, adjust as needed)
            $usdtAmount = $amount / 1000;

            // Minimum withdrawal check in USDT
            if ($usdtAmount < 0.01) {
                return redirect()->back()->with('error', 'Minimum withdrawal amount is 0.01 USDT (10 points).');
            }

            // Check if user has a pending withdrawal
            if ($this->hasPendingWithdrawal($user_id)) {
                return redirect()->back()->with('error', 'You already have a pending withdrawal. Please wait for it to be processed.');
            }

            // Get user email for FaucetPay
            $userEmail = $user->email;

            // Check if user exists in FaucetPay
            try {
                $userCheck = $this->faucetPay->checkUser($userEmail);
                if (!$userCheck || (isset($userCheck['status']) && $userCheck['status'] !== 200)) {
                    return redirect()->back()->with('error', 'Email not found in FaucetPay. Please register at FaucetPay.io first.');
                }
            } catch (Exception $e) {
                log_message('error', 'FaucetPay user check failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Unable to verify FaucetPay account. Please try again later.');
            }

            // Create withdrawal record
            $withdrawalData = [
                'user_id' => $user_id,
                'amount' => $amount,
                'usdt_amount' => $usdtAmount,
                'status' => 'pending',
                'email' => $userEmail,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $withdrawalId = $this->withdrawalModel->insert($withdrawalData);

            if (!$withdrawalId) {
                return redirect()->back()->with('error', 'Failed to create withdrawal request. Please try again.');
            }

            // Process the withdrawal through FaucetPay
            try {
                $referenceId = 'withdrawal_' . $withdrawalId . '_' . $user_id;

                $response = $this->faucetPay->sendPayment(
                    $userEmail,
                    $usdtAmount,
                    'USDT',
                    $referenceId
                );

                if (isset($response['status']) && $response['status'] === 200) {
                    // Payment successful
                    $this->withdrawalModel->update($withdrawalId, [
                        'status' => 'completed',
                        'faucetpay_reference' => $referenceId,
                        'processed_at' => date('Y-m-d H:i:s'),
                        'response_data' => json_encode($response)
                    ]);

                    // Deduct points from user balance
                    $this->userModel->set('points', 'points - ' . $amount, false)
                        ->where('id', $user_id)
                        ->update();

                    // Log successful withdrawal
                    log_message('info', "Withdrawal completed for user {$user_id}: {$amount} points ({$usdtAmount} USDT)");

                    return redirect()->back()->with('success', "Withdrawal successful! {$usdtAmount} USDT has been sent to your FaucetPay account.");
                } else {
                    // Payment failed
                    $errorMessage = $response['message'] ?? 'Unknown error from FaucetPay';

                    $this->withdrawalModel->update($withdrawalId, [
                        'status' => 'failed',
                        'error_message' => $errorMessage,
                        'response_data' => json_encode($response)
                    ]);

                    log_message('error', "FaucetPay withdrawal failed for user {$user_id}: " . $errorMessage);

                    return redirect()->back()->with('error', 'Withdrawal failed: ' . $errorMessage);
                }

            } catch (Exception $e) {
                // Update withdrawal status to failed
                $this->withdrawalModel->update($withdrawalId, [
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);

                log_message('error', 'Withdrawal processing error: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Withdrawal processing failed. Please try again later.');
            }

        } catch (Exception $e) {
            log_message('error', 'Withdrawal request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your withdrawal. Please try again.');
        }
    }

    /**
     * Check if user has a pending withdrawal
     */
    private function hasPendingWithdrawal(int $userId): bool
    {
        $pendingCount = $this->withdrawalModel
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->countAllResults();

        return $pendingCount > 0;
    }

    /**
     * Get withdrawal history for API
     */
    public function history()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Access denied');
        }

        $user_id = auth()->id();
        $withdrawals = $this->withdrawalModel->getWithdrawalsByUser($user_id);

        return $this->respond([
            'status' => 'success',
            'data' => $withdrawals
        ]);
    }

    /**
     * Cancel pending withdrawal
     */
    public function cancel($withdrawalId = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        if (!$withdrawalId) {
            return redirect()->back()->with('error', 'Invalid withdrawal ID');
        }

        $user_id = auth()->id();

        // Check if withdrawal exists and belongs to user
        $withdrawal = $this->withdrawalModel
            ->where('id', $withdrawalId)
            ->where('user_id', $user_id)
            ->where('status', 'pending')
            ->first();

        if (!$withdrawal) {
            return redirect()->back()->with('error', 'Withdrawal not found or cannot be cancelled');
        }

        // Update withdrawal status
        $updated = $this->withdrawalModel->update($withdrawalId, [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            return redirect()->back()->with('success', 'Withdrawal has been cancelled successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel withdrawal');
        }
    }
}