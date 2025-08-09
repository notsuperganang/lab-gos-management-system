<?php

namespace Database\Seeders;

use App\Models\VisitRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class VisitRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for relationships
        $users = User::where('is_active', true)->get();
        $admins = $users->where('role', 'admin');

        $visitRequests = [
            // Approved Visits
            [
                'request_id' => 'VR20250215001',
                'status' => 'approved',
                'full_name' => 'Dr. Rini Kartika Sari, M.T',
                'email' => 'rini.kartika@polmed.ac.id',
                'phone' => '+62 813-7890-1234',
                'institution' => 'Politeknik Negeri Medan',
                'purpose' => 'study-visit',
                'visit_date' => now()->addDays(10),
                'visit_time' => 'morning',
                'participants' => 25,
                'additional_notes' => 'Kunjungan studi untuk mahasiswa D3 Teknik Instrumentasi semester 6. Ingin melihat langsung aplikasi spektroskopi dalam industri dan penelitian. Mohon presentasi singkat tentang career path di bidang instrumentasi.',
                'request_letter_path' => 'visit-requests/VR20250215001/surat-permohonan-polmed.pdf',
                'approval_letter_path' => 'visit-requests/VR20250215001/surat-persetujuan-kunjungan.pdf',
                'submitted_at' => now()->subDays(20),
                'reviewed_at' => now()->subDays(15),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Disetujui. Tim akan menyiapkan presentasi tentang career path dan demo peralatan spektroskopi. Koordinasi dengan Dr. Siti Nurhaliza untuk technical presentation.',
                'agreement_accepted' => true,
            ],
            [
                'request_id' => 'VR20250212002',
                'status' => 'completed',
                'full_name' => 'Prof. Dr. Sutrisno Adiwibowo',
                'email' => 'sutrisno.adiwibowo@itb.ac.id',
                'phone' => '+62 812-3456-7890',
                'institution' => 'Institut Teknologi Bandung',
                'purpose' => 'research',
                'visit_date' => now()->subDays(5),
                'visit_time' => 'afternoon',
                'participants' => 3,
                'additional_notes' => 'Kunjungan untuk diskusi kolaborasi penelitian di bidang material characterization menggunakan advanced spectroscopy. Tim terdiri dari 2 dosen dan 1 mahasiswa doktoral.',
                'request_letter_path' => 'visit-requests/VR20250212002/surat-permohonan-itb.pdf',
                'approval_letter_path' => 'visit-requests/VR20250212002/surat-persetujuan-kunjungan.pdf',
                'submitted_at' => now()->subDays(25),
                'reviewed_at' => now()->subDays(20),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Sambutan sangat baik untuk kolaborasi. Meeting dengan Prof. Andi Rahman dan Dr. Ahmad Fadli untuk membahas potential joint research.',
                'agreement_accepted' => true,
            ],
            [
                'request_id' => 'VR20250208003',
                'status' => 'approved',
                'full_name' => 'Ir. Bambang Suryadi, M.M',
                'email' => 'bambang.suryadi@pupuk-indonesia.com',
                'phone' => '+62 814-5678-9012',
                'institution' => 'PT Pupuk Indonesia',
                'purpose' => 'learning',
                'visit_date' => now()->addDays(5),
                'visit_time' => 'morning',
                'participants' => 8,
                'additional_notes' => 'Tim QC dari PT Pupuk Indonesia ingin belajar tentang aplikasi spektroskopi FTIR untuk quality control produk pupuk. Khususnya untuk analisis komposisi dan deteksi impurities.',
                'request_letter_path' => 'visit-requests/VR20250208003/surat-permohonan-pupuk-indonesia.pdf',
                'approval_letter_path' => 'visit-requests/VR20250208003/surat-persetujuan-kunjungan.pdf',
                'submitted_at' => now()->subDays(15),
                'reviewed_at' => now()->subDays(12),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Disetujui. Dr. Siti Nurhaliza akan memberikan presentasi khusus tentang aplikasi FTIR untuk QC industri kimia. Demo dengan sampel fertilizer.',
                'agreement_accepted' => true,
            ],

            // Pending Visits
            [
                'request_id' => 'VR20250213004',
                'status' => 'pending',
                'full_name' => 'Drs. Ahmad Zulkifli, M.Pd',
                'email' => 'ahmad.zulkifli@sman1medan.sch.id',
                'phone' => '+62 815-2345-6789',
                'institution' => 'SMA Negeri 1 Medan',
                'purpose' => 'learning',
                'visit_date' => now()->addDays(20),
                'visit_time' => 'morning',
                'participants' => 40,
                'additional_notes' => 'Kunjungan siswa kelas XII IPA yang berminat melanjutkan studi di bidang fisika. Ingin mengenal lebih dekat tentang penelitian fisika modern dan peluang karir di bidang sains.',
                'request_letter_path' => 'visit-requests/VR20250213004/surat-permohonan-sman1medan.pdf',
                'approval_letter_path' => null,
                'submitted_at' => now()->subDays(3),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'approval_notes' => null,
                'agreement_accepted' => true,
            ],
            [
                'request_id' => 'VR20250214005',
                'status' => 'pending',
                'full_name' => 'Dr. Maria Ulfa Siregar, M.Si',
                'email' => 'maria.siregar@unimed.ac.id',
                'phone' => '+62 816-3456-7890',
                'institution' => 'Universitas Negeri Medan',
                'purpose' => 'research',
                'visit_date' => now()->addDays(25),
                'visit_time' => 'afternoon',
                'participants' => 6,
                'additional_notes' => 'Tim peneliti dari Jurusan Kimia UNIMED ingin melakukan benchmarking fasilitas laboratorium dan diskusi kemungkinan sharing equipment untuk research collaboration.',
                'request_letter_path' => 'visit-requests/VR20250214005/surat-permohonan-unimed.pdf',
                'approval_letter_path' => null,
                'submitted_at' => now()->subDays(2),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'approval_notes' => null,
                'agreement_accepted' => true,
            ],

            // International Visit - Approved
            [
                'request_id' => 'VR20250210006',
                'status' => 'approved',
                'full_name' => 'Prof. Dr. Takeshi Yamamoto',
                'email' => 'takeshi.yamamoto@riken.jp',
                'phone' => '+81-3-1234-5678',
                'institution' => 'RIKEN (Japan)',
                'purpose' => 'research',
                'visit_date' => now()->addDays(30),
                'visit_time' => 'morning',
                'participants' => 4,
                'additional_notes' => 'Visit from RIKEN research team to discuss potential collaboration in 2D materials characterization. Planning to explore joint research proposal for JSPS-LIPI program.',
                'request_letter_path' => 'visit-requests/VR20250210006/invitation-letter-riken.pdf',
                'approval_letter_path' => 'visit-requests/VR20250210006/approval-letter-international.pdf',
                'submitted_at' => now()->subDays(30),
                'reviewed_at' => now()->subDays(25),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'International collaboration opportunity. Prof. Andi Rahman dan Dr. Ahmad Fadli akan menjadi host. Persiapkan presentasi tentang research capabilities Lab GOS.',
                'agreement_accepted' => true,
            ],

            // Rejected Visit
            [
                'request_id' => 'VR20250205007',
                'status' => 'rejected',
                'full_name' => 'Budi Hartono',
                'email' => 'budi.hartono.private@gmail.com',
                'phone' => '+62 817-8901-2345',
                'institution' => 'Individual/Private',
                'purpose' => 'others',
                'visit_date' => now()->addDays(3),
                'visit_time' => 'afternoon',
                'participants' => 1,
                'additional_notes' => 'Saya ingin melihat-lihat fasilitas laboratorium karena tertarik dengan dunia penelitian. Bisa saja nanti bergabung sebagai peneliti.',
                'request_letter_path' => null,
                'approval_letter_path' => null,
                'submitted_at' => now()->subDays(8),
                'reviewed_at' => now()->subDays(6),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Mohon maaf, kunjungan harus melalui institusi resmi dengan tujuan yang jelas (akademik/penelitian). Silakan mengajukan kembali dengan surat pengantar dari institusi.',
                'agreement_accepted' => false,
            ],

            // Internship Visit - Approved
            [
                'request_id' => 'VR20250220008',
                'status' => 'approved',
                'full_name' => 'Sinta Maharani',
                'email' => 'sinta.maharani@student.uns.ac.id',
                'phone' => '+62 818-9012-3456',
                'institution' => 'Universitas Sebelas Maret',
                'purpose' => 'internship',
                'visit_date' => now()->addDays(45),
                'visit_time' => 'morning',
                'participants' => 1,
                'additional_notes' => 'Mahasiswa S1 Fisika semester 6 mengajukan permohonan magang selama 1 bulan untuk menyelesaikan mata kuliah Kerja Praktik. Tertarik dengan bidang spektroskopi dan instrumentasi.',
                'request_letter_path' => 'visit-requests/VR20250220008/surat-permohonan-magang-uns.pdf',
                'approval_letter_path' => 'visit-requests/VR20250220008/surat-penerimaan-magang.pdf',
                'submitted_at' => now()->subDays(10),
                'reviewed_at' => now()->subDays(7),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Diterima untuk magang periode Maret-April 2024. Akan ditempatkan di divisi spektroskopi under supervision Dr. Siti Nurhaliza. Mahasiswa harus submit plan kerja praktik sebelum mulai.',
                'agreement_accepted' => true,
            ],

            // Recent Study Visit - Pending
            [
                'request_id' => 'VR20250211009',
                'status' => 'pending',
                'full_name' => 'Dr. Eko Prasetyo, M.T',
                'email' => 'eko.prasetyo@its.ac.id',
                'phone' => '+62 819-0123-4567',
                'institution' => 'Institut Teknologi Sepuluh Nopember',
                'purpose' => 'study-visit',
                'visit_date' => now()->addDays(18),
                'visit_time' => 'afternoon',
                'participants' => 15,
                'additional_notes' => 'Kunjungan dosen dan mahasiswa pascasarjana Teknik Fisika ITS untuk studi komparasi setup laboratorium spektroskopi. Berencana untuk upgrade fasilitas laboratorium di ITS.',
                'request_letter_path' => 'visit-requests/VR20250211009/surat-permohonan-its.pdf',
                'approval_letter_path' => null,
                'submitted_at' => now()->subDays(4),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'approval_notes' => null,
                'agreement_accepted' => true,
            ],

            // Cancelled Visit
            [
                'request_id' => 'VR20250209010',
                'status' => 'cancelled',
                'full_name' => 'Ir. Dewi Sartika, M.T',
                'email' => 'dewi.sartika@polsu.ac.id',
                'phone' => '+62 820-1234-5678',
                'institution' => 'Politeknik Negeri Sumatera Utara',
                'purpose' => 'study-visit',
                'visit_date' => now()->addDays(8),
                'visit_time' => 'morning',
                'participants' => 30,
                'additional_notes' => 'Kunjungan mahasiswa D4 Teknologi Rekayasa Instrumentasi dan Kontrol.',
                'request_letter_path' => 'visit-requests/VR20250209010/surat-permohonan-polsu.pdf',
                'approval_letter_path' => null,
                'submitted_at' => now()->subDays(18),
                'reviewed_at' => now()->subDays(15),
                'reviewed_by' => $admins->random()->id,
                'approval_notes' => 'Dibatalkan karena bentrok dengan jadwal maintenance peralatan utama. Silakan reschedule untuk minggu berikutnya.',
                'agreement_accepted' => true,
            ],
        ];

        foreach ($visitRequests as $request) {
            VisitRequest::create($request);
        }

        $this->command->info('✅ Created ' . count($visitRequests) . ' visit requests with various statuses');
    }
}
