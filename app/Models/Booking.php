<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'package_id',
        'phone',
        'event_date',
        'guest_count',
        'total_price',
        'status',
    ];



    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
