<?php

namespace App\Controllers;

class ReferralController extends BaseController
{
    public function index(): string
    {
        return view('user/referral');
    }
}
