<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class FaucetPay extends BaseConfig
{
    /**
     * FaucetPay API Key
     * Get this from your FaucetPay dashboard
     */
    public string $apiKey = '';

    /**
     * FaucetPay API Base URL
     */
    public string $baseUrl = 'https://faucetpay.io/api/v1/';

    /**
     * Minimum withdrawal amount in USDT
     */
    public float $minWithdrawalUsdt = 0.2;

    /**
     * Points to USDT conversion rate
     * How many points equal 1 USDT
     */
    public int $pointsToUsdtRate = 10000;

    /**
     * Minimum points required for withdrawal
     */
    public int $minWithdrawalPoints = 2000;

    /**
     * HTTP timeout for API requests (seconds)
     */
    public int $timeout = 30;

    /**
     * Connection timeout for API requests (seconds)
     */
    public int $connectTimeout = 10;

    /**
     * Maximum number of pending withdrawals per user
     */
    public int $maxPendingWithdrawals = 3;

    /**
     * Supported currencies
     */
    public array $supportedCurrencies = [
        'BTC',
        'ETH',
        'LTC',
        'DOGE',
        'BCH',
        'DASH',
        'DGB',
        'TRX',
        'FEY',
        'ZEC',
        'BNB',
        'SOL',
        'USDT'
    ];

    /**
     * Default currency for withdrawals
     */
    public string $defaultCurrency = 'USDT';

    /**
     * Enable/disable automatic withdrawal processing
     */
    public bool $autoProcess = true;

    /**
     * Enable logging of all API requests and responses
     */
    public bool $enableLogging = true;

    /**
     * Log level for FaucetPay operations
     */
    public string $logLevel = 'info';

    public function __construct()
    {
        parent::__construct();

        // Load API key from environment
        $this->apiKey = env('FAUCETPAY_API_KEY', '');

        // Override defaults with environment variables if available
        $this->minWithdrawalUsdt = (float) env('FAUCETPAY_MIN_WITHDRAWAL_USDT', $this->minWithdrawalUsdt);
        $this->pointsToUsdtRate = (int) env('FAUCETPAY_POINTS_TO_USDT_RATE', $this->pointsToUsdtRate);
        $this->timeout = (int) env('FAUCETPAY_TIMEOUT', $this->timeout);
        $this->autoProcess = filter_var(env('FAUCETPAY_AUTO_PROCESS', $this->autoProcess), FILTER_VALIDATE_BOOLEAN);
        $this->enableLogging = filter_var(env('FAUCETPAY_ENABLE_LOGGING', $this->enableLogging), FILTER_VALIDATE_BOOLEAN);

        // Calculate minimum points based on USDT rate
        $this->minWithdrawalPoints = (int) ($this->minWithdrawalUsdt * $this->pointsToUsdtRate);
    }

    /**
     * Convert points to USDT
     */
    public function pointsToUsdt(float $points): float
    {
        return $points / $this->pointsToUsdtRate;
    }

    /**
     * Convert USDT to points
     */
    public function usdtToPoints(float $usdt): float
    {
        return $usdt * $this->pointsToUsdtRate;
    }

    /**
     * Check if currency is supported
     */
    public function isCurrencySupported(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->supportedCurrencies);
    }

    /**
     * Get formatted minimum withdrawal text
     */
    public function getMinWithdrawalText(): string
    {
        return number_format($this->minWithdrawalPoints) . ' points (' .
            number_format($this->minWithdrawalUsdt, 4) . ' USDT)';
    }
}