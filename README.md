# JustFaucet

A secure, open-source crypto faucet platform built with CodeIgniter 4, featuring advanced fraud detection and administration capabilities.

![Faucet](https://github.com/user-attachments/assets/5221fad3-6fb1-46a2-b07a-6ab911081054)

## Features

- **Admin Dashboard** - Manage users, payouts, and settings
- **Fraud Prevention** - VPN/Proxy/Tor detection, CAPTCHA, and behavioral analysis
- **Flexible Configuration** - Cooldown periods, base amounts, and referral bonuses
- **Automatic Payouts** - Scheduled balance refresh via cron jobs
- **Security-First** - Built-in user banning and suspicion-based account monitoring

## Quick Start

### Prerequisites
- PHP 8.0+
- Composer
- MySQL/MariaDB

### Installation

1. Clone the repository:
```bash
git clone https://github.com/helouism/justfaucet.git
cd justfaucet
```

2. Install dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp env.example .env
```

4. Configure settings in `.env`:
   - Database credentials
   - Email settings
   - API keys (FaucetPay, IPHub, VPNApi)
   - Faucet parameters (cooldown, base amount, referral percentage)

5. Initialize authentication:
```bash
php spark shield:setup
```

> **Note:** Do not overwrite `Auth.php`, `AuthGroups.php`, or `AuthToken.php` config files.

6. (Optional) Set up automated payouts by adding to crontab:
```bash
*/5 * * * * cd /path/to/justfaucet && php spark faucet:refresh-balance
```


## Technology Stack

| Layer | Technologies |
|-------|---|
| **Backend** | CodeIgniter 4, CodeIgniter Shield |
| **Frontend** | Bootstrap 5, jQuery, Sweetalert2, FontAwesome |
| **Security** | CAPTCHA, VPN Detection, Fraud Prevention |

## Contributing

Contributions are welcome! Feel free to submit issues and pull requests.
