<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Package $package)
    {
        $package = [
            'id' => $package->id,
            'name' => $package->name,
            'price' => $package->price,
            'image' => $package->image ? asset('storage/' . $package->image) : null,
            'description' => $package->description
        ];
        return Inertia::render('client/booking/Booking', compact('package'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'phone' => 'required | min:10 | max:20',
            'event_date' => 'required | date',
            'guest_count' => 'required | numeric',
            'total_price' => 'required | numeric'
        ]);

        $booking = Booking::create($request->all());

        return redirect()->route('booking.success', $booking->id);
    }



    public function success(Booking $booking)
    {
        $package = $booking->package;
        $package = [
            'id' => $package->id,
            'name' => $package->name,
            'price' => $package->price,
            'image' => $package->image ? asset('storage/' . $package->image) : null,
            'description' => $package->description
        ];
        $booking = [
            'id' => $booking->id,
            'phone' => $booking->phone,
            'event_date' => $booking->event_date,
            'guest_count' => $booking->guest_count,
            'total_price' => $booking->total_price,
            'status' => $booking->status,
            'package' => $package
        ];
        return Inertia::render('client/booking/BookingSuccess', compact('booking'));
    }
}
