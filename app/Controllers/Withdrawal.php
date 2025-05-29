<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;
use App\Models\UserModel;
use Mahtab2003\FaucetPay\Api;

class Withdrawal extends BaseController
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
        // Create a new Api class instance.
        // $faucetpay_api = new Api(getenv('FAUCETPAY_API_KEY'), 'USDT');

        // Check if user can withdraw



        $rules = [

            'amount' => [
                'rules' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[100000]',
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
                'message' => 'You does not have enough points.',
            ];
            return $this->response->setJSON($response);
        } else {
            // Create withdrawal record
            // $user_email = $this->userModel->getUserEmail($user_id);
            // $response = $faucetpay_api->send($user_email, $amount);

            // if ($response->isSuccessful()) {
            //     $data = $response->getData();
            //     $withdrawalData = [
            //         'user_id' => $user_id,
            //         'email' => $user_email,
            //         'amount' => $amount,
            //         'status' => 'paid',
            //         'faucetpay_payout_id' => $data['payout_id'] ?? null, // Assuming API returns transaction ID
            //     ];
            //     // Save withdrawal to database
            //     $withdrawalData[''] = $data[''] ?? null;

            $withdrawalData = [
                'user_id' => $user_id,
                'amount' => $amount,
                'status' => 'paid',
                'faucetpay_payout_id' => null,
            ];// Placeholder for API response
            $this->withdrawalModel->insert($withdrawalData);
            // Update user points
            $this->userModel->decrementPoints($user_id, $amount);
            // Redirect with success message
            $response = [
                'success' => 'Withdrawal successful.',
                'message' => 'You will receive your payout soon.',
            ];
            return $this->response->setJSON($response);
        }



    }
}