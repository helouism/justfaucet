<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClaimModel;
use App\Models\FraudUserModel;

class Claim extends BaseController
{
    protected $userModel;
    protected $claimModel;
    protected $fraudUserModel;



    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->claimModel = new ClaimModel();
        $this->fraudUserModel = new FraudUserModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Claim',

        ];
        return view('user/claim/index', $data);
    }

    // CLAIM ACTION
    public function store()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        // Verify hCaptcha first
        $hcaptchaResponse = $this->request->getPost('h-captcha-response');
        if (!$this->verifyHCaptcha($hcaptchaResponse)) {
            return $this->response->setJSON([
                'error' => 'Invalid captcha response. Please try again.'
            ]);
        }

        $user_id = auth()->id();
        $ipAddress = $this->request->getIPAddress();

        // Check if user can claim (5-minute cooldown)
        if (!$this->claimModel->canUserIdClaimFaucet($user_id)) {
            $this->logFraudAttempt($user_id, 'Use scripts', $ipAddress);
            return $this->response->setJSON([
                'error' => 'Please wait 5 minutes between claims.'
            ]);
        }

        // Check for VPN/Proxy/Tor usage
        $vpnCheckResult = $this->checkVpnProxy($ipAddress);
        if ($vpnCheckResult['isVpn']) {
            $this->logFraudAttempt($user_id, 'Using VPN/Proxy/Tor', $ipAddress, $vpnCheckResult['provider']);
            return $this->response->setJSON([
                'error' => 'VPN/Proxy/Tor usage is not allowed.'
            ]);
        }

        // Enhanced multiple account detection
        $multiAccountCheck = $this->claimModel->checkMultipleAccounts($user_id, $ipAddress);
        if (!$multiAccountCheck['allowed']) {
            $this->logFraudAttempt($user_id, 'Using multiple accounts', $ipAddress, $multiAccountCheck['reason']);
            return $this->response->setJSON([
                'error' => 'Multiple accounts on the same network is not allowed.'
            ]);
        }

        // Check if user is already marked as fraud
        if ($this->fraudUserModel->isUserFraud($user_id)) {
            return $this->response->setJSON([
                'error' => 'Account flagged for suspicious activity. Contact support.'
            ]);
        }

        // Get user's current level and calculate claim amount
        $userData = $this->userModel->find($user_id);
        $level = (int) $userData->level;
        $claimAmount = $this->baseClaimAmount() + ($level * 0.01);

        $rules = $this->claimModel->validationRules;
        if (!$this->validate($rules)) {
            $response = [
                'error' => 'Error.',
                'message' => implode('<br>', $this->validator->getErrors()),
            ];

        }

        $claimData = [
            'user_id' => $user_id,
            'claim_amount' => $claimAmount,
            'ip_address' => $ipAddress
        ];





        // Insert claim record
        $this->claimModel->insert($claimData);

        // Update claimer's points and exp
        $newExp = (int) $userData->exp + 1;
        $newLevel = floor($newExp / 100);

        $updateUserStats = [
            'points' => (float) $userData->points + $claimAmount,
            'exp' => $newExp,
            'level' => $newLevel
        ];
        $this->userModel->update($user_id, $updateUserStats);

        // Check if user was referred and add bonus to referrer
        $this->userModel->applyReferralBonus($user_id, $claimAmount);

        // Get updated balance
        $newBalance = $this->userModel->getBalance($user_id);

        $response = [
            'success' => 'Successfully claimed ' . number_format($claimAmount, 2) . ' points! and get 1 exp',
            'nextClaimTime' => time() + 300,
            'newBalance' => $newBalance,
            'exp' => $newExp,
            'level' => $newLevel,
            'nextLevelExp' => ($newLevel + 1) * 100
        ];

        // Check if level up occurred
        if ($newLevel > $level) {
            $response['levelUp'] = true;
            $response['newLevel'] = $newLevel;
        }

        return $this->response->setJSON($response);
    }

    // SHOW CLAIM STATUS
    public function show()
    {
        $user_id = auth()->id();
        $ipAddress = $this->request->getIPAddress();

        $userData = $this->userModel->find($user_id);
        $exp = (int) $userData->exp;
        $level = (int) $userData->level;

        // Check if user is flagged as fraud
        if ($this->fraudUserModel->isUserFraud($user_id)) {
            return $this->response->setJSON([
                'canClaim' => false,
                'error' => 'Account flagged for suspicious activity. Contact support.',
                'balance' => $this->userModel->getBalance($user_id),
                'exp' => $exp,
                'level' => $level,
                'nextLevelExp' => ($level + 1) * 100
            ]);
        }

        if ($this->claimModel->canUserIdClaimFaucet($user_id)) {
            // Check VPN/Proxy before allowing claim
            $vpnCheckResult = $this->checkVpnProxy($ipAddress);
            if ($vpnCheckResult['isVpn']) {
                return $this->response->setJSON([
                    'canClaim' => false,
                    'error' => 'VPN/Proxy/Tor usage is not allowed.',
                    'balance' => $this->userModel->getBalance($user_id),
                    'exp' => $exp,
                    'level' => $level,
                    'nextLevelExp' => ($level + 1) * 100
                ]);
            }

            // Check multiple accounts
            $multiAccountCheck = $this->claimModel->checkMultipleAccounts($user_id, $ipAddress);
            if ($multiAccountCheck['allowed']) {
                return $this->response->setJSON([
                    'canClaim' => true,
                    'balance' => $this->userModel->getBalance($user_id),
                    'exp' => $exp,
                    'level' => $level,
                    'nextLevelExp' => ($level + 1) * 100
                ]);
            }
        }

        // Get next claim time
        $nextClaimTime = $this->claimModel->getNextClaimTime($user_id);

        if ($nextClaimTime === null) {
            return $this->response->setJSON([
                'canClaim' => true,
                'balance' => $this->userModel->getBalance($user_id),
                'exp' => $exp,
                'level' => $level,
                'nextLevelExp' => ($level + 1) * 100
            ]);
        }

        return $this->response->setJSON([
            'canClaim' => false,
            'nextClaimTime' => $nextClaimTime,
            'balance' => $this->userModel->getBalance($user_id),
            'exp' => $exp,
            'level' => $level,
            'nextLevelExp' => ($level + 1) * 100
        ]);
    }

    /**
     * Check if IP address is using VPN/Proxy/Tor
     */
    private function checkVpnProxy(string $ipAddress): array
    {
        // Skip local/private IP addresses
        if ($this->isPrivateIP($ipAddress)) {
            return ['isVpn' => false, 'provider' => null];
        }

        // Try VPNApi first
        $vpnApiResult = $this->checkVpnApi($ipAddress);
        if ($vpnApiResult['isVpn']) {
            return $vpnApiResult;
        }

        // Try IPHub as fallback
        $ipHubResult = $this->checkIpHub($ipAddress);
        return $ipHubResult;
    }

    /**
     * Check VPN/Proxy using VPNApi.io
     */
    private function checkVpnApi(string $ipAddress): array
    {
        try {
            $apiKey = env('VPNAPI_KEY'); // Set in .env file
            if (empty($apiKey)) {
                return ['isVpn' => false, 'provider' => null];
            }

            $url = "https://vpnapi.io/api/{$ipAddress}?key={$apiKey}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                $data = json_decode($response, true);

                if (isset($data['security'])) {
                    $security = $data['security'];
                    $isVpn = $security['vpn'] || $security['proxy'] || $security['tor'];

                    return [
                        'isVpn' => $isVpn,
                        'provider' => $isVpn ? 'VPNApi' : null,
                        'details' => $security
                    ];
                }
            }
        } catch (Exception $e) {
            log_message('error', 'VPNApi check failed: ' . $e->getMessage());
        }

        return ['isVpn' => false, 'provider' => null];
    }

    /**
     * Check VPN/Proxy using IPHub
     */
    private function checkIpHub(string $ipAddress): array
    {
        try {
            $apiKey = env('IPHUB_KEY'); // Set in .env file
            if (empty($apiKey)) {
                return ['isVpn' => false, 'provider' => null];
            }

            $url = "http://v2.api.iphub.info/ip/{$ipAddress}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "X-Key: {$apiKey}"
            ]);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                $data = json_decode($response, true);

                if (isset($data['block'])) {
                    // IPHub returns 0 for good IPs, 1 for bad/proxy IPs, 2 for unknown
                    $isVpn = $data['block'] == 1;

                    return [
                        'isVpn' => $isVpn,
                        'provider' => $isVpn ? 'IPHub' : null,
                        'details' => $data
                    ];
                }
            }
        } catch (Exception $e) {
            log_message('error', 'IPHub check failed: ' . $e->getMessage());
        }

        return ['isVpn' => false, 'provider' => null];
    }

    /**
     * Check if IP address is private/local
     */
    private function isPrivateIP(string $ipAddress): bool
    {
        return !filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Log fraud attempt with additional details
     */
    private function logFraudAttempt(int $userId, string $abuseType, string $ipAddress, string $provider = null): void
    {
        $fraudData = [
            'user_id' => $userId,
            'abuse_type' => $abuseType,
            'ip_address' => $ipAddress,
            'detection_method' => $provider,
            'user_agent' => $this->request->getUserAgent(),
            'additional_data' => json_encode([
                'timestamp' => time(),
                'headers' => $this->request->getHeaders()
            ])
        ];

        $this->fraudUserModel->insert($fraudData);
    }

    // Verify with hCaptcha 
    private function verifyHCaptcha($response)
    {
        if (empty($response)) {
            return false;
        }

        try {
            $secret = env('HCAPTCHA_SECRET_KEY');
            $data = [
                'secret' => $secret,
                'response' => $response
            ];

            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($verify);
            curl_close($verify);

            $responseData = json_decode($response);
            log_message('debug', $response);
            return $responseData->success;
        } catch (Exception $e) {
            log_message('error', 'hCaptcha verification failed: ' . $e->getMessage());
            return false;
        }
    }

    private function baseClaimAmount(): int
    {
        return (int) env('BASE_CLAIM_AMOUNT');
    }
}