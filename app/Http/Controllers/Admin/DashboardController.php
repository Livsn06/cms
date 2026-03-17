<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/dashboard/Dashboard', [
            'stats' => [
                'totalBookings' => Booking::count(),
                'activePackages' => Package::count(),

                'totalRevenue' => number_format(Booking::join('packages', 'bookings.package_id', '=', 'packages.id')
                    ->where('status', '!=', 'cancelled')
                    ->sum('packages.price'), 2),
                'bookingTrend' => '-20%',
            ]
        ]);
    }
}
