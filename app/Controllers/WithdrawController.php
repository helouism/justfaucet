<?php

namespace App\Controllers;

class WithdrawController extends BaseController
{
    public function index(): string
    {
        return view('user/withdraw');
    }
}
