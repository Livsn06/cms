<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('package')->latest()->get();

        // GET PACKAGE ASSET IMAGE LINK
        $bookingData = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'phone' => $booking->phone,
                'event_date' => $booking->event_date,
                'guest_count' => $booking->guest_count,
                'status' => $booking->status,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'package' => [
                    'id' => $booking->package->id,
                    'name' => $booking->package->name,
                    'image' => $booking->package->image ? asset('storage/' . $booking->package->image) : null,
                    'price' => $booking->package->price,
                    'description' => $booking->package->description
                ]
            ];
        });
        return Inertia::render('admin/booking/BookingManagement', compact('bookingData'));
    }
}
