<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Spektroskopi',
                'description' => 'Peralatan spektroskopi untuk analisis dan karakterisasi material',
                'color_code' => '#3B82F6',
                'icon_class' => 'fas fa-chart-line',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Optik dan Laser',
                'description' => 'Peralatan optik, laser, dan sistem interferometri',
                'color_code' => '#EF4444',
                'icon_class' => 'fas fa-lightbulb',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Mikroskopi',
                'description' => 'Mikroskop optik, elektron, dan peralatan imaging',
                'color_code' => '#10B981',
                'icon_class' => 'fas fa-microscope',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Elektronik dan Instrumentasi',
                'description' => 'Peralatan elektronik, sensor, dan sistem instrumentasi',
                'color_code' => '#8B5CF6',
                'icon_class' => 'fas fa-microchip',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Gelombang dan Getaran',
                'description' => 'Peralatan untuk studi gelombang, getaran, dan akustik',
                'color_code' => '#F59E0B',
                'icon_class' => 'fas fa-wave-square',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Komputasi dan Software',
                'description' => 'Perangkat keras komputer dan software analisis',
                'color_code' => '#6B7280',
                'icon_class' => 'fas fa-laptop',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Peralatan Umum',
                'description' => 'Peralatan pendukung laboratorium umum',
                'color_code' => '#78716C',
                'icon_class' => 'fas fa-tools',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Keselamatan dan Proteksi',
                'description' => 'Alat pelindung diri dan peralatan keselamatan laboratorium',
                'color_code' => '#DC2626',
                'icon_class' => 'fas fa-shield-alt',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ Created ' . count($categories) . ' equipment categories');
    }
}
