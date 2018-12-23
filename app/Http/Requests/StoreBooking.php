<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Booking;

class StoreBooking extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.attributes.start' => 'required|date',
            'data.attributes.duration' => 'required|integer|min:1',
            'data.relationships.room.data.id' => 'required|exists:rooms,id',
            'meta.overrideUserCollision' => 'boolean',
        ];
    }

    /**
    * Get custom attributes for validator errors.
    *
    * @return array
    */
    public function attributes()
    {
        return [
            'data.attributes.start' => 'start time',
            'data.attributes.duration' => 'duration',
            'data.relationships.room.data.id' => 'room',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            'data.attributes.duration.min' => 'The :attribute must be at least 1 minute.',
        ];
    }

    public function validateRoomCollision(Validator $validator, Booking $booking)
    {
        if ($booking->checkRoomCollision()) {
            $validator->errors()->add(
                'data.attributes.room.data.id', 'This room is already in use at that time.'
            );
        }
    }

    public function validateUserCollision(Validator $validator, Booking $booking)
    {
        $override = $this->input('meta.overrideUserCollision');
        if (!$override && $booking->checkUserCollision()) {
            $validator->errors()->add(
                'data.attributes.start', 'You already have a booking at that time. You may override this warning.'
            );
        }
    }

    /**
    * Configure the validator instance.
    *
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $start = new Carbon($this->input('data.attributes.start'));
            $booking = new Booking([
                'user_id' => Auth::id(),
                'room_id' => $this->input('data.relationships.room.data.id'),
                'start' => $start,
                'end'   => (clone $start)->addMinutes(
                    $this->input('data.attributes.duration')
                ),
            ]);

            $this->validateRoomCollision($validator, $booking);
            $this->validateUserCollision($validator, $booking);
        });
    }
}
