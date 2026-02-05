<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255', // nullable for those who don't want to give it
            'gender' => 'required|string',
            'birthdate' => 'required|date',
            'selected_schedule_id' => 'required|exists:appointment_schedules,id',
            'selected_time_slot_id' => 'required|exists:appointment_slots,id',

            'selected_type_ids' => 'required|array|min:1',
            'selected_type_ids.*' => 'exists:test_types,id',
        ];
    }
}
