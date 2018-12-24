<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    public $timestamps = false;

    protected $dates = [
        'start', 'end',
    ];

    protected $casts = [
        'start' => 'datetime:Y-m-d H:i:s',
        'end'   => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'user_id', 'room_id', 'start', 'end'
    ];

    /**
     * Get the user that owns this booking.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the room associated with this booking.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function save(array $options = Array()) {
        if ($this->end <= $this->start) {
            return false;
        }
        if ($this->checkRoomCollision()) {
            return false;
        }
        return parent::save($options);
    }

    /**
     * checkCollision return true if this meeting overlaps or surrounds
     * an other booking.
     * Support room collision and user collision.
     *
     * @param $column is either room_id or user_id.
     * @param $value is the room_id or user_id to check for collision.
     */
    protected function checkCollision($column, $value) {
        return DB::table('bookings')
            ->where([
                [$column, $value],
                ['start', '<=', $this->start],
                ['end', '>', $this->start],
            ])
            ->orWhere([
                [$column, $value],
                ['start', '<', $this->end],
                ['end', '>=', $this->end],
            ])
            ->orWhere([
                [$column, $value],
                ['start', '>=', $this->start],
                ['end', '<', $this->end],
            ])
            ->limit(1)->exists();
    }

    /**
     * checkRoomCollision returns true is this room is already booked at
     * the same time.
     */
    public function checkRoomCollision() {
        return $this->checkCollision('room_id', $this->room_id);
    }

    /**
     * checkUserCollision returns true if the user who books this booking has
     * an other booking at the same time.
     */
    public function checkUserCollision() {
        return $this->checkCollision('user_id', $this->user_id);
    }
}
