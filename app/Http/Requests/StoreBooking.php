<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;
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

    /**
     * validateRoomCollision checks if the request will produce a room collision.
     *
     * @throws ValidationException with 109 status
     */
    public function validateRoomCollision(Validator $validator, Booking $booking)
    {
        if ($booking->checkRoomCollision()) {
            $validator->errors()->add(
                'data.relationships.room.data.id',
                'This room is already in use at that time.'
            );
            $validator->errors()->add('meta.overridable', false);
            throw (new ValidationException($validator))->status(409);
        }
    }

    /**
     * validateUserCollision checks if the request will produce a user collision.
     * This test is bypassed if meta.overrideUserCollision is set to true in the request.
     *
     * @throws ValidationException with 109 status
     */
    public function validateUserCollision(Validator $validator, Booking $booking)
    {
        $override = $this->input('meta.overrideUserCollision');
        if (!$override && $booking->checkUserCollision()) {
            $validator->errors()->add(
                'data.attributes.start',
                'You already have a booking at that time. You may override this warning.'
            );
            $validator->errors()->add('meta.overridable', true);
            throw (new ValidationException($validator))->status(409);
        }
    }

    /**
    * Add room & user collision checks in the validation process.
    *
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $start = new Carbon($this->input('data.attributes.start'));

            $booking = new Booking();
            if ($this->method() == 'PATCH' || $this->method() == 'PUT') {
                $booking->id = $this->route()->parameter('booking');
            }
            $booking->fill([
                'user_id' => Auth::id(),
                'room_id' => $this->input('data.relationships.room.data.id'),
                'start'   => $start,
                'end'     => (clone $start)->addMinutes(
                                 $this->input('data.attributes.duration')
                             ),
            ]);

            $this->validateRoomCollision($validator, $booking);
            $this->validateUserCollision($validator, $booking);
        });
    }
}
