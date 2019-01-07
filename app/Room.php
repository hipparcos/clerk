<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;

    /**
     * Get the bookings for this room.
     */
    public function bookings()
    {
        return $this->hasMany('App\Booking');
    }
}
