<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Booking extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => 'booking',
            'attributes' => [
                'user_id' => $this->when(
                    $this->user_id == Auth::id(), $this->user_id
                ),
                'room_id' => $this->room_id,
                'start'   => $this->start,
                'end'     => $this->end,
            ],
        ];
    }
}
