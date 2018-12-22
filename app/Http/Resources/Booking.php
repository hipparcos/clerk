<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Room as RoomResource;
use App\Http\Resources\User as UserResource;

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
                'start' => $this->start,
                'end' => $this->end,
            ],
            'relationships' => [
                'user' => $this->when(
                    $this->user_id == Auth::id(), new UserResource($this->user)
                ),
                'room' => [
                    'data' => new RoomResource($this->room),
                ],
            ],
        ];
    }
}
