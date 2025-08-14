<?php

namespace App\Enums;

enum StaffType: string
{
    case DOSEN = 'dosen';
    case LABORAN = 'laboran';
    case TEKNISI = 'teknisi';
    case KEPALA_LABORATORIUM = 'kepala_laboratorium';

    /**
     * Get the display label for the staff type
     */
    public function label(): string
    {
        return match($this) {
            self::DOSEN => 'Dosen',
            self::LABORAN => 'Laboran',
            self::TEKNISI => 'Teknisi',
            self::KEPALA_LABORATORIUM => 'Kepala Laboratorium',
        };
    }

    /**
     * Get all staff types as an array for validation
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all staff types with labels for dropdowns
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get staff type from position string (for backfill migration)
     */
    public static function fromPosition(string $position): self
    {
        $position = strtolower($position);

        if (str_contains($position, 'kepala') || str_contains($position, 'head of lab')) {
            return self::KEPALA_LABORATORIUM;
        }

        if (str_contains($position, 'laboran') || 
            str_contains($position, 'lab manager') || 
            str_contains($position, 'asisten') || 
            str_contains($position, 'technical manager')) {
            return self::LABORAN;
        }

        if (str_contains($position, 'teknisi')) {
            return self::TEKNISI;
        }

        // Default to dosen for research scientists, senior research associates, etc.
        return self::DOSEN;
    }
}