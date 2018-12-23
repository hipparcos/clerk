<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Booking;
use App\Http\Resources\Booking as BookingResource;
use App\Http\Resources\BookingCollection;
use DateTime;
use DateInterval;

class BookingController extends Controller {
    /**
     * index returns a list of all booking for the current user.
     */
    public function index(Request $request) {
        return new BookingCollection(
            Auth::user()->bookings()->get()
        );
    }

    /**
     * store creates a booking.
     */
    public function store(Request $request) {
        $data = $request->only([
            'data.attributes.start',
            'data.attributes.duration',
            'data.relationships.room.data.id',
        ])['data'];

        $start = new DateTime($data['attributes']['start']);
        $end = (clone $start)->add(
            new DateInterval('PT'.$data['attributes']['duration'].'M')
        );

        $booking = new Booking([
            'user_id' => Auth::id(),
            'room_id' => $data['relationships']['room']['data']['id'],
            'start'   => $start,
            'end'     => $end,
        ]);

        if (!$booking->save()) {
            return response()->json(null, 422);
        }

        return response()
            ->json([
                'data' => new BookingResource($booking),
            ], 201)
            ->withHeaders([
                'Location', route('bookings.show', ['id' => $booking->id])
            ]);
    }
}
