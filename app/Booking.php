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

    public function save(array $options = Array()) {
        if ($this->end <= $this->start) {
            return false;
        }
        if ($this->checkRoomCollision()) {
            return false;
        }
        return parent::save($options);
    }

    public function checkRoomCollision() {
        return DB::table('bookings')
            ->where([
                ['room_id', $this->room_id],
                ['start', '<=', $this->start],
                ['end', '>', $this->start],
            ])
            ->orWhere([
                ['room_id', $this->room_id],
                ['start', '<', $this->end],
                ['end', '>=', $this->end],
            ])
            ->orWhere([
                ['room_id', $this->room_id],
                ['start', '>=', $this->start],
                ['end', '<', $this->end],
            ])
            ->limit(1)->exists();
    }
}
