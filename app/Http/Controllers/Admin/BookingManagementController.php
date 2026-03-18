<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
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
                'total_price' => $booking->total_price,
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



    // CREATE BOOKING
    public function create()
    {
        $packages = Package::latest()->get();
        return Inertia::render('admin/booking/CreateBooking', compact('packages'));
    }




    //STORE BOOKING
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required | exists:packages,id',
            'phone' => 'required | numeric',
            'event_date' => 'required | date',
            'total_price' => 'required | numeric',
            'guest_count' => 'required | numeric',
        ]);

        Booking::create($request->all());
        return back()->with('success', 'Booking created successfully.');
    }





    // CANCEL BOOKING
    public function cancel(Booking $booking)
    {
        $booking->status = 'cancelled';
        $booking->save();
        return redirect()->route('admin.bookings')->with('success', 'Booking cancelled successfully.');
    }


    // DELETE BOOKING
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }
}
