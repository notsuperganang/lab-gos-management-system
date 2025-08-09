<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $spektroskopi = Category::where('name', 'Spektroskopi')->first();
        $optik = Category::where('name', 'Optik dan Laser')->first();
        $mikroskopi = Category::where('name', 'Mikroskopi')->first();
        $elektronik = Category::where('name', 'Elektronik dan Instrumentasi')->first();
        $gelombang = Category::where('name', 'Gelombang dan Getaran')->first();
        $komputasi = Category::where('name', 'Komputasi dan Software')->first();
        $umum = Category::where('name', 'Peralatan Umum')->first();
        $keselamatan = Category::where('name', 'Keselamatan dan Proteksi')->first();

        $equipment = [
            // Spektroskopi
            [
                'name' => 'UV-Vis Spectrophotometer',
                'category_id' => $spektroskopi->id,
                'model' => 'UV-2600i',
                'manufacturer' => 'Shimadzu',
                'specifications' => [
                    'wavelength_range' => '185-900 nm',
                    'resolution' => '0.1 nm',
                    'accuracy' => '±0.2 nm',
                    'light_source' => 'Deuterium and Halogen lamps',
                    'detector' => 'Photomultiplier tube',
                    'power' => '220V, 50Hz, 300W',
                ],
                'total_quantity' => 2,
                'available_quantity' => 2,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-03-15',
                'purchase_price' => 125000000.00,
                'location' => 'Lab Spektroskopi - Ruang A201',
                'image_path' => 'equipment/uv-vis-spectrophotometer.jpg',
                'manual_file_path' => 'manuals/uv-vis-spectrophotometer-manual.pdf',
                'notes' => 'Kalibrasi dilakukan setiap 6 bulan. Terakhir dikalibrasi pada Januari 2024.',
                'last_maintenance_date' => '2024-01-15',
                'next_maintenance_date' => '2024-07-15',
            ],
            [
                'name' => 'FTIR Spectrometer',
                'category_id' => $spektroskopi->id,
                'model' => 'Spectrum Two',
                'manufacturer' => 'PerkinElmer',
                'specifications' => [
                    'wavelength_range' => '7800-350 cm⁻¹',
                    'resolution' => '0.5 cm⁻¹',
                    'detector' => 'DTGS',
                    'beam_splitter' => 'KBr',
                    'interferometer' => 'RockSolid',
                    'software' => 'Spectrum 10',
                ],
                'total_quantity' => 1,
                'available_quantity' => 1,
                'status' => 'active',
                'condition_status' => 'good',
                'purchase_date' => '2022-08-20',
                'purchase_price' => 185000000.00,
                'location' => 'Lab Spektroskopi - Ruang A202',
                'image_path' => 'equipment/ftir-spectrometer.jpg',
                'manual_file_path' => 'manuals/ftir-spectrometer-manual.pdf',
                'notes' => 'Memerlukan purging nitrogen untuk hasil optimal.',
                'last_maintenance_date' => '2023-12-10',
                'next_maintenance_date' => '2024-06-10',
            ],

            // Optik dan Laser
            [
                'name' => 'He-Ne Laser',
                'category_id' => $optik->id,
                'model' => '1508P-0',
                'manufacturer' => 'JDS Uniphase',
                'specifications' => [
                    'wavelength' => '632.8 nm',
                    'power_output' => '0.8 mW',
                    'beam_diameter' => '0.8 mm',
                    'polarization' => '>500:1',
                    'stability' => '±2%',
                    'operating_voltage' => '1800V DC',
                ],
                'total_quantity' => 5,
                'available_quantity' => 4,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-09-10',
                'purchase_price' => 15000000.00,
                'location' => 'Lab Optik - Ruang B101',
                'image_path' => 'equipment/he-ne-laser.jpg',
                'manual_file_path' => 'manuals/he-ne-laser-manual.pdf',
                'notes' => 'Gunakan kacamata pengaman saat operasi. Masa hidup tube ±20,000 jam.',
                'last_maintenance_date' => '2024-01-20',
                'next_maintenance_date' => '2024-07-20',
            ],
            [
                'name' => 'Optical Bench 1.5m',
                'category_id' => $optik->id,
                'model' => 'OB-1500',
                'manufacturer' => 'PASCO Scientific',
                'specifications' => [
                    'length' => '1.5 meters',
                    'material' => 'Aluminum extrusion',
                    'scale' => 'Metric graduations',
                    'accessories' => 'Component holders, lens mounts',
                    'precision' => '±0.5 mm',
                ],
                'total_quantity' => 8,
                'available_quantity' => 7,
                'status' => 'active',
                'condition_status' => 'good',
                'purchase_date' => '2022-02-28',
                'purchase_price' => 8500000.00,
                'location' => 'Lab Optik - Ruang B102',
                'image_path' => 'equipment/optical-bench.jpg',
                'manual_file_path' => 'manuals/optical-bench-manual.pdf',
                'notes' => 'Lengkap dengan set lensa dan komponen optik.',
            ],

            // Mikroskopi
            [
                'name' => 'Optical Microscope',
                'category_id' => $mikroskopi->id,
                'model' => 'Eclipse Ci-L',
                'manufacturer' => 'Nikon',
                'specifications' => [
                    'magnification' => '40x - 1000x',
                    'objectives' => '4x, 10x, 40x, 100x (oil)',
                    'eyepieces' => '10x CFI',
                    'illumination' => 'LED',
                    'condenser' => 'Abbe N.A. 1.25',
                    'stage' => 'Mechanical stage',
                ],
                'total_quantity' => 12,
                'available_quantity' => 10,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-06-15',
                'purchase_price' => 45000000.00,
                'location' => 'Lab Mikroskopi - Ruang C101',
                'image_path' => 'equipment/optical-microscope.jpg',
                'manual_file_path' => 'manuals/optical-microscope-manual.pdf',
                'notes' => 'Dilengkapi dengan kamera digital untuk dokumentasi.',
                'last_maintenance_date' => '2024-02-01',
                'next_maintenance_date' => '2024-08-01',
            ],

            // Elektronik dan Instrumentasi
            [
                'name' => 'Digital Oscilloscope',
                'category_id' => $elektronik->id,
                'model' => 'TBS1202B',
                'manufacturer' => 'Tektronix',
                'specifications' => [
                    'bandwidth' => '200 MHz',
                    'channels' => '2',
                    'sample_rate' => '2 GS/s',
                    'memory_depth' => '2.5k points',
                    'display' => '7-inch WVGA',
                    'trigger_types' => 'Edge, Pulse Width, Video',
                ],
                'total_quantity' => 6,
                'available_quantity' => 5,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-11-20',
                'purchase_price' => 18500000.00,
                'location' => 'Lab Elektronik - Ruang D101',
                'image_path' => 'equipment/digital-oscilloscope.jpg',
                'manual_file_path' => 'manuals/digital-oscilloscope-manual.pdf',
                'notes' => 'Dilengkapi dengan probe dan kabel koneksi.',
            ],
            [
                'name' => 'Function Generator',
                'category_id' => $elektronik->id,
                'model' => 'AFG1022',
                'manufacturer' => 'Tektronix',
                'specifications' => [
                    'frequency_range' => '1 μHz to 25 MHz',
                    'channels' => '2',
                    'waveforms' => 'Sine, Square, Pulse, Ramp, Noise',
                    'amplitude' => '1 mVpp to 10 Vpp',
                    'resolution' => '1 μHz',
                    'accuracy' => '±1 ppm',
                ],
                'total_quantity' => 4,
                'available_quantity' => 4,
                'status' => 'active',
                'condition_status' => 'good',
                'purchase_date' => '2023-05-10',
                'purchase_price' => 12000000.00,
                'location' => 'Lab Elektronik - Ruang D102',
                'image_path' => 'equipment/function-generator.jpg',
                'manual_file_path' => 'manuals/function-generator-manual.pdf',
            ],

            // Gelombang dan Getaran
            [
                'name' => 'Vibration Analyzer',
                'category_id' => $gelombang->id,
                'model' => 'VA-12',
                'manufacturer' => 'PASCO Scientific',
                'specifications' => [
                    'frequency_range' => '0.1 Hz to 10 kHz',
                    'amplitude_range' => '0.001 to 100 mm',
                    'acceleration_range' => '0.01 to 1000 m/s²',
                    'sensors' => 'Accelerometer, Displacement',
                    'data_acquisition' => '16-bit, 50 kS/s',
                    'software' => 'Capstone',
                ],
                'total_quantity' => 3,
                'available_quantity' => 3,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-08-05',
                'purchase_price' => 25000000.00,
                'location' => 'Lab Gelombang - Ruang E101',
                'image_path' => 'equipment/vibration-analyzer.jpg',
                'manual_file_path' => 'manuals/vibration-analyzer-manual.pdf',
                'notes' => 'Dilengkapi dengan berbagai jenis sensor getaran.',
            ],

            // Komputasi
            [
                'name' => 'Workstation Computer',
                'category_id' => $komputasi->id,
                'model' => 'Z4 G4',
                'manufacturer' => 'HP',
                'specifications' => [
                    'processor' => 'Intel Xeon W-2123',
                    'memory' => '32 GB DDR4 ECC',
                    'storage' => '512 GB SSD + 2TB HDD',
                    'graphics' => 'NVIDIA Quadro P2200',
                    'os' => 'Windows 11 Pro',
                    'software' => 'MATLAB, LabVIEW, Origin, Python',
                ],
                'total_quantity' => 8,
                'available_quantity' => 7,
                'status' => 'active',
                'condition_status' => 'excellent',
                'purchase_date' => '2023-07-12',
                'purchase_price' => 35000000.00,
                'location' => 'Lab Komputasi - Ruang F101',
                'image_path' => 'equipment/workstation-computer.jpg',
                'manual_file_path' => 'manuals/workstation-manual.pdf',
                'notes' => 'Dilengkapi dengan software analisis data lengkap.',
            ],

            // Peralatan Umum
            [
                'name' => 'Digital Caliper',
                'category_id' => $umum->id,
                'model' => 'CD-6"CSX',
                'manufacturer' => 'Mitutoyo',
                'specifications' => [
                    'measuring_range' => '0-150 mm',
                    'resolution' => '0.01 mm',
                    'accuracy' => '±0.02 mm',
                    'battery' => 'SR44',
                    'material' => 'Stainless steel',
                    'functions' => 'Zero setting, Data output',
                ],
                'total_quantity' => 15,
                'available_quantity' => 13,
                'status' => 'active',
                'condition_status' => 'good',
                'purchase_date' => '2022-11-30',
                'purchase_price' => 1250000.00,
                'location' => 'Storage - Ruang G001',
                'image_path' => 'equipment/digital-caliper.jpg',
                'manual_file_path' => 'manuals/digital-caliper-manual.pdf',
                'notes' => 'Kalibrasi dilakukan setiap tahun.',
            ],

            // Keselamatan
            [
                'name' => 'Laser Safety Glasses',
                'category_id' => $keselamatan->id,
                'model' => 'LG-011',
                'manufacturer' => 'Kentek',
                'specifications' => [
                    'wavelength_protection' => '600-700 nm',
                    'optical_density' => 'OD 4+',
                    'visible_light_transmission' => '20%',
                    'material' => 'Polycarbonate',
                    'certification' => 'CE, ANSI Z136.1',
                    'frame' => 'Adjustable, lightweight',
                ],
                'total_quantity' => 20,
                'available_quantity' => 18,
                'status' => 'active',
                'condition_status' => 'good',
                'purchase_date' => '2023-01-15',
                'purchase_price' => 750000.00,
                'location' => 'Lab Optik - Safety Cabinet',
                'image_path' => 'equipment/laser-safety-glasses.jpg',
                'manual_file_path' => 'manuals/laser-safety-glasses-manual.pdf',
                'notes' => 'Wajib digunakan saat bekerja dengan laser kelas 3 dan 4.',
            ],

            // Equipment in maintenance
            [
                'name' => 'Atomic Force Microscope',
                'category_id' => $mikroskopi->id,
                'model' => 'Dimension Icon',
                'manufacturer' => 'Bruker',
                'specifications' => [
                    'scan_size' => '90 μm x 90 μm',
                    'resolution' => 'Sub-angstrom',
                    'probe_holder' => 'ScanAsyst',
                    'software' => 'NanoScope Analysis',
                    'environment' => 'Air, liquid, vacuum',
                    'modes' => 'Contact, Tapping, PeakForce',
                ],
                'total_quantity' => 1,
                'available_quantity' => 0,
                'status' => 'maintenance',
                'condition_status' => 'fair',
                'purchase_date' => '2021-05-20',
                'purchase_price' => 850000000.00,
                'location' => 'Lab Mikroskopi - Clean Room',
                'image_path' => 'equipment/atomic-force-microscope.jpg',
                'manual_file_path' => 'manuals/afm-manual.pdf',
                'notes' => 'Sedang dalam perbaikan. Estimasi selesai: Maret 2024.',
                'last_maintenance_date' => '2024-02-01',
                'next_maintenance_date' => '2024-08-01',
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::create($item);
        }

        $this->command->info('✅ Created ' . count($equipment) . ' equipment items');
    }
}
