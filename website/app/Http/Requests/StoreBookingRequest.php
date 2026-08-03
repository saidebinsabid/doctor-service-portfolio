<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'slot_time'        => ['required', 'date_format:H:i'],
            'chamber_id'       => ['required', 'integer', 'exists:chambers,id'],

            'patient_name'     => ['required', 'string', 'min:2', 'max:100'],

            /* বাংলাদেশি মোবাইল: 013–019 দিয়ে শুরু, মোট ১১ অঙ্ক।
               নানা ফরম্যাটে লেখা নম্বর আগেই এক রূপে আনা হয় (prepareForValidation)। */
            'patient_phone'    => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],

            'patient_age'      => ['required', 'integer', 'min:0', 'max:200'],
            'patient_age_unit' => ['required', Rule::in(['day', 'month', 'year'])],
            'gender'           => ['nullable', Rule::in(['male', 'female'])],
            'guardian_name'    => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:200'],
            'visit_type'       => ['required', Rule::in(['new', 'followup', 'report'])],
            'problem'          => ['nullable', 'string', 'max:500'],

            /* হানিপট — মানুষ দেখতেই পায় না, বট পূরণ করে ফেলে।
               কিছু লেখা থাকলেই বুকিং বাতিল। */
            'website'          => ['prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'patient_phone' => normalize_bd_phone($this->input('patient_phone')),
            'slot_time'     => substr((string) $this->input('slot_time'), 0, 5),
        ]);
    }

    public function messages(): array
    {
        return [
            'patient_name.required'  => __('validation_custom.name_required'),
            'patient_phone.required' => __('validation_custom.phone_required'),
            'patient_phone.regex'    => __('validation_custom.phone_invalid'),
            'patient_age.required'   => __('validation_custom.age_required'),
            'appointment_date.required' => __('validation_custom.date_required'),
            'slot_time.required'     => __('validation_custom.time_required'),
            'website.prohibited'     => __('validation_custom.spam'),
        ];
    }
}
