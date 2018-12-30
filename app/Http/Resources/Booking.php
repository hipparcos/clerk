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
            'type' => 'booking',
            'id' => $this->id,
            'attributes' => [
                'start' => json_encode($this->start),
                'end' => json_encode($this->end),
                'duration' => $this->start->diffInMinutes($this->end),
            ],
            'relationships' => [
                'user' => [
                    'data' => new UserResource($this->user),
                ],
                'room' => [
                    'data' => new RoomResource($this->room),
                ],
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request) {
        return [
            'links' => [
                'self' => route('bookings.show', $this->id),
            ],
        ];
    }
}
