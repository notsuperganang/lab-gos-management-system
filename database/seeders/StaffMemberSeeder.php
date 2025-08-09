<?php

namespace Database\Seeders;

use App\Models\StaffMember;
use Illuminate\Database\Seeder;

class StaffMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Dr. Ahmad Fadli, M.Si',
                'position' => 'Kepala Laboratorium',
                'specialization' => 'Spektroskopi dan Karakterisasi Material',
                'education' => 'S3 Fisika - Universitas Indonesia (2015), S2 Fisika - ITB (2010), S1 Fisika - USU (2008)',
                'email' => 'ahmad.fadli@usu.ac.id',
                'phone' => '+62 812-3456-7890',
                'photo_path' => 'staff/ahmad-fadli.jpg',
                'bio' => 'Dr. Ahmad Fadli adalah Kepala Laboratorium GOS dengan pengalaman lebih dari 10 tahun dalam bidang spektroskopi dan karakterisasi material. Beliau telah menerbitkan lebih dari 50 paper di jurnal internasional dan menjadi reviewer untuk beberapa jurnal bergengsi di bidang material science.',
                'research_interests' => 'Spektroskopi FTIR, Karakterisasi material nanokomposit, Analisis struktur molekul, Pengembangan sensor optik',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Siti Nurhaliza, M.Sc',
                'position' => 'Lab Manager - Spektroskopi',
                'specialization' => 'Spektroskopi UV-Vis dan FTIR',
                'education' => 'S3 Chemistry - University of Melbourne (2018), S2 Chemistry - Universitas Gadjah Mada (2012), S1 Kimia - USU (2010)',
                'email' => 'siti.nurhaliza@usu.ac.id',
                'phone' => '+62 813-4567-8901',
                'photo_path' => 'staff/siti-nurhaliza.jpg',
                'bio' => 'Dr. Siti Nurhaliza adalah ahli spektroskopi dengan keahlian khusus dalam analisis kualitatif dan kuantitatif menggunakan UV-Vis dan FTIR. Beliau bertanggung jawab atas operasional dan maintenance peralatan spektroskopi di laboratorium.',
                'research_interests' => 'Analisis senyawa organik, Spektroskopi molekular, Quality control dalam industri, Environmental analysis',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Drs. Budi Santoso, M.Si',
                'position' => 'Lab Manager - Optik',
                'specialization' => 'Optik dan Fotonikal',
                'education' => 'S2 Fisika - ITB (2014), S1 Fisika - USU (2010)',
                'email' => 'budi.santoso@usu.ac.id',
                'phone' => '+62 814-5678-9012',
                'photo_path' => 'staff/budi-santoso.jpg',
                'bio' => 'Drs. Budi Santoso mengelola laboratorium optik dan bertanggung jawab atas keselamatan penggunaan laser. Dengan pengalaman lebih dari 8 tahun, beliau ahli dalam sistem optik dan aplikasi laser untuk berbagai keperluan penelitian.',
                'research_interests' => 'Laser applications, Optical fiber technology, Holography, Interferometry',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Maya Sari, M.T',
                'position' => 'Lab Manager - Elektronik',
                'specialization' => 'Instrumentasi Elektronik',
                'education' => 'S3 Teknik Elektro - ITB (2019), S2 Teknik Elektro - USU (2013), S1 Teknik Elektro - USU (2011)',
                'email' => 'maya.sari@usu.ac.id',
                'phone' => '+62 815-6789-0123',
                'photo_path' => 'staff/maya-sari.jpg',
                'bio' => 'Dr. Maya Sari adalah spesialis dalam bidang instrumentasi elektronik dan sistem akuisisi data. Beliau mengelola peralatan elektronik laboratorium dan memberikan konsultasi dalam pengembangan sistem instrumentasi custom.',
                'research_interests' => 'Electronic instrumentation, Data acquisition systems, Sensor technology, Embedded systems for laboratory automation',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Dr. Andi Rahman, M.Sc',
                'position' => 'Senior Research Associate',
                'specialization' => 'Fisika Material dan Getaran',
                'education' => 'S3 Physics - University of Cambridge (2005), S2 Physics - University of Edinburgh (2000), S1 Fisika - ITB (1998)',
                'email' => 'andi.rahman@usu.ac.id',
                'phone' => '+62 816-7890-1234',
                'photo_path' => 'staff/andi-rahman.jpg',
                'bio' => 'Prof. Dr. Andi Rahman adalah peneliti senior dengan lebih dari 20 tahun pengalaman di bidang fisika material dan analisis getaran. Beliau adalah profesor tamu dari beberapa universitas internasional dan telah memimpin lebih dari 30 proyek penelitian internasional.',
                'research_interests' => 'Materials physics, Vibration analysis, Nondestructive testing, Advanced characterization techniques',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Ir. Dewi Kusuma, M.T',
                'position' => 'Technical Manager',
                'specialization' => 'Maintenance dan Kalibrasi Peralatan',
                'education' => 'S2 Teknik Fisika - ITB (2016), S1 Teknik Fisika - ITB (2012)',
                'email' => 'dewi.kusuma@usu.ac.id',
                'phone' => '+62 817-8901-2345',
                'photo_path' => 'staff/dewi-kusuma.jpg',
                'bio' => 'Ir. Dewi Kusuma bertanggung jawab atas maintenance, kalibrasi, dan quality assurance semua peralatan laboratorium. Beliau memiliki sertifikasi internasional dalam maintenance peralatan analitik dan telah mengikuti training di berbagai vendor peralatan.',
                'research_interests' => 'Instrument maintenance, Calibration procedures, Quality control systems, Laboratory accreditation',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Muhammad Rizki, S.Si',
                'position' => 'Asisten Laboratorium Senior',
                'specialization' => 'Operasional Peralatan Spektroskopi',
                'education' => 'S1 Fisika - USU (2020)',
                'email' => 'muhammad.rizki@usu.ac.id',
                'phone' => '+62 818-9012-3456',
                'photo_path' => 'staff/muhammad-rizki.jpg',
                'bio' => 'Muhammad Rizki adalah asisten laboratorium senior yang membantu dalam operasional harian laboratorium spektroskopi. Beliau juga bertanggung jawab dalam memberikan training penggunaan peralatan kepada mahasiswa dan peneliti baru.',
                'research_interests' => 'Sample preparation techniques, Spectroscopic analysis methods, Laboratory training and education',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Sari Indah Lestari, S.Si',
                'position' => 'Asisten Laboratorium',
                'specialization' => 'Mikroskopi dan Imaging',
                'education' => 'S1 Fisika - USU (2021)',
                'email' => 'sari.lestari@usu.ac.id',
                'phone' => '+62 819-0123-4567',
                'photo_path' => 'staff/sari-lestari.jpg',
                'bio' => 'Sari Indah Lestari adalah asisten laboratorium yang mengelola peralatan mikroskopi dan sistem imaging. Beliau membantu mahasiswa dalam dokumentasi dan analisis hasil pengamatan mikroskopik.',
                'research_interests' => 'Microscopy techniques, Image analysis, Sample preparation for microscopy',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Rahmat Hidayat, M.Eng',
                'position' => 'Research Scientist',
                'specialization' => 'Pengembangan Sensor Optik',
                'education' => 'S3 Engineering Physics - Tokyo Institute of Technology (2020), S2 Engineering Physics - Tokyo Institute of Technology (2017), S1 Teknik Fisika - ITB (2015)',
                'email' => 'rahmat.hidayat@usu.ac.id',
                'phone' => '+62 820-1234-5678',
                'photo_path' => 'staff/rahmat-hidayat.jpg',
                'bio' => 'Dr. Rahmat Hidayat adalah peneliti muda yang fokus pada pengembangan sensor optik dan aplikasinya dalam monitoring lingkungan. Beliau baru bergabung dengan tim Lab GOS pada tahun 2023 setelah menyelesaikan program doktoral di Jepang.',
                'research_interests' => 'Optical sensors, Environmental monitoring, Fiber optic sensors, Biosensors',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Putri Amelia, S.T',
                'position' => 'IT Support Specialist',
                'specialization' => 'Sistem Informasi dan Database',
                'education' => 'S1 Teknik Informatika - USU (2022)',
                'email' => 'putri.amelia@usu.ac.id',
                'phone' => '+62 821-2345-6789',
                'photo_path' => 'staff/putri-amelia.jpg',
                'bio' => 'Putri Amelia bertanggung jawab atas sistem informasi laboratorium, maintenance database, dan support teknis untuk software analisis. Beliau juga mengelola website dan sistem reservasi online laboratorium.',
                'research_interests' => 'Laboratory information systems, Database management, Software integration, Web development',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Syahrul, A.Md',
                'position' => 'Teknisi Laboratorium',
                'specialization' => 'Maintenance Peralatan Mekanik',
                'education' => 'D3 Teknik Mesin - Politeknik Negeri Medan (2018)',
                'email' => 'ahmad.syahrul@usu.ac.id',
                'phone' => '+62 822-3456-7890',
                'photo_path' => 'staff/ahmad-syahrul.jpg',
                'bio' => 'Ahmad Syahrul adalah teknisi yang bertanggung jawab atas maintenance aspek mekanik dari peralatan laboratorium. Beliau memiliki pengalaman dalam perbaikan dan modifikasi sistem mekanik untuk berbagai jenis peralatan analitik.',
                'research_interests' => 'Mechanical systems maintenance, Equipment modification, Workshop techniques',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Lisa Noviana, M.Si',
                'position' => 'Research Scientist',
                'specialization' => 'Biofisika dan Aplikasi Medis',
                'education' => 'S3 Biophysics - National University of Singapore (2021), S2 Fisika - ITB (2017), S1 Fisika - USU (2015)',
                'email' => 'lisa.noviana@usu.ac.id',
                'phone' => '+62 823-4567-8901',
                'photo_path' => 'staff/lisa-noviana.jpg',
                'bio' => 'Dr. Lisa Noviana adalah peneliti yang fokus pada aplikasi teknik optik dan spektroskopi dalam bidang biomedis. Beliau mengembangkan metode diagnostik non-invasif menggunakan teknologi optik.',
                'research_interests' => 'Biomedical optics, Non-invasive diagnostics, Tissue characterization, Medical device development',
                'sort_order' => 12,
                'is_active' => true,
            ],

            // Alumni/Former Staff (inactive)
            [
                'name' => 'Dr. Robert Simanjuntak, M.Sc',
                'position' => 'Former Lab Manager',
                'specialization' => 'Laser Physics',
                'education' => 'S3 Physics - University of California (2010), S2 Physics - Stanford University (2005), S1 Fisika - ITB (2003)',
                'email' => 'robert.simanjuntak@alumni.usu.ac.id',
                'phone' => null,
                'photo_path' => 'staff/robert-simanjuntak.jpg',
                'bio' => 'Dr. Robert Simanjuntak adalah mantan Lab Manager yang telah berpindah ke industri pada tahun 2023. Beliau berkontribusi besar dalam membangun reputasi Lab GOS dalam bidang laser applications.',
                'research_interests' => 'Laser physics, Industrial applications, Precision measurements',
                'sort_order' => 99,
                'is_active' => false,
            ],
        ];

        foreach ($staffMembers as $staff) {
            StaffMember::create($staff);
        }

        $this->command->info('✅ Created ' . count($staffMembers) . ' staff members (includes ' . count(array_filter($staffMembers, fn($s) => !$s['is_active'])) . ' inactive)');
    }
}
