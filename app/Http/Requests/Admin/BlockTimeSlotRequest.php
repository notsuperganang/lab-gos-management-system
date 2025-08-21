<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\BlockedTimeSlot;

class BlockTimeSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Check if this is a bulk operation (multiple slots)
        $isBulk = $this->has('slots');

        if ($isBulk) {
            return [
                'slots' => 'required|array|min:1|max:10',
                'slots.*.date' => [
                    'required',
                    'date',
                    'after:today',
                    function ($attribute, $value, $fail) {
                        $date = Carbon::parse($value);
                        if ($date->isWeekend()) {
                            $fail('Cannot block slots on weekends. Laboratory operates Monday - Friday only.');
                        }
                    }
                ],
                'slots.*.start_time' => [
                    'required',
                    'date_format:H:i',
                    'before:slots.*.end_time',
                    function ($attribute, $value, $fail) {
                        if (!$this->isWithinOperatingHours($value)) {
                            $fail('Start time must be within operating hours (08:00 - 16:00, excluding lunch 12:00-13:00).');
                        }
                    }
                ],
                'slots.*.end_time' => [
                    'required',
                    'date_format:H:i',
                    'after:slots.*.start_time',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1]; // Get array index
                        $startTime = $this->input("slots.{$index}.start_time");
                        
                        if ($startTime && !$this->isValidTimeRange($startTime, $value)) {
                            $fail('Time range must be within operating hours and not overlap lunch break.');
                        }
                    }
                ],
                'slots.*.reason' => 'nullable|string|max:500',
            ];
        } else {
            return [
                'date' => [
                    'required',
                    'date',
                    'after:today',
                    function ($attribute, $value, $fail) {
                        $date = Carbon::parse($value);
                        if ($date->isWeekend()) {
                            $fail('Cannot block slots on weekends. Laboratory operates Monday - Friday only.');
                        }
                    }
                ],
                'start_time' => [
                    'required',
                    'date_format:H:i',
                    'before:end_time',
                    function ($attribute, $value, $fail) {
                        if (!$this->isWithinOperatingHours($value)) {
                            $fail('Start time must be within operating hours (08:00 - 16:00, excluding lunch 12:00-13:00).');
                        }
                    }
                ],
                'end_time' => [
                    'required',
                    'date_format:H:i',
                    'after:start_time',
                    function ($attribute, $value, $fail) {
                        $startTime = $this->input('start_time');
                        
                        if ($startTime && !$this->isValidTimeRange($startTime, $value)) {
                            $fail('Time range must be within operating hours and not overlap lunch break.');
                        }
                    }
                ],
                'reason' => 'nullable|string|max:500',
            ];
        }
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Date is required.',
            'date.date' => 'Please provide a valid date.',
            'date.after' => 'Cannot block time slots in the past. Date must be tomorrow or later.',
            
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be in HH:MM format (e.g., 09:00).',
            'start_time.before' => 'Start time must be before end time.',
            
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'End time must be in HH:MM format (e.g., 10:00).',
            'end_time.after' => 'End time must be after start time.',
            
            'reason.string' => 'Reason must be text.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
            
            'slots.required' => 'At least one time slot is required.',
            'slots.array' => 'Time slots must be provided as an array.',
            'slots.min' => 'At least one time slot is required.',
            'slots.max' => 'Cannot block more than 10 time slots at once.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('slots')) {
                $this->validateBulkSlots($validator);
            } else {
                $this->validateSingleSlot($validator);
            }
        });
    }

    /**
     * Validate bulk slot operations.
     */
    protected function validateBulkSlots($validator): void
    {
        $slots = $this->input('slots', []);
        
        foreach ($slots as $index => $slot) {
            if (!isset($slot['date'], $slot['start_time'], $slot['end_time'])) {
                continue;
            }

            // Check for duplicate blocked slots
            $existingBlock = BlockedTimeSlot::where('date', $slot['date'])
                ->where('start_time', $slot['start_time'])
                ->where('end_time', $slot['end_time'])
                ->first();

            if ($existingBlock) {
                $validator->errors()->add(
                    "slots.{$index}.start_time",
                    "Time slot {$slot['start_time']} - {$slot['end_time']} on {$slot['date']} is already blocked."
                );
            }

            // Check for overlapping blocked slots
            $overlappingBlocks = BlockedTimeSlot::where('date', $slot['date'])
                ->where(function ($query) use ($slot) {
                    $query->where(function ($q) use ($slot) {
                        // New slot starts within existing block
                        $q->where('start_time', '<=', $slot['start_time'])
                          ->where('end_time', '>', $slot['start_time']);
                    })->orWhere(function ($q) use ($slot) {
                        // New slot ends within existing block
                        $q->where('start_time', '<', $slot['end_time'])
                          ->where('end_time', '>=', $slot['end_time']);
                    })->orWhere(function ($q) use ($slot) {
                        // New slot encompasses existing block
                        $q->where('start_time', '>=', $slot['start_time'])
                          ->where('end_time', '<=', $slot['end_time']);
                    });
                })
                ->exists();

            if ($overlappingBlocks) {
                $validator->errors()->add(
                    "slots.{$index}.start_time",
                    "Time slot {$slot['start_time']} - {$slot['end_time']} overlaps with an existing blocked slot."
                );
            }
        }

        // Check for duplicates within the submitted slots
        $uniqueSlots = [];
        foreach ($slots as $index => $slot) {
            if (!isset($slot['date'], $slot['start_time'], $slot['end_time'])) {
                continue;
            }

            $slotKey = $slot['date'] . '_' . $slot['start_time'] . '_' . $slot['end_time'];
            
            if (in_array($slotKey, $uniqueSlots)) {
                $validator->errors()->add(
                    "slots.{$index}",
                    "Duplicate time slot detected in the submission."
                );
            } else {
                $uniqueSlots[] = $slotKey;
            }
        }
    }

    /**
     * Validate single slot operation.
     */
    protected function validateSingleSlot($validator): void
    {
        $date = $this->input('date');
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');

        if (!$date || !$startTime || !$endTime) {
            return; // Basic validation will handle missing fields
        }

        // Check for existing blocked slot
        $existingBlock = BlockedTimeSlot::where('date', $date)
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->first();

        if ($existingBlock) {
            $validator->errors()->add('start_time', "This time slot is already blocked.");
        }

        // Check for overlapping blocked slots
        $overlappingBlocks = BlockedTimeSlot::where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime) {
                    // New slot starts within existing block
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($endTime) {
                    // New slot ends within existing block
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New slot encompasses existing block
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->exists();

        if ($overlappingBlocks) {
            $validator->errors()->add('start_time', "This time slot overlaps with an existing blocked slot.");
        }
    }

    /**
     * Check if time is within operating hours.
     */
    protected function isWithinOperatingHours(string $time): bool
    {
        $timeMinutes = $this->timeToMinutes($time);
        $startMinutes = 8 * 60; // 08:00
        $endMinutes = 16 * 60;  // 16:00

        return $timeMinutes >= $startMinutes && $timeMinutes <= $endMinutes;
    }

    /**
     * Check if time range is valid (within operating hours and doesn't overlap lunch).
     */
    protected function isValidTimeRange(string $startTime, string $endTime): bool
    {
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        
        // Operating hours: 08:00-16:00
        $operatingStart = 8 * 60;  // 08:00 = 480 minutes
        $operatingEnd = 16 * 60;   // 16:00 = 960 minutes
        
        // Lunch break: 12:00-13:00
        $lunchStart = 12 * 60;     // 12:00 = 720 minutes
        $lunchEnd = 13 * 60;       // 13:00 = 780 minutes

        // Check if within operating hours
        if ($startMinutes < $operatingStart || $endMinutes > $operatingEnd) {
            return false;
        }

        // Check if overlaps with lunch break
        if ($startMinutes < $lunchEnd && $endMinutes > $lunchStart) {
            return false;
        }

        return true;
    }

    /**
     * Convert time string (HH:MM) to minutes since midnight.
     */
    protected function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        $hours = (int) $parts[0];
        $minutes = isset($parts[1]) ? (int) $parts[1] : 0;
        
        return ($hours * 60) + $minutes;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'start_time' => 'start time',
            'end_time' => 'end time',
            'slots.*.date' => 'date',
            'slots.*.start_time' => 'start time',
            'slots.*.end_time' => 'end time',
            'slots.*.reason' => 'reason',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure consistent time format (add :00 if only HH format provided)
        if ($this->has('start_time')) {
            $startTime = $this->input('start_time');
            if (preg_match('/^\d{1,2}$/', $startTime)) {
                $this->merge(['start_time' => sprintf('%02d:00', (int) $startTime)]);
            }
        }

        if ($this->has('end_time')) {
            $endTime = $this->input('end_time');
            if (preg_match('/^\d{1,2}$/', $endTime)) {
                $this->merge(['end_time' => sprintf('%02d:00', (int) $endTime)]);
            }
        }

        // Handle bulk slots time formatting
        if ($this->has('slots')) {
            $slots = $this->input('slots', []);
            foreach ($slots as $index => $slot) {
                if (isset($slot['start_time']) && preg_match('/^\d{1,2}$/', $slot['start_time'])) {
                    $slots[$index]['start_time'] = sprintf('%02d:00', (int) $slot['start_time']);
                }
                if (isset($slot['end_time']) && preg_match('/^\d{1,2}$/', $slot['end_time'])) {
                    $slots[$index]['end_time'] = sprintf('%02d:00', (int) $slot['end_time']);
                }
            }
            $this->merge(['slots' => $slots]);
        }
    }
}