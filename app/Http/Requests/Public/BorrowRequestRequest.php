<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use App\Models\Equipment;

class BorrowRequestRequest extends FormRequest
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
        return [
            'members' => 'required|array|min:1|max:10',
            'members.*.name' => 'required|string|max:255',
            'members.*.nim' => 'required|string|max:50',
            'members.*.study_program' => 'required|string|max:255',
            
            'supervisor_name' => 'required|string|max:255',
            'supervisor_nip' => 'required|string|max:50',
            'supervisor_email' => 'required|email|max:255',
            'supervisor_phone' => 'required|string|max:20',
            
            'purpose' => 'required|string|max:1000',
            'borrow_date' => 'required|date|after:today|before:' . now()->addMonths(6)->format('Y-m-d'),
            'return_date' => 'required|date|after:borrow_date|before:' . now()->addMonths(6)->format('Y-m-d'),
            'start_time' => 'required|string|date_format:H:i',
            'end_time' => 'required|string|date_format:H:i|after:start_time',
            
            'equipment_items' => 'required|array|min:1|max:20',
            'equipment_items.*.equipment_id' => 'required|exists:equipment,id',
            'equipment_items.*.quantity_requested' => 'required|integer|min:1|max:100',
            'equipment_items.*.notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'members.required' => 'At least one team member is required.',
            'members.array' => 'Team members must be provided as an array.',
            'members.min' => 'At least one team member is required.',
            'members.max' => 'Maximum 10 team members allowed.',
            
            'members.*.name.required' => 'Member name is required.',
            'members.*.nim.required' => 'Member NIM is required.',
            'members.*.study_program.required' => 'Member study program is required.',
            
            'supervisor_email.email' => 'Please provide a valid supervisor email address.',
            'borrow_date.after' => 'Borrow date must be at least tomorrow.',
            'borrow_date.before' => 'Borrow date cannot be more than 6 months in advance.',
            'return_date.after' => 'Return date must be after borrow date.',
            'return_date.before' => 'Return date cannot be more than 6 months in advance.',
            
            'start_time.regex' => 'Start time must be in HH:MM format (24-hour).',
            'end_time.regex' => 'End time must be in HH:MM format (24-hour).',
            'end_time.after' => 'End time must be after start time.',
            
            'equipment_items.required' => 'At least one equipment item is required.',
            'equipment_items.min' => 'At least one equipment item is required.',
            'equipment_items.max' => 'Maximum 20 equipment items allowed per request.',
            
            'equipment_items.*.equipment_id.required' => 'Equipment selection is required.',
            'equipment_items.*.equipment_id.exists' => 'Selected equipment does not exist.',
            'equipment_items.*.quantity_requested.required' => 'Requested quantity is required.',
            'equipment_items.*.quantity_requested.min' => 'Quantity must be at least 1.',
            'equipment_items.*.quantity_requested.max' => 'Maximum quantity per item is 100.',
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
            if (!$validator->errors()->has('equipment_items')) {
                $this->validateEquipmentAvailability($validator);
            }
            
            $this->validateBorrowDuration($validator);
            $this->validateOperationalHours($validator);
        });
    }

    /**
     * Validate equipment availability
     */
    private function validateEquipmentAvailability($validator)
    {
        $equipmentItems = $this->input('equipment_items', []);
        
        foreach ($equipmentItems as $index => $item) {
            if (!isset($item['equipment_id']) || !isset($item['quantity_requested'])) {
                continue;
            }
            
            $equipment = Equipment::find($item['equipment_id']);
            
            if (!$equipment) {
                $validator->errors()->add(
                    "equipment_items.{$index}.equipment_id",
                    'Equipment not found.'
                );
                continue;
            }
            
            if (!$equipment->isAvailable($item['quantity_requested'])) {
                $validator->errors()->add(
                    "equipment_items.{$index}.quantity_requested",
                    "Equipment '{$equipment->name}' does not have sufficient available quantity. Available: {$equipment->available_quantity}"
                );
            }
        }
    }

    /**
     * Validate borrow duration (max 30 days)
     */
    private function validateBorrowDuration($validator)
    {
        $borrowDate = $this->input('borrow_date');
        $returnDate = $this->input('return_date');
        
        if ($borrowDate && $returnDate) {
            $borrowDateTime = \Carbon\Carbon::parse($borrowDate);
            $returnDateTime = \Carbon\Carbon::parse($returnDate);
            $duration = $borrowDateTime->diffInDays($returnDateTime);
            
            if ($duration > 30) {
                $validator->errors()->add('return_date', 'Maximum borrow duration is 30 days.');
            }
        }
    }

    /**
     * Validate operational hours
     */
    private function validateOperationalHours($validator)
    {
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');
        $borrowDate = $this->input('borrow_date');
        
        if ($startTime && $endTime && $borrowDate) {
            // Get lab operational hours from config
            $operationalHours = config('lab.operational_hours');
            $borrowDateTime = \Carbon\Carbon::parse($borrowDate);
            $dayOfWeek = strtolower($borrowDateTime->format('l'));
            
            $operatingTime = $operationalHours[$dayOfWeek] ?? null;
            
            if ($operatingTime === 'Tutup' || !$operatingTime) {
                $validator->errors()->add('borrow_date', 'Laboratory is closed on ' . ucfirst($dayOfWeek) . '.');
                return;
            }
            
            // Parse operating hours (e.g., "08:00-16:00")
            if (preg_match('/^(\d{2}:\d{2})-(\d{2}:\d{2})$/', $operatingTime, $matches)) {
                $openTime = $matches[1];
                $closeTime = $matches[2];
                
                if ($startTime < $openTime || $endTime > $closeTime) {
                    $validator->errors()->add('start_time', "Operating hours on {$dayOfWeek}: {$operatingTime}");
                }
            }
        }
    }
}