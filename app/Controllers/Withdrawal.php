<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;
use App\Models\UserModel;
use Mahtab2003\FaucetPay\Api;

class Withdrawal extends BaseController
{
    protected $withdrawalModel;
    protected $userModel;
    protected $faucetPayApi;

    public function __construct()
    {
        $this->withdrawalModel = new WithdrawalModel();
        $this->userModel = new UserModel();

        // Initialize FaucetPay API
        $this->faucetPayApi = new Api(getenv('FAUCETPAY_API_KEY'), 'USDT');
        // You'll need to set your API key - either from config or environment

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

    public function sendPayment()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $user_id = auth()->id();
        $amount = $this->request->getPost('amount');

        $rules = [
            'amount' => [
                'rules' => 'required|integer|greater_than_equal_to[2]|less_than_equal_to[100000]',
                'errors' => [
                    'required' => 'The amount field is required.',
                    'integer' => 'The amount must be an integer.',
                    'greater_than_equal_to' => 'You need at least 2000 Points to withdraw.',
                    'less_than_equal_to' => 'The amount must not exceed 100000.',
                ]
            ]
        ];

        // Validate input data
        if (!$this->validate($rules)) {
            $response = [
                'error' => 'Withdrawal failed.',
                'message' => implode('<br>', $this->validator->getErrors()),
            ];
            return $this->response->setJSON($response);
        }

        // Check if the user has enough points
        $user_points = $this->userModel->getBalance($user_id);

        if ($user_points < $amount) {
            $response = [
                'error' => 'Withdrawal failed.',
                'message' => 'You do not have enough points.',
            ];
            return $this->response->setJSON($response);
        }
        try {
            // Get the faucet balance from cache or API
            $cache = \Config\Services::cache();
            $balanceKey = 'faucetpay_balance';
            $balanceResponse = $cache->get($balanceKey);

            if ($balanceResponse === null) {
                $balanceResponse = $this->faucetPayApi->getBalance();
                if ($balanceResponse->isSuccessful()) {
                    // Cache the balance for 5 minutes
                    $cache->save($balanceKey, $balanceResponse, 300);
                }
            }

            if (!$balanceResponse->isSuccessful()) {
                log_message('debug', $balanceResponse->getMessage());
                $response = [
                    'error' => 'Withdrawal failed.',
                    'message' => 'Unable to check FaucetPay balance. Please try again later.',
                ];
                return $this->response->setJSON($response);
            }            // Get user email
            $users = auth()->user();
            $user_email = $users->email;

            // Convert points to USD
            $satoshiAmount = $this->withdrawalModel->convertPoints($amount);
            if ($satoshiAmount === 0) {
                $response = [
                    'error' => 'Withdrawal failed.',
                    'message' => 'Error converting points to Satoshi. Please try again later.',
                ];
                return $this->response->setJSON($response);
            }

            $paymentResponse = $this->faucetPayApi->send($user_email, $satoshiAmount, false);

            if ($paymentResponse->isSuccessful()) {
                $responseData = $paymentResponse->getData();
                // Payment successful
                $withdrawalData = [
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'status' => 'paid',
                    'faucetpay_payout_id' => $responseData['payout_id'] ?? null,
                ];

                $this->withdrawalModel->insert($withdrawalData);

                // Update user points
                $this->userModel->decrementPoints($user_id, $amount);

                $response = [
                    'success' => 'Withdrawal successful.',
                    'message' => 'Payment has been sent to your email via FaucetPay.',
                ];
            } else {
                // Payment failed
                $withdrawalData = [
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'status' => 'failed',
                    'faucetpay_payout_id' => null,
                ];

                $this->withdrawalModel->insert($withdrawalData);

                $errorMessage = $paymentResponse->getMessage() ?? 'Payment failed through FaucetPay.';
                $response = [
                    'error' => 'Withdrawal failed.',
                    'message' => $errorMessage,
                ];
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            log_message('error', 'FaucetPay API Error: ' . $e->getMessage());

            // Record failed withdrawal
            $withdrawalData = [
                'user_id' => $user_id,
                'amount' => $amount,
                'status' => 'failed',
                'faucetpay_payout_id' => null,
            ];
            $this->withdrawalModel->insert($withdrawalData);

            $response = [
                'error' => 'Withdrawal failed.',
                'message' => 'An error occurred while processing your withdrawal. Please try again later.',
            ];
        }

        return $this->response->setJSON($response);
    }
}