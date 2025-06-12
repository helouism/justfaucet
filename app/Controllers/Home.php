<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {


        // Get referral ID from URL
        $referredBy = $this->request->getGet('ref');
        if ($referredBy) {
            session()->set('referred_by', $referredBy);
        }
        $data = [
            'title' => 'JustFaucet',
            'referred_by' => $referredBy

        ];

        return view('homepage', $data);
    }
}
