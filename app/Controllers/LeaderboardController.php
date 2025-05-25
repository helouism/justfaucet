<?php

namespace App\Controllers;

class LeaderboardController extends BaseController
{
    public function index(): string
    {
        return view('user/leaderboard');
    }
}
