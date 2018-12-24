<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Booking;
use App\Http\Resources\Booking as BookingResource;
use App\Http\Resources\BookingCollection;
use App\Http\Requests\StoreBooking;
use Carbon\Carbon;

class BookingController extends Controller {
    /**
     * index returns a list of all booking for the current user.
     */
    public function index(Request $request) {
        return response()
            ->json(new BookingCollection(
                Auth::user()->bookings()->get()
            ), 200);
    }

    /**
     * show returns the details of a booking.
     */
    public function show(Request $request, $id) {
        $booking = Booking::find($id);
        if ($booking == null) {
            return response()->json(null, 404);
        }
        return response()
            ->json([
                'links' => [
                    'self' => route('bookings.show', $booking->id)
                ],
                'data' => new BookingResource($booking),
            ], 200);
    }

    /**
     * store creates a booking.
     */
    public function store(StoreBooking $request) {
        $data = $request->validated()['data'];

        $start = new Carbon($data['attributes']['start']);
        $end = (clone $start)->addMinutes($data['attributes']['duration']);

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

    /**
     * update updates an existing booking.
     */
    public function update(StoreBooking $request, $id) {
        $booking = Booking::where([
            ['id', '=', $id],
            ['user_id', '=', Auth::id()],
        ])->firstOrFail();

        $data = $request->validated()['data'];

        $booking->room_id = $data['relationships']['room']['data']['id'];
        $booking->start = new Carbon($data['attributes']['start']);
        $booking->end = (clone $booking->start)
            ->addMinutes($data['attributes']['duration']);

        if (!$booking->save()) {
            return response()->json(null, 422);
        }

        return response()
            ->json([
                'links' => [
                    'self' => route('bookings.show', $booking->id)
                ],
                'data' => new BookingResource($booking),
            ], 200);
    }

    /**
     * destroy deletes a booking.
     */
    public function destroy(Request $request, $id) {
        Booking::where([
            ['id', '=', $id],
            ['user_id', '=', Auth::id()],
        ])->firstOrFail()->delete();
        return response()->json(null, 200);
    }
}
