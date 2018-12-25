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
     * index returns a list of all booking for a given day.
     * If no year/month/day are provided, index returns the bookings of today.
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function index(Request $request, int $year = null, int $month = null, int $day = null) {
        if ($year && $month && $day) {
            $date = Carbon::create($year, $month, $day, 0, 0, 0, 'UTC');
        } else {
            $date = Carbon::now();
        }
        $bookings = Booking::onDate($date);
        return new BookingCollection($bookings);
    }

    /**
     * show returns the details of a booking.
     */
    public function show(Request $request, $id) {
        $booking = Booking::findOrFail($id);
        return new BookingResource($booking);
    }

    /**
     * store creates a booking.
     */
    public function store(StoreBooking $request) {
        $data = $request->validated()['data'];
        $start = new Carbon($data['attributes']['start']);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $data['relationships']['room']['data']['id'],
            'start'   => $start,
            'end'     => (clone $start)->addMinutes($data['attributes']['duration']),
        ]);
        if (!$booking->id) {
            abort(500, 'Booking has been validated but creation has failed.');
        }

        return (new BookingResource($booking))->response(201)
            ->header('Location', route('bookings.show', ['id' => $booking->id]));
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
        $start = new Carbon($data['attributes']['start']);

        $booking->update([
            'room_id' => $data['relationships']['room']['data']['id'],
            'start'   => $start,
            'end'     => (clone $start)->addMinutes($data['attributes']['duration']),
        ]);

        return new BookingResource($booking);
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
