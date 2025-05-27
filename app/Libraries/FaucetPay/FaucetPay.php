<?php

namespace App\Libraries\FaucetPay;

use Exception;
use InvalidArgumentException;

class FaucetPay
{
    private string $apiKey;
    private string $baseUrl;
    private array $supportedCurrencies;

    public function __construct()
    {
        $this->apiKey = env('FAUCETPAY_API_KEY');
        $this->baseUrl = 'https://faucetpay.io/api/v1/';
        $this->supportedCurrencies = ['BTC', 'ETH', 'LTC', 'DOGE', 'BCH', 'DASH', 'DGB', 'TRX', 'FEY', 'ZEC', 'BNB', 'SOL', 'USDT'];

        if (empty($this->apiKey)) {
            throw new Exception('FaucetPay API key is not configured in environment variables');
        }
    }

    /**
     * Send payment to user via FaucetPay
     *
     * @param string $email User's email address
     * @param float $amount Amount to send
     * @param string $currency Currency code (default: USDT)
     * @param string|null $referenceId Optional reference ID for tracking
     * @return array API response
     * @throws Exception
     */
    public function sendPayment(string $email, float $amount, string $currency = 'USDT', ?string $referenceId = null): array
    {
        // Validate inputs
        $this->validateEmail($email);
        $this->validateAmount($amount);
        $this->validateCurrency($currency);

        $payload = [
            'api_key' => $this->apiKey,
            'to' => $email,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'referenceId' => $referenceId ?? uniqid('withdraw_', true)
        ];

        return $this->makeRequest('send', $payload);
    }

    /**
     * Check account balance
     *
     * @param string $currency Currency to check balance for
     * @return array API response containing balance information
     * @throws Exception
     */
    public function getBalance(string $currency = 'USDT'): array
    {
        $this->validateCurrency($currency);

        $payload = [
            'api_key' => $this->apiKey,
            'currency' => strtoupper($currency)
        ];

        return $this->makeRequest('balance', $payload);
    }

    /**
     * Get list of supported currencies
     *
     * @return array List of supported currency codes
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    /**
     * Check if user email exists in FaucetPay
     *
     * @param string $email User's email address
     * @return array API response
     * @throws Exception
     */
    public function checkUser(string $email): array
    {
        $this->validateEmail($email);

        $payload = [
            'api_key' => $this->apiKey,
            'email' => $email
        ];

        return $this->makeRequest('checkuser', $payload);
    }

    /**
     * Make HTTP request to FaucetPay API
     *
     * @param string $endpoint API endpoint
     * @param array $payload Request payload
     * @return array Decoded API response
     * @throws Exception
     */
    private function makeRequest(string $endpoint, array $payload): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: CodeIgniter-FaucetPay-Client/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($error)) {
            throw new Exception("cURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: Received status code $httpCode");
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response: " . json_last_error_msg());
        }

        // Check for API errors
        if (isset($decodedResponse['status']) && $decodedResponse['status'] !== 200) {
            $errorMessage = $decodedResponse['message'] ?? 'Unknown API error';
            throw new Exception("FaucetPay API Error: " . $errorMessage);
        }

        return $decodedResponse;
    }

    /**
     * Validate email address
     *
     * @param string $email Email to validate
     * @throws InvalidArgumentException
     */
    private function validateEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address provided');
        }
    }

    /**
     * Validate amount
     *
     * @param float $amount Amount to validate
     * @throws InvalidArgumentException
     */
    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        // Set minimum amount for USDT (adjust as needed)
        $minAmount = 0.2;
        if ($amount < $minAmount) {
            throw new InvalidArgumentException("Minimum withdrawal amount is $minAmount USDT");
        }
    }

    /**
     * Validate currency code
     *
     * @param string $currency Currency to validate
     * @throws InvalidArgumentException
     */
    private function validateCurrency(string $currency): void
    {
        $currency = strtoupper($currency);
        if (!in_array($currency, $this->supportedCurrencies)) {
            throw new InvalidArgumentException("Unsupported currency: $currency");
        }
    }
}