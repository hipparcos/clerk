<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return parent::save($options);
    }
}
