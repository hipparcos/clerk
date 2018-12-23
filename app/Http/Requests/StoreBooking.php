<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
