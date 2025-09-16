<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Settings to remove - those not in the user's required list
        $settingsToRemove = [
            // From Content settings
            'footer_text',
            'safety_policy',
            'equipment_usage_policy',
            // From JSON settings
            'operating_hours',
            'services',
            'research_areas',
            'lab_logo',
            'institution_logo',
        ];

        // Remove unwanted settings from database
        SiteSetting::whereIn('key', $settingsToRemove)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the removed settings with basic structure
        // This allows rollback but content will need to be re-entered
        $settingsToRestore = [
            [
                'key' => 'footer_text',
                'title' => 'Teks Footer',
                'content' => '',
                'type' => 'text',
                'is_active' => false,
            ],
            [
                'key' => 'safety_policy',
                'title' => 'Kebijakan Keselamatan',
                'content' => '',
                'type' => 'rich_text',
                'is_active' => false,
            ],
            [
                'key' => 'equipment_usage_policy',
                'title' => 'Kebijakan Penggunaan Peralatan',
                'content' => '',
                'type' => 'rich_text',
                'is_active' => false,
            ],
            [
                'key' => 'operating_hours',
                'title' => 'Jam Operasional',
                'content' => '{}',
                'type' => 'json',
                'is_active' => false,
            ],
            [
                'key' => 'services',
                'title' => 'Layanan Laboratorium',
                'content' => '{}',
                'type' => 'json',
                'is_active' => false,
            ],
            [
                'key' => 'research_areas',
                'title' => 'Bidang Penelitian',
                'content' => '[]',
                'type' => 'json',
                'is_active' => false,
            ],
            [
                'key' => 'lab_logo',
                'title' => 'Logo Laboratorium',
                'content' => '',
                'type' => 'image',
                'is_active' => false,
            ],
            [
                'key' => 'institution_logo',
                'title' => 'Logo Institusi',
                'content' => '',
                'type' => 'image',
                'is_active' => false,
            ],
        ];

        foreach ($settingsToRestore as $setting) {
            SiteSetting::create($setting);
        }
    }
};