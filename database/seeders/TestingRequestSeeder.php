<?php

namespace Database\Seeders;

use App\Models\TestingRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for relationships
        $users = User::where('is_active', true)->get();
        $admins = $users->where('role', 'admin');

        $testingRequests = [
            // Completed Testing Requests
            [
                'request_id' => 'TR20250201001',
                'status' => 'completed',
                'client_name' => 'Dr. Andi Kurniawan',
                'client_organization' => 'PT Kimia Farma',
                'client_email' => 'andi.kurniawan@kimiafarma.co.id',
                'client_phone' => '+62 811-2345-6789',
                'client_address' => 'Jl. Veteran No. 9, Jakarta Pusat 10110',
                'sample_name' => 'Paracetamol Tablet Batch KF240125',
                'sample_description' => 'Tablet paracetamol 500mg untuk quality control analysis. Sampel dari production batch yang akan dirilis ke market. Perlu konfirmasi komposisi dan identifikasi impurities.',
                'sample_quantity' => '10 tablets (approximately 5 grams)',
                'testing_type' => 'ftir_spectroscopy',
                'testing_parameters' => [
                    'method' => 'KBr pellet and ATR',
                    'wavenumber_range' => '4000-400 cm⁻¹',
                    'resolution' => '4 cm⁻¹',
                    'scans' => 32,
                    'reference_standard' => 'USP Paracetamol RS',
                    'acceptance_criteria' => 'Match with reference spectrum (correlation > 0.95)'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->subDays(25),
                'estimated_duration_hours' => 4,
                'actual_start_date' => now()->subDays(23),
                'actual_completion_date' => now()->subDays(22),
                'result_files_path' => [
                    'spectrum_file' => 'testing-results/TR20250201001/paracetamol_ftir_spectrum.sp',
                    'analysis_report' => 'testing-results/TR20250201001/analysis_report.pdf',
                    'comparison_chart' => 'testing-results/TR20250201001/reference_comparison.pdf'
                ],
                'result_summary' => 'Sampel tablet paracetamol menunjukkan spektrum FTIR yang sesuai dengan standar USP (korelasi 0.987). Puncak karakteristik pada 1651 cm⁻¹ (C=O stretch), 1562 cm⁻¹ (N-H bend), dan 837 cm⁻¹ (aromatic C-H) terdeteksi dengan intensitas normal. Tidak terdeteksi impurities signifikan. Sampel memenuhi spesifikasi quality control.',
                'cost_estimate' => 2500000.00,
                'final_cost' => 2500000.00,
                'submitted_at' => now()->subDays(28),
                'reviewed_at' => now()->subDays(26),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Request untuk QC pharmaceutical diterima. Tim akan menggunakan both KBr and ATR methods untuk comprehensive analysis.',
            ],
            [
                'request_id' => 'TR20250128002',
                'status' => 'completed',
                'client_name' => 'Prof. Dr. Sari Mutia',
                'client_organization' => 'Universitas Gadjah Mada',
                'client_email' => 'sari.mutia@ugm.ac.id',
                'client_phone' => '+62 812-3456-7890',
                'client_address' => 'Sekip Utara, Yogyakarta 55281',
                'sample_name' => 'Gold Nanoparticles - Various Sizes',
                'sample_description' => 'Nanopartikel emas yang disintesis dengan berbagai ukuran (10nm, 20nm, 50nm) untuk penelitian plasmon resonance. Sampel dalam bentuk koloid dengan stabilizer citrate.',
                'sample_quantity' => '3 vials × 10 mL each',
                'testing_type' => 'uv_vis_spectroscopy',
                'testing_parameters' => [
                    'wavelength_range' => '300-800 nm',
                    'resolution' => '1 nm',
                    'scan_speed' => 'medium',
                    'baseline_correction' => 'automatic',
                    'dilution_factor' => '1:10 with deionized water',
                    'reference' => 'deionized water'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->subDays(18),
                'estimated_duration_hours' => 3,
                'actual_start_date' => now()->subDays(16),
                'actual_completion_date' => now()->subDays(16),
                'result_files_path' => [
                    'spectrum_file' => 'testing-results/TR20250128002/aunp_uv_vis_spectra.csv',
                    'analysis_report' => 'testing-results/TR20250128002/plasmon_analysis_report.pdf',
                    'size_correlation' => 'testing-results/TR20250128002/size_plasmon_correlation.xlsx'
                ],
                'result_summary' => 'Analisis UV-Vis menunjukkan puncak plasmon resonans pada 520nm (10nm AuNPs), 525nm (20nm AuNPs), dan 535nm (50nm AuNPs). Red-shift sesuai dengan teori Mie untuk peningkatan ukuran partikel. Intensitas absorbansi menunjukkan konsentrasi yang konsisten. Tidak ada agregasi signifikan berdasarkan broadening puncak.',
                'cost_estimate' => 1500000.00,
                'final_cost' => 1500000.00,
                'submitted_at' => now()->subDays(22),
                'reviewed_at' => now()->subDays(20),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Penelitian akademik - fee special rate. Interesting samples untuk plasmon study.',
            ],

            // In Progress Testing
            [
                'request_id' => 'TR20250210003',
                'status' => 'in_progress',
                'client_name' => 'Ir. Bambang Wijaya, M.T',
                'client_organization' => 'PT Semen Padang',
                'client_email' => 'bambang.wijaya@semenpadang.co.id',
                'client_phone' => '+62 813-4567-8901',
                'client_address' => 'Indarung, Padang, Sumatera Barat 25237',
                'sample_name' => 'Portland Cement - Type I',
                'sample_description' => 'Sampel semen portland tipe I dari production line baru untuk karakterisasi komposisi mineral dan quality assurance. Perlu analisis untuk sertifikasi SNI.',
                'sample_quantity' => '500 grams powder',
                'testing_type' => 'ftir_spectroscopy',
                'testing_parameters' => [
                    'method' => 'KBr pellet technique',
                    'wavenumber_range' => '4000-400 cm⁻¹',
                    'resolution' => '2 cm⁻¹',
                    'scans' => 64,
                    'sample_preparation' => 'Grinding with KBr (1:100 ratio)',
                    'reference_standards' => 'NIST cement standards'
                ],
                'urgent_request' => true,
                'preferred_date' => now()->subDays(2),
                'estimated_duration_hours' => 6,
                'actual_start_date' => now()->subDays(1),
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 3500000.00,
                'final_cost' => null,
                'submitted_at' => now()->subDays(5),
                'reviewed_at' => now()->subDays(4),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Urgent request untuk sertifikasi produk. Priority processing. Dr. Siti Nurhaliza assigned sebagai analyst.',
            ],
            [
                'request_id' => 'TR20250209004',
                'status' => 'in_progress',
                'client_name' => 'Dr. Lisa Handayani',
                'client_organization' => 'Badan Litbang Kementerian Kesehatan',
                'client_email' => 'lisa.handayani@litbang.kemkes.go.id',
                'client_phone' => '+62 814-5678-9012',
                'client_address' => 'Jl. Percetakan Negara No. 23, Jakarta Pusat 10560',
                'sample_name' => 'Herbal Extract - Sambiloto',
                'sample_description' => 'Ekstrak sambiloto (Andrographis paniculata) untuk standardisasi sebagai bahan baku obat herbal tradisional. Perlu identifikasi senyawa aktif andrographolide.',
                'sample_quantity' => '2 grams dried extract',
                'testing_type' => 'uv_vis_spectroscopy',
                'testing_parameters' => [
                    'wavelength_range' => '200-400 nm',
                    'resolution' => '0.5 nm',
                    'solvent' => 'methanol HPLC grade',
                    'concentration' => '0.1 mg/mL',
                    'reference_standard' => 'andrographolide pure compound',
                    'quantitative_analysis' => true
                ],
                'urgent_request' => false,
                'preferred_date' => now()->subDays(1),
                'estimated_duration_hours' => 4,
                'actual_start_date' => now(),
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 2000000.00,
                'final_cost' => null,
                'submitted_at' => now()->subDays(8),
                'reviewed_at' => now()->subDays(6),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Government research project. Akan dilakukan quantitative analysis untuk standardisasi. Expected andrographolide content 5-15%.',
            ],

            // Approved Testing (Waiting to Start)
            [
                'request_id' => 'TR20250212005',
                'status' => 'approved',
                'client_name' => 'Dr. Maya Permata Sari',
                'client_organization' => 'Universitas Syiah Kuala',
                'client_email' => 'maya.permata@unsyiah.ac.id',
                'client_phone' => '+62 815-6789-0123',
                'client_address' => 'Jl. Syiah Kuala No. 1, Banda Aceh 23111',
                'sample_name' => 'Chitosan Nanofiber Membrane',
                'sample_description' => 'Membran nanofiber chitosan yang dimodifikasi dengan nanopartikel silver untuk aplikasi water filtration. Perlu karakterisasi untuk konfirmasi successful incorporation silver nanoparticles.',
                'sample_quantity' => '5 pieces (2×2 cm each)',
                'testing_type' => 'ftir_spectroscopy',
                'testing_parameters' => [
                    'method' => 'ATR-FTIR',
                    'wavenumber_range' => '4000-600 cm⁻¹',
                    'resolution' => '2 cm⁻¹',
                    'scans' => 32,
                    'reference_sample' => 'pure chitosan membrane',
                    'focus_analysis' => 'Ag-O bonding detection'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->addDays(3),
                'estimated_duration_hours' => 3,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 1800000.00,
                'final_cost' => null,
                'submitted_at' => now()->subDays(4),
                'reviewed_at' => now()->subDays(2),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Menarik untuk nanocomposite research. ATR method akan optimal untuk thin membrane samples.',
            ],

            // Pending Testing Requests
            [
                'request_id' => 'TR20250213006',
                'status' => 'pending',
                'client_name' => 'Budi Santoso, S.T',
                'client_organization' => 'PT Charoen Pokphand Indonesia',
                'client_email' => 'budi.santoso@charoen.co.id',
                'client_phone' => '+62 816-7890-1234',
                'client_address' => 'Jl. Ancol VIII No. 1, Jakarta Utara 14430',
                'sample_name' => 'Fish Feed Pellets - Premium Grade',
                'sample_description' => 'Pelet pakan ikan premium grade untuk aquaculture. Perlu analisis komposisi protein, lemak, dan mineral content untuk quality control dan labeling compliance.',
                'sample_quantity' => '1 kg pellets',
                'testing_type' => 'ftir_spectroscopy',
                'testing_parameters' => [
                    'method' => 'Grinding + KBr pellet',
                    'wavenumber_range' => '4000-400 cm⁻¹',
                    'resolution' => '4 cm⁻¹',
                    'scans' => 32,
                    'analysis_focus' => 'Protein, lipid, carbohydrate bands',
                    'reference_standards' => 'Standard fish meal, soy protein'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->addDays(7),
                'estimated_duration_hours' => 5,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 2800000.00,
                'final_cost' => null,
                'submitted_at' => now()->subDays(1),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'assigned_to' => null,
                'approval_notes' => null,
            ],
            [
                'request_id' => 'TR20250214007',
                'status' => 'pending',
                'client_name' => 'Prof. Dr. Rina Marliana',
                'client_organization' => 'Universitas Andalas',
                'client_email' => 'rina.marliana@unand.ac.id',
                'client_phone' => '+62 817-8901-2345',
                'client_address' => 'Limau Manis, Padang, Sumatera Barat 25163',
                'sample_name' => 'Graphene Oxide Composite',
                'sample_description' => 'Komposit graphene oxide yang didoping dengan nitrogen untuk aplikasi supercapacitor. Perlu konfirmasi successful doping dan karakterisasi optical properties.',
                'sample_quantity' => '100 mg powder',
                'testing_type' => 'uv_vis_spectroscopy',
                'testing_parameters' => [
                    'wavelength_range' => '200-800 nm',
                    'resolution' => '1 nm',
                    'solvent' => 'DMF (dimethylformamide)',
                    'concentration_range' => '0.01-0.1 mg/mL',
                    'baseline_correction' => 'solvent blank',
                    'temperature' => 'room temperature'
                ],
                'urgent_request' => true,
                'preferred_date' => now()->addDays(2),
                'estimated_duration_hours' => 4,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 2200000.00,
                'final_cost' => null,
                'submitted_at' => now()->subHours(8),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'assigned_to' => null,
                'approval_notes' => null,
            ],

            // Rejected Testing Request
            [
                'request_id' => 'TR20250208008',
                'status' => 'rejected',
                'client_name' => 'Ahmad Rizki',
                'client_organization' => 'PT Berkah Jaya',
                'client_email' => 'ahmad.rizki@berkahjaya.co.id',
                'client_phone' => '+62 818-9012-3456',
                'client_address' => 'Jl. Sudirman No. 123, Medan 20122',
                'sample_name' => 'Unknown White Powder',
                'sample_description' => 'Bubuk putih yang ditemukan di warehouse perusahaan. Perlu identifikasi untuk keperluan investigasi internal.',
                'sample_quantity' => '50 grams',
                'testing_type' => 'ftir_spectroscopy',
                'testing_parameters' => [
                    'method' => 'ATR-FTIR',
                    'wavenumber_range' => '4000-400 cm⁻¹',
                    'safety_protocol' => 'unknown material handling'
                ],
                'urgent_request' => true,
                'preferred_date' => now()->addDays(1),
                'estimated_duration_hours' => 2,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => null,
                'final_cost' => null,
                'submitted_at' => now()->subDays(6),
                'reviewed_at' => now()->subDays(4),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => null,
                'approval_notes' => 'Request ditolak. Lab GOS tidak melayani analisis untuk keperluan investigasi/forensik atau material dengan latar belakang tidak jelas. Silakan hubungi laboratorium forensik yang certified.',
            ],

            // Custom Testing - Approved
            [
                'request_id' => 'TR20250211009',
                'status' => 'approved',
                'client_name' => 'Dr. Ir. Sulastri, M.T',
                'client_organization' => 'Pusat Penelitian Kimia LIPI',
                'client_email' => 'sulastri.kimia@lipi.go.id',
                'client_phone' => '+62 819-0123-4567',
                'client_address' => 'Kawasan Puspiptek, Serpong, Tangerang Selatan 15314',
                'sample_name' => 'Biodegradable Plastic from Cassava Starch',
                'sample_description' => 'Plastik biodegradable dari pati singkong dengan penambahan plasticizer glycerol. Perlu karakterisasi untuk publikasi di journal internasional.',
                'sample_quantity' => '20 film samples (5×5 cm)',
                'testing_type' => 'custom',
                'testing_parameters' => [
                    'custom_method' => 'Combined FTIR + UV-Vis analysis',
                    'ftir_parameters' => [
                        'method' => 'ATR',
                        'range' => '4000-600 cm⁻¹',
                        'resolution' => '2 cm⁻¹'
                    ],
                    'uv_vis_parameters' => [
                        'transmission_mode' => true,
                        'range' => '300-800 nm',
                        'reference' => 'air'
                    ],
                    'analysis_focus' => 'Polymer structure + optical transparency'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->addDays(10),
                'estimated_duration_hours' => 8,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 4500000.00,
                'final_cost' => null,
                'submitted_at' => now()->subDays(3),
                'reviewed_at' => now()->subDays(1),
                'reviewed_by' => $admins->random()->id,
                'assigned_to' => $admins->random()->id,
                'approval_notes' => 'Interesting research topic. Custom analysis package approved. Team akan provide comprehensive characterization untuk journal publication.',
            ],

            // Recent Submission - Pending
            [
                'request_id' => 'TR20250215010',
                'status' => 'pending',
                'client_name' => 'Dr. Khairul Anwar',
                'client_organization' => 'Universitas Brawijaya',
                'client_email' => 'khairul.anwar@ub.ac.id',
                'client_phone' => '+62 820-1234-5678',
                'client_address' => 'Jl. Veteran, Malang, Jawa Timur 65145',
                'sample_name' => 'TiO2 Photocatalyst - Doped with Cu',
                'sample_description' => 'Fotokatalis TiO2 yang didoping dengan copper untuk aplikasi degradasi pollutant organik. Perlu karakterisasi untuk memastikan successful doping dan band gap analysis.',
                'sample_quantity' => '200 mg powder',
                'testing_type' => 'uv_vis_spectroscopy',
                'testing_parameters' => [
                    'measurement_mode' => 'diffuse reflectance',
                    'wavelength_range' => '250-800 nm',
                    'resolution' => '1 nm',
                    'reference_standard' => 'BaSO4',
                    'analysis_type' => 'Kubelka-Munk transformation for band gap',
                    'sample_preparation' => 'dilution with BaSO4 (1:10)'
                ],
                'urgent_request' => false,
                'preferred_date' => now()->addDays(12),
                'estimated_duration_hours' => 4,
                'actual_start_date' => null,
                'actual_completion_date' => null,
                'result_files_path' => null,
                'result_summary' => null,
                'cost_estimate' => 2000000.00,
                'final_cost' => null,
                'submitted_at' => now()->subHours(4),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'assigned_to' => null,
                'approval_notes' => null,
            ],
        ];

        foreach ($testingRequests as $request) {
            TestingRequest::create($request);
        }

        $this->command->info('✅ Created ' . count($testingRequests) . ' testing requests with various statuses and types');
    }
}
