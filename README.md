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
4. Rename env to .env
5. Configure your database credentials in the `.env` file
6. Run this command to setup CodeIgniter Shield and follow the instructions
```console
php spark shield:setup
```
8. Edit `App/Config/Email.php` to configure your email credentials
10. Configure your IPHUB and VPNAPI API KEY in `.env` file




### Tech Stack

#### Backend
CodeIgniter 4 with CodeIgniter Shield for Authentication

#### Frontend
- Bootstrap 5
- Sweetalert2
- jQuery
- FontAwesome
