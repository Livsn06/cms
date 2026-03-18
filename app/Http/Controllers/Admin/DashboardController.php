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
        // Define time ranges
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sixtyDaysAgo = Carbon::now()->subDays(60);

        // 1. Get Bookings Count
        $currentBookings = Booking::where('created_at', '>=', $thirtyDaysAgo)->count();
        $previousBookings = Booking::where('created_at', '>=', $sixtyDaysAgo)
            ->where('created_at', '<', $thirtyDaysAgo)
            ->count();

        // 2. Calculate Booking Trend Percentage
        $bookingTrend = 0;
        if ($previousBookings > 0) {
            $bookingTrend = (($currentBookings - $previousBookings) / $previousBookings) * 100;
        }

        // 3. Get Package Trend (comparing this month's new packages vs last month)
        $currentPackages = Package::where('created_at', '>=', $thirtyDaysAgo)->count();
        $previousPackages = Package::where('created_at', '<', $thirtyDaysAgo)->count();

        $packageTrend = 0;
        if ($previousPackages > 0) {
            // Growth of the library total
            $packageTrend = ($currentPackages / $previousPackages) * 100;
        }

        return Inertia::render('admin/dashboard/Dashboard', [
            'stats' => [
                'totalBookings' => Booking::count(),
                'bookingTrend' => round($bookingTrend, 1) . '%', // e.g., "-20.5%"

                'activePackages' => Package::count(),
                'packageTrend' => '+' . round($packageTrend, 1) . '%',

                'totalRevenue' => Booking::where('status', '!=', 'cancelled')->sum('total_price'),
            ]
        ]);
    }
}
