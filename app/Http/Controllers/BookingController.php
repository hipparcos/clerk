<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Booking;
use DateTime;
use DateInterval;

class BookingController extends Controller {
    /**
     * index returns a list of all booking for the current user.
     */
    public function index() {
        return response()->json([
            'data' => Booking::allForUser(Auth::id()),
        ]);
    }

    /**
     * create creates a booking.
     */
    public function create(Request $request) {
        $start = new DateTime($request->input('start'));
        $end = (clone $start)
            ->add(new DateInterval('PT'. $request->input('end') .'M'));

        $booking = new Booking;
        $booking->user_id = Auth::id();
        $booking->room_id = $request->input('room_id');
        $booking->start = $start;
        $booking->end = $end;

        if (!$booking->save()) {
            return response()->json([], 422);
        }

        return response()->json(['data' => ['id' => $booking->id]], 201);
    }
}
