<?php

namespace App\Http\Requests;

use App\Rules\ReservationDate;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|min:5|max:255',
            'start_reservation' => [
                'required',
                'date',
                new ReservationDate
            ],
            'room_id' => 'required|exists:rooms,id'
        ];

        if (backpack_user()->can('admin.reservations.create')) {
            $rules['user_id'] = 'required|exists:users,id';
        }


        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
