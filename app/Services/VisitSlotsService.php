<?php

namespace App\Services;

use Carbon\Carbon;

class VisitSlotsService
{
    /**
     * Operating hours configuration
     */
    const START_HOUR = 8;      // 08:00
    const END_HOUR = 16;       // 16:00
    const LUNCH_START = 12;    // 12:00
    const LUNCH_END = 13;      // 13:00

    /**
     * Calculate available time slots for a given date and duration
     *
     * @param string $date
     * @param int $duration
     * @param array $existingBookings
     * @return array
     */
    public function calculateAvailableSlots(string $date, int $duration, array $existingBookings = []): array
    {
        // Generate all possible base slots for the duration
        $baseSlots = $this->generateBaseTimeSlots($duration);
        
        // Filter out slots that conflict with existing bookings
        $availableSlots = $this->filterConflictingSlots($baseSlots, $existingBookings);
        
        return $availableSlots;
    }

    /**
     * Generate base time slots based on duration
     *
     * @param int $duration
     * @return array
     */
    protected function generateBaseTimeSlots(int $duration): array
    {
        $slots = [];
        
        // Generate slots from start hour to end hour minus duration
        for ($hour = self::START_HOUR; $hour <= self::END_HOUR - $duration; $hour++) {
            $endHour = $hour + $duration;
            
            // Skip slots that would overlap with lunch break
            if ($this->overlapsWithLunchBreak($hour, $endHour)) {
                continue;
            }
            
            // Skip slots that would end after operating hours
            if ($endHour > self::END_HOUR) {
                continue;
            }
            
            $startTime = sprintf('%02d:00', $hour);
            $endTime = sprintf('%02d:00', $endHour);
            
            $slots[] = [
                'start' => $startTime,
                'end' => $endTime,
                'display' => sprintf('%s - %s WIB (%d jam)', $startTime, $endTime, $duration)
            ];
        }
        
        return $slots;
    }

    /**
     * Check if a time slot overlaps with lunch break
     *
     * @param int $startHour
     * @param int $endHour
     * @return bool
     */
    protected function overlapsWithLunchBreak(int $startHour, int $endHour): bool
    {
        // Check if the slot overlaps with lunch break (12:00-13:00)
        return ($startHour < self::LUNCH_END && $endHour > self::LUNCH_START);
    }

    /**
     * Filter out slots that conflict with existing bookings
     *
     * @param array $baseSlots
     * @param array $existingBookings
     * @return array
     */
    protected function filterConflictingSlots(array $baseSlots, array $existingBookings): array
    {
        if (empty($existingBookings)) {
            return $baseSlots;
        }
        
        $availableSlots = [];
        
        foreach ($baseSlots as $slot) {
            $slotStart = $this->timeToMinutes($slot['start']);
            $slotEnd = $this->timeToMinutes($slot['end']);
            
            $hasConflict = false;
            
            foreach ($existingBookings as $booking) {
                // Skip bookings with null or empty time values
                if (!$booking['start_time'] || !$booking['end_time']) {
                    continue;
                }
                
                $bookingStart = $this->timeToMinutes($booking['start_time']);
                $bookingEnd = $this->timeToMinutes($booking['end_time']);
                
                // Skip invalid time ranges
                if ($bookingStart >= $bookingEnd) {
                    continue;
                }
                
                // Check if the slot overlaps with this booking
                if ($this->timeSlotsOverlap($slotStart, $slotEnd, $bookingStart, $bookingEnd)) {
                    $hasConflict = true;
                    break;
                }
            }
            
            if (!$hasConflict) {
                $availableSlots[] = $slot;
            }
        }
        
        return $availableSlots;
    }

    /**
     * Check if two time slots overlap
     *
     * @param int $start1
     * @param int $end1
     * @param int $start2
     * @param int $end2
     * @return bool
     */
    protected function timeSlotsOverlap(int $start1, int $end1, int $start2, int $end2): bool
    {
        // Two slots overlap if:
        // - Slot 1 starts before slot 2 ends AND
        // - Slot 1 ends after slot 2 starts
        return ($start1 < $end2) && ($end1 > $start2);
    }

    /**
     * Convert time string (HH:MM) to minutes since midnight
     *
     * @param string $time
     * @return int
     */
    protected function timeToMinutes(?string $time): int
    {
        if (!$time) {
            return 0; // Return 0 for null or empty time
        }
        
        $parts = explode(':', $time);
        $hours = (int) $parts[0];
        $minutes = isset($parts[1]) ? (int) $parts[1] : 0;
        
        return ($hours * 60) + $minutes;
    }

    /**
     * Get existing bookings for a specific date
     *
     * @param string $date
     * @return array
     */
    public function getExistingBookings(string $date): array
    {
        return \App\Models\VisitRequest::where('visit_date', $date)
            ->whereIn('status', ['approved', 'ready', 'active'])
            ->select(['start_time', 'end_time'])
            ->get()
            ->toArray();
    }

    /**
     * Check if a specific time slot is available
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function isSlotAvailable(string $date, string $startTime, string $endTime): bool
    {
        $existingBookings = $this->getExistingBookings($date);
        
        $slotStart = $this->timeToMinutes($startTime);
        $slotEnd = $this->timeToMinutes($endTime);
        
        foreach ($existingBookings as $booking) {
            // Skip bookings with null or empty time values
            if (!$booking['start_time'] || !$booking['end_time']) {
                continue;
            }
            
            $bookingStart = $this->timeToMinutes($booking['start_time']);
            $bookingEnd = $this->timeToMinutes($booking['end_time']);
            
            // Skip invalid time ranges
            if ($bookingStart >= $bookingEnd) {
                continue;
            }
            
            if ($this->timeSlotsOverlap($slotStart, $slotEnd, $bookingStart, $bookingEnd)) {
                return false;
            }
        }
        
        return true;
    }
}