<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Mahtab2003\FaucetPay\Api;

class RefreshFaucetPayBalance extends BaseCommand
{
    protected $group = 'Faucet';
    protected $name = 'faucet:refresh-balance';
    protected $description = 'Refreshes the FaucetPay balance cache';

    public function run(array $params)
    {
        try {
            // Initialize FaucetPay API
            $faucetPayApi = new Api(getenv('FAUCETPAY_API_KEY'), 'USDT');

            // Get fresh balance
            $balanceResponse = $faucetPayApi->getBalance();

            if ($balanceResponse->isSuccessful()) {
                // Save to cache
                $cache = \Config\Services::cache();
                $cache->save('faucetpay_balance', $balanceResponse, 300);

                CLI::write('FaucetPay balance cache refreshed successfully', 'green');
            } else {
                CLI::error('Failed to get FaucetPay balance: ' . $balanceResponse->getMessage());
            }
        } catch (\Exception $e) {
            CLI::error('Error refreshing FaucetPay balance: ' . $e->getMessage());
        }
    }
}
