# JustFaucet - A Simple Crypto Faucet Website built with CodeIgniter 4

## What is JustFaucet?

A simple, secure crypto faucet website with comprehensive fraud prevention and admin management features.

### Installation Steps

1. Clone this repo
```console
git clone https://github.com/helouism/justfaucet.git
```
2. Install package dependencies
```console
composer install
```
4. Rename `env.example` to `.env`
5. Configure your database and email credentials in the `.env` file, do not overwrite the `Auth.php`, `AuthGroups.php`, `AuthToken.php` file
6. Run this command to setup CodeIgniter Shield and follow the instructions
```console
php spark shield:setup
```

10. Configure your FAUCETPAY, IPHUB, VPNAPI API KEY in `.env` file
11. Setup your faucet settings like cooldown, base amount and referral bonus commission percentage in the `.env` file

12. To set up the cron job on your server to run every 5 minutes, add this to your crontab:
```console
*/5 * * * * cd /path/to/your/project && php spark faucet:refresh-balance
```

### Features
- Admin Dashboard
- 5 Minutes Faucet Cooldown
- VPN/Proxy/Tor abuse detection
- bot detection with hCaptcha
- Manual and auto ban(based on suspicious activity)


### Tech Stack

#### Backend
CodeIgniter 4 with CodeIgniter Shield for Authentication

#### Frontend
- Bootstrap 5
- Sweetalert2
- jQuery
- FontAwesome
