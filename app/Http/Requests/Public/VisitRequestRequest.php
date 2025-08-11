<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VisitRequest;

class VisitRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint, no authorization needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $advanceBookingDays = config('lab.services.lab_visit.advance_booking_days', 7);
        $minDate = now()->addDays($advanceBookingDays)->format('Y-m-d');
        $maxDate = now()->addMonths(6)->format('Y-m-d');
        
        $purposes = implode(',', array_keys(VisitRequest::getPurposes()));
        $visitTimes = implode(',', array_keys(VisitRequest::getVisitTimes()));

        return [
            'full_name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20|min:10',
            'institution' => 'required|string|max:255|min:3',
            
            'purpose' => "required|in:{$purposes}",
            'visit_date' => "required|date|after:{$minDate}|before:{$maxDate}",
            'visit_time' => "required|in:{$visitTimes}",
            'participants' => 'required|integer|min:1|max:50',
            
            'additional_notes' => 'nullable|string|max:1000',
            'agreement_accepted' => 'required|accepted',
            'request_letter' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $advanceBookingDays = config('lab.services.lab_visit.advance_booking_days', 7);
        
        return [
            'full_name.required' => 'Full name is required.',
            'full_name.min' => 'Full name must be at least 2 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'institution.required' => 'Institution/organization is required.',
            'institution.min' => 'Institution name must be at least 3 characters.',
            
            'purpose.required' => 'Visit purpose is required.',
            'purpose.in' => 'Please select a valid visit purpose.',
            'visit_date.required' => 'Visit date is required.',
            'visit_date.after' => "Visit must be booked at least {$advanceBookingDays} days in advance.",
            'visit_date.before' => 'Visit date cannot be more than 6 months in advance.',
            'visit_time.required' => 'Visit time is required.',
            'visit_time.in' => 'Please select a valid visit time.',
            
            'participants.required' => 'Number of participants is required.',
            'participants.min' => 'At least 1 participant is required.',
            'participants.max' => 'Maximum 50 participants allowed per visit.',
            
            'agreement_accepted.required' => 'You must accept the terms and conditions.',
            'agreement_accepted.accepted' => 'You must accept the terms and conditions.',
            
            'request_letter.file' => 'Request letter must be a file.',
            'request_letter.mimes' => 'Request letter must be in PDF format.',
            'request_letter.max' => 'Request letter file size must not exceed 5MB.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateVisitSchedule($validator);
        });
    }

    /**
     * Validate visit schedule against operational hours
     */
    private function validateVisitSchedule($validator)
    {
        $visitDate = $this->input('visit_date');
        
        if (!$visitDate) {
            return;
        }
        
        $visitDateTime = \Carbon\Carbon::parse($visitDate);
        $dayOfWeek = strtolower($visitDateTime->format('l'));
        
        // Get lab operational hours from config
        $operationalHours = config('lab.operational_hours');
        $operatingTime = $operationalHours[$dayOfWeek] ?? null;
        
        // Check if lab is closed on selected day
        if ($operatingTime === 'Tutup' || !$operatingTime) {
            $validator->errors()->add(
                'visit_date',
                'Laboratory is closed on ' . ucfirst($dayOfWeek) . '. Please select a different date.'
            );
            return;
        }
        
        // Additional validation for Friday (half day)
        $visitTime = $this->input('visit_time');
        if ($dayOfWeek === 'friday' && $visitTime === 'afternoon') {
            $validator->errors()->add(
                'visit_time',
                'Laboratory closes early on Friday. Only morning visits are available.'
            );
        }
        
        // Check if visit date is a public holiday (you could add this logic)
        // $this->validatePublicHolidays($validator, $visitDateTime);
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'institution' => 'institution/organization',
            'visit_date' => 'visit date',
            'visit_time' => 'visit time',
            'participants' => 'number of participants',
            'additional_notes' => 'additional notes',
            'agreement_accepted' => 'agreement acceptance',
            'request_letter' => 'request letter',
        ];
    }
}