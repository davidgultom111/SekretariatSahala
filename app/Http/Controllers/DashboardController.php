<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Letter;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::count();
        $activeMembers = Member::where('status_aktif', 'Aktif')->count();
        $totalLetters = Letter::count();
        $recentLetters = Letter::with('member')->latest()->take(5)->get();

        return view('dashboard', [
            'totalMembers' => $totalMembers,
            'activeMembbers' => $activeMembers,
            'totalLetters' => $totalLetters,
            'recentLetters' => $recentLetters,
        ]);
    }
}
