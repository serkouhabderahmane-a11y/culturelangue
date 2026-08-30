<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'students_count' => User::role('student')->count(),
            'teachers_count' => User::role('teacher')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_bookings' => Booking::count(),
        ];

        $recentBookings = Booking::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }

    public function analytics()
    {
        return view('admin.analytics');
    }

    public function payments()
    {
        return view('admin.payments');
    }
}
