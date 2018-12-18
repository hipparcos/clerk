<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Booking;

class BookingController extends Controller {
    /**
     * index returns a list of all booking for the current user.
     */
    public function index() {
        return response()->json([
            'bookings' => Booking::allForUser(Auth::id()),
        ]);
    }
}
