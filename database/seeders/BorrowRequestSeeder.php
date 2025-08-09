<?php

namespace Database\Seeders;

use App\Models\BorrowRequest;
use App\Models\BorrowRequestItem;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class BorrowRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and equipment for relationships
        $users = User::where('is_active', true)->get();
        $admins = $users->where('role', 'admin');
        $equipment = Equipment::where('status', 'active')->get();

        $borrowRequests = [
            // Approved Requests
            [
                'request_id' => 'BR20250201001',
                'status' => 'approved',
                'members' => [
                    [
                        'name' => 'Muhammad Andi Pratama',
                        'nim' => '201401001',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2020'
                    ],
                    [
                        'name' => 'Siti Aminah Hasibuan',
                        'nim' => '201401002',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2020'
                    ]
                ],
                'supervisor_name' => 'Dr. Ir. Budi Hartono, M.T',
                'supervisor_nip' => '196705051993031002',
                'supervisor_email' => 'budi.hartono@usu.ac.id',
                'supervisor_phone' => '+62 811-6234-5678',
                'purpose' => 'Penelitian skripsi tentang karakterisasi sifat optik nanopartikel perak menggunakan spektroskopi UV-Vis. Penelitian ini bertujuan untuk menganalisis pengaruh ukuran nanopartikel terhadap posisi puncak plasmon resonans permukaan.',
                'borrow_date' => now()->addDays(3),
                'return_date' => now()->addDays(10),
                'start_time' => '09:00',
                'end_time' => '16:00',
                'submitted_at' => now()->subDays(7),
                'reviewed_at' => now()->subDays(5),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Proposal penelitian sudah lengkap. Pastikan mengikuti SOP penggunaan spektrometer dan koordinasi dengan lab manager untuk jadwal penggunaan.',
            ],
            [
                'request_id' => 'BR20250128002',
                'status' => 'active',
                'members' => [
                    [
                        'name' => 'Rizky Adithya Putra',
                        'nim' => '211401045',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2021'
                    ]
                ],
                'supervisor_name' => 'Prof. Dr. Sari Murni, M.Sc',
                'supervisor_nip' => '196203101987032001',
                'supervisor_email' => 'sari.murni@usu.ac.id',
                'supervisor_phone' => '+62 812-7890-1234',
                'purpose' => 'Praktikum lanjutan mata kuliah Fisika Instrumentasi untuk eksperimen interferometri Michelson. Tujuan praktikum adalah memahami prinsip interferensi cahaya dan mengukur panjang gelombang laser He-Ne.',
                'borrow_date' => now()->subDays(2),
                'return_date' => now()->addDays(1),
                'start_time' => '13:00',
                'end_time' => '17:00',
                'submitted_at' => now()->subDays(10),
                'reviewed_at' => now()->subDays(8),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Disetujui untuk praktikum. Pastikan menggunakan kacamata pengaman laser dan ikuti prosedur keselamatan yang telah ditetapkan.',
            ],
            [
                'request_id' => 'BR20250125003',
                'status' => 'completed',
                'members' => [
                    [
                        'name' => 'Indira Sari Dewi',
                        'nim' => '191401123',
                        'program' => 'S2 Fisika',
                        'angkatan' => '2019'
                    ],
                    [
                        'name' => 'Ahmad Fauzan',
                        'nim' => '191401124',
                        'program' => 'S2 Fisika',
                        'angkatan' => '2019'
                    ]
                ],
                'supervisor_name' => 'Dr. Maya Sari, M.T',
                'supervisor_nip' => '197808152005012001',
                'supervisor_email' => 'maya.sari@usu.ac.id',
                'supervisor_phone' => '+62 815-6789-0123',
                'purpose' => 'Penelitian tesis tentang pengembangan sensor kelembaban berbasis fiber optik. Memerlukan oscilloscope untuk karakterisasi response time dan sensitivitas sensor.',
                'borrow_date' => now()->subDays(15),
                'return_date' => now()->subDays(8),
                'start_time' => '08:00',
                'end_time' => '17:00',
                'submitted_at' => now()->subDays(20),
                'reviewed_at' => now()->subDays(18),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Penelitian menarik. Koordinasi dengan teknisi untuk setup yang optimal.',
            ],

            // Pending Requests
            [
                'request_id' => 'BR20250208004',
                'status' => 'pending',
                'members' => [
                    [
                        'name' => 'Putri Rahma Sari',
                        'nim' => '221401067',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2022'
                    ],
                    [
                        'name' => 'Muhammad Fadhil',
                        'nim' => '221401068',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2022'
                    ],
                    [
                        'name' => 'Aisyah Maharani',
                        'nim' => '221401069',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2022'
                    ]
                ],
                'supervisor_name' => 'Drs. Budi Santoso, M.Si',
                'supervisor_nip' => '197504122001121001',
                'supervisor_email' => 'budi.santoso@usu.ac.id',
                'supervisor_phone' => '+62 814-5678-9012',
                'purpose' => 'Praktikum kelompok untuk mata kuliah Gelombang dan Optik. Eksperimen difraksi cahaya menggunakan single slit dan double slit untuk memahami sifat gelombang cahaya.',
                'borrow_date' => now()->addDays(7),
                'return_date' => now()->addDays(8),
                'start_time' => '10:00',
                'end_time' => '15:00',
                'submitted_at' => now()->subDays(2),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'approval_notes' => null,
            ],
            [
                'request_id' => 'BR20250209005',
                'status' => 'pending',
                'members' => [
                    [
                        'name' => 'Dedi Kurniawan',
                        'nim' => '201801089',
                        'program' => 'S3 Fisika',
                        'angkatan' => '2018'
                    ]
                ],
                'supervisor_name' => 'Prof. Dr. Andi Rahman, M.Sc',
                'supervisor_nip' => '196512101990031001',
                'supervisor_email' => 'andi.rahman@usu.ac.id',
                'supervisor_phone' => '+62 816-7890-1234',
                'purpose' => 'Penelitian disertasi tentang karakterisasi material 2D menggunakan spektroskopi Raman dan FTIR. Memerlukan akses intensif untuk preparasi sampel dan pengukuran spektroskopi dalam jangka waktu panjang.',
                'borrow_date' => now()->addDays(14),
                'return_date' => now()->addDays(28),
                'start_time' => '08:00',
                'end_time' => '18:00',
                'submitted_at' => now()->subDays(1),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'approval_notes' => null,
            ],

            // Rejected Request
            [
                'request_id' => 'BR20250205006',
                'status' => 'rejected',
                'members' => [
                    [
                        'name' => 'Rudi Hermansyah',
                        'nim' => '221401089',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2022'
                    ]
                ],
                'supervisor_name' => 'Dr. Lisa Noviana, M.Si',
                'supervisor_nip' => '198305152010012001',
                'supervisor_email' => 'lisa.noviana@usu.ac.id',
                'supervisor_phone' => '+62 823-4567-8901',
                'purpose' => 'Praktikum mandiri untuk memahami cara kerja AFM.',
                'borrow_date' => now()->addDays(1),
                'return_date' => now()->addDays(2),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'submitted_at' => now()->subDays(5),
                'reviewed_at' => now()->subDays(3),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'AFM sedang dalam maintenance dan memerlukan training khusus sebelum penggunaan. Silakan daftar training terlebih dahulu dan ajukan kembali setelah training selesai.',
            ],

            // Cancelled Request
            [
                'request_id' => 'BR20250130007',
                'status' => 'cancelled',
                'members' => [
                    [
                        'name' => 'Sari Indah Permata',
                        'nim' => '201401156',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2020'
                    ]
                ],
                'supervisor_name' => 'Dr. Rahmat Hidayat, M.Eng',
                'supervisor_nip' => '198712252015041002',
                'supervisor_email' => 'rahmat.hidayat@usu.ac.id',
                'supervisor_phone' => '+62 820-1234-5678',
                'purpose' => 'Penelitian tentang sensor optik untuk monitoring CO2.',
                'borrow_date' => now()->addDays(5),
                'return_date' => now()->addDays(12),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'submitted_at' => now()->subDays(12),
                'reviewed_at' => now()->subDays(10),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Dibatalkan atas permintaan pemohon karena perubahan jadwal penelitian.',
            ],

            // Recent Approved Request
            [
                'request_id' => 'BR20250210008',
                'status' => 'approved',
                'members' => [
                    [
                        'name' => 'Maharani Putri',
                        'nim' => '211401234',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2021'
                    ],
                    [
                        'name' => 'Bayu Firmansyah',
                        'nim' => '211401235',
                        'program' => 'S1 Fisika',
                        'angkatan' => '2021'
                    ]
                ],
                'supervisor_name' => 'Dr. Siti Nurhaliza, M.Sc',
                'supervisor_nip' => '198203152008012001',
                'supervisor_email' => 'siti.nurhaliza@usu.ac.id',
                'supervisor_phone' => '+62 813-4567-8901',
                'purpose' => 'Praktikum spektroskopi FTIR untuk analisis senyawa organik. Mahasiswa akan mempelajari teknik preparasi sampel dengan metode KBr pellet dan ATR.',
                'borrow_date' => now()->addDays(12),
                'return_date' => now()->addDays(13),
                'start_time' => '08:00',
                'end_time' => '12:00',
                'submitted_at' => now()->subHours(6),
                'reviewed_at' => now()->subHours(2),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Disetujui. Koordinasi dengan lab assistant untuk preparasi standar dan training singkat sebelum penggunaan.',
            ],
        ];

        foreach ($borrowRequests as $requestData) {
            $request = BorrowRequest::create($requestData);

            // Create borrow request items for each request
            $this->createBorrowRequestItems($request, $equipment);
        }

        $this->command->info('✅ Created ' . count($borrowRequests) . ' borrow requests with equipment items');
    }

    /**
     * Create borrow request items for a given request
     */
    private function createBorrowRequestItems(BorrowRequest $request, $equipment)
    {
        $items = [];

        // Helper function to safely get equipment ID
        $getEquipmentId = function($name) use ($equipment) {
            $eq = $equipment->where('name', $name)->first();
            if (!$eq) {
                $this->command->warn("⚠️  Equipment '{$name}' not found, using random equipment");
                return $equipment->random()->id;
            }
            return $eq->id;
        };

        switch ($request->request_id) {
            case 'BR20250201001': // UV-Vis research
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('UV-Vis Spectrophotometer'),
                        'quantity_requested' => 1,
                        'quantity_approved' => $request->status === 'approved' ? 1 : null,
                        'condition_before' => $request->status === 'active' ? 'excellent' : null,
                        'condition_after' => $request->status === 'completed' ? 'excellent' : null,
                        'notes' => 'Untuk analisis spektrum absorbansi nanopartikel perak dalam berbagai konsentrasi',
                    ]
                ];
                break;

            case 'BR20250128002': // Interferometry experiment
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('He-Ne Laser'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => 'excellent',
                        'condition_after' => null,
                        'notes' => 'Laser untuk eksperimen interferometri Michelson',
                    ],
                    [
                        'equipment_id' => $getEquipmentId('Optical Bench 1.5m'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => 'good',
                        'condition_after' => null,
                        'notes' => 'Setup optical bench untuk interferometer',
                    ]
                ];
                break;

            case 'BR20250125003': // Fiber optic sensor
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('Digital Oscilloscope'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => 'excellent',
                        'condition_after' => 'excellent',
                        'notes' => 'Untuk karakterisasi response time sensor fiber optik',
                    ],
                    [
                        'equipment_id' => $getEquipmentId('Function Generator'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => 'good',
                        'condition_after' => 'good',
                        'notes' => 'Signal generator untuk testing sensor',
                    ]
                ];
                break;

            case 'BR20250208004': // Diffraction experiment
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('He-Ne Laser'),
                        'quantity_requested' => 2,
                        'quantity_approved' => null,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Dua unit laser untuk setup paralel eksperimen difraksi',
                    ],
                    [
                        'equipment_id' => $getEquipmentId('Optical Bench 1.5m'),
                        'quantity_requested' => 2,
                        'quantity_approved' => null,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Optical bench untuk setup eksperimen difraksi single dan double slit',
                    ]
                ];
                break;

            case 'BR20250209005': // 2D materials research
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('FTIR Spectrometer'),
                        'quantity_requested' => 1,
                        'quantity_approved' => null,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Untuk karakterisasi vibrational modes material 2D',
                    ],
                    [
                        'equipment_id' => $getEquipmentId('Digital Caliper'),
                        'quantity_requested' => 2,
                        'quantity_approved' => null,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Untuk pengukuran dimensi sampel material 2D',
                    ]
                ];
                break;

            case 'BR20250205006': // AFM (rejected)
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('Atomic Force Microscope'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 0,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Request ditolak - AFM dalam maintenance',
                    ]
                ];
                break;

            case 'BR20250210008': // FTIR practical
                $items = [
                    [
                        'equipment_id' => $getEquipmentId('FTIR Spectrometer'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Praktikum FTIR dengan sampel senyawa organik standar',
                    ],
                    [
                        'equipment_id' => $getEquipmentId('Digital Caliper'),
                        'quantity_requested' => 1,
                        'quantity_approved' => 1,
                        'condition_before' => null,
                        'condition_after' => null,
                        'notes' => 'Untuk preparasi KBr pellet dengan ketebalan optimal',
                    ]
                ];
                break;

            default:
                // Default items for other requests
                $items = [
                    [
                        'equipment_id' => $equipment->random()->id,
                        'quantity_requested' => 1,
                        'quantity_approved' => in_array($request->status, ['approved', 'active', 'completed']) ? 1 : null,
                        'condition_before' => in_array($request->status, ['active', 'completed']) ? 'good' : null,
                        'condition_after' => $request->status === 'completed' ? 'good' : null,
                        'notes' => 'General equipment request',
                    ]
                ];
                break;
        }

        foreach ($items as $itemData) {
            BorrowRequestItem::create(array_merge([
                'borrow_request_id' => $request->id,
            ], $itemData));
        }
    }
}
