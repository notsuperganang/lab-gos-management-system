<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Basic Lab Information
            [
                'key' => 'lab_name',
                'title' => 'Nama Laboratorium',
                'content' => 'Laboratorium Getaran, Optik, dan Spektroskopi (GOS)',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'lab_acronym',
                'title' => 'Akronim Laboratorium',
                'content' => 'Lab GOS',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'institution_name',
                'title' => 'Nama Institusi',
                'content' => 'Universitas Sumatera Utara',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'department_name',
                'title' => 'Nama Departemen',
                'content' => 'Departemen Fisika',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'faculty_name',
                'title' => 'Nama Fakultas',
                'content' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam',
                'type' => 'text',
                'is_active' => true,
            ],

            // Vision & Mission
            [
                'key' => 'vision',
                'title' => 'Visi Laboratorium',
                'content' => 'Menjadi laboratorium unggulan dalam bidang getaran, optik, dan spektroskopi yang berkontribusi pada pengembangan ilmu pengetahuan dan teknologi untuk kesejahteraan masyarakat.',
                'type' => 'rich_text',
                'is_active' => true,
            ],
            [
                'key' => 'mission',
                'title' => 'Misi Laboratorium',
                'content' => '<ul>
                    <li>Menyelenggarakan praktikum dan penelitian berkualitas tinggi dalam bidang getaran, optik, dan spektroskopi</li>
                    <li>Menyediakan fasilitas dan peralatan canggih untuk mendukung kegiatan pembelajaran dan penelitian</li>
                    <li>Mengembangkan kompetensi mahasiswa dan peneliti dalam penguasaan teknologi instrumentasi</li>
                    <li>Melakukan kerjasama dengan industri dan institusi lain dalam pengembangan aplikasi teknologi</li>
                    <li>Berkontribusi dalam pengembangan ilmu pengetahuan melalui publikasi dan inovasi</li>
                </ul>',
                'type' => 'rich_text',
                'is_active' => true,
            ],

            // Contact Information
            [
                'key' => 'address',
                'title' => 'Alamat Laboratorium',
                'content' => 'Gedung J7 Lantai 2, Departemen Fisika FMIPA USU
Jl. Bioteknologi No. 1, Kampus USU Padang Bulan
Medan, Sumatera Utara 20155',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'phone',
                'title' => 'Nomor Telepon',
                'content' => '+62 61 8211050',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'email',
                'title' => 'Email Laboratorium',
                'content' => 'labgos@usu.ac.id',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'website',
                'title' => 'Website Laboratorium',
                'content' => 'https://labgos.fisika.usu.ac.id',
                'type' => 'text',
                'is_active' => true,
            ],

            // Operating Hours
            [
                'key' => 'operating_hours',
                'title' => 'Jam Operasional',
                'content' => json_encode([
                    'senin' => '08:00 - 16:00',
                    'selasa' => '08:00 - 16:00',
                    'rabu' => '08:00 - 16:00',
                    'kamis' => '08:00 - 16:00',
                    'jumat' => '08:00 - 11:30, 13:30 - 16:00',
                    'sabtu' => 'Tutup',
                    'minggu' => 'Tutup',
                    'catatan' => 'Jam operasional dapat berubah pada hari libur nasional'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],

            // About Laboratory
            [
                'key' => 'about',
                'title' => 'Tentang Laboratorium',
                'content' => '<p>Laboratorium Getaran, Optik, dan Spektroskopi (Lab GOS) merupakan salah satu laboratorium unggulan di Departemen Fisika, Fakultas MIPA, Universitas Sumatera Utara. Laboratorium ini didirikan pada tahun 2010 dengan tujuan untuk mendukung kegiatan pembelajaran dan penelitian dalam bidang fisika instrumentasi.</p>

<p>Lab GOS dilengkapi dengan peralatan canggih dan modern untuk berbagai keperluan penelitian dan praktikum mahasiswa. Beberapa peralatan unggulan yang tersedia meliputi spektrometer UV-Vis, FTIR, mikroskop optik, sistem laser, dan berbagai peralatan elektronik instrumentasi.</p>

<p>Laboratorium ini melayani kegiatan praktikum untuk mata kuliah Fisika Instrumentasi, Getaran dan Gelombang, Optik, dan Spektroskopi. Selain itu, Lab GOS juga membuka layanan untuk penelitian mahasiswa S1, S2, S3, serta kerjasama penelitian dengan pihak eksternal.</p>',
                'type' => 'rich_text',
                'is_active' => true,
            ],

            // Services
            [
                'key' => 'services',
                'title' => 'Layanan Laboratorium',
                'content' => json_encode([
                    'praktikum' => [
                        'title' => 'Praktikum Mahasiswa',
                        'description' => 'Praktikum untuk mata kuliah Fisika Instrumentasi, Getaran dan Gelombang, Optik, dan Spektroskopi'
                    ],
                    'penelitian' => [
                        'title' => 'Penelitian',
                        'description' => 'Fasilitas penelitian untuk mahasiswa S1, S2, S3 dan dosen'
                    ],
                    'testing' => [
                        'title' => 'Layanan Testing',
                        'description' => 'Layanan analisis dan karakterisasi material menggunakan spektroskopi UV-Vis dan FTIR'
                    ],
                    'pelatihan' => [
                        'title' => 'Pelatihan',
                        'description' => 'Pelatihan penggunaan peralatan instrumentasi untuk mahasiswa dan peneliti'
                    ],
                    'konsultasi' => [
                        'title' => 'Konsultasi',
                        'description' => 'Konsultasi teknis dalam bidang instrumentasi dan analisis data'
                    ]
                ]),
                'type' => 'json',
                'is_active' => true,
            ],

            // Research Areas
            [
                'key' => 'research_areas',
                'title' => 'Bidang Penelitian',
                'content' => json_encode([
                    'Material Characterization',
                    'Optical Properties of Materials',
                    'Vibrational Spectroscopy',
                    'Laser Applications',
                    'Sensor Development',
                    'Nondestructive Testing',
                    'Biomedical Optics',
                    'Environmental Monitoring'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],

            // Social Media
            [
                'key' => 'social_media',
                'title' => 'Media Sosial',
                'content' => json_encode([
                    'instagram' => '@labgos_usu',
                    'twitter' => '@labgos_usu',
                    'youtube' => 'Lab GOS USU',
                    'linkedin' => 'company/lab-gos-usu'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],

            // Policies and Procedures
            [
                'key' => 'safety_policy',
                'title' => 'Kebijakan Keselamatan',
                'content' => '<h3>Kebijakan Keselamatan Laboratorium</h3>
<ol>
    <li><strong>Alat Pelindung Diri (APD)</strong>: Wajib menggunakan APD yang sesuai saat bekerja di laboratorium</li>
    <li><strong>Pelatihan Keselamatan</strong>: Semua pengguna harus mengikuti pelatihan keselamatan sebelum menggunakan peralatan</li>
    <li><strong>Prosedur Darurat</strong>: Setiap pengguna harus memahami prosedur darurat dan lokasi peralatan keselamatan</li>
    <li><strong>Penggunaan Laser</strong>: Khusus untuk peralatan laser, wajib menggunakan kacamata pengaman laser</li>
    <li><strong>Pelaporan Insiden</strong>: Setiap insiden atau kecelakaan harus dilaporkan segera kepada penanggung jawab laboratorium</li>
</ol>',
                'type' => 'rich_text',
                'is_active' => true,
            ],

            // Equipment Usage Policy
            [
                'key' => 'equipment_usage_policy',
                'title' => 'Kebijakan Penggunaan Peralatan',
                'content' => '<h3>Aturan Penggunaan Peralatan</h3>
<ol>
    <li><strong>Reservasi</strong>: Peralatan harus direservasi terlebih dahulu melalui sistem online</li>
    <li><strong>Pelatihan</strong>: Pengguna harus telah mengikuti pelatihan penggunaan peralatan yang bersangkutan</li>
    <li><strong>Supervisi</strong>: Mahasiswa S1 harus didampingi oleh asisten atau pembimbing</li>
    <li><strong>Maintenance</strong>: Laporkan segera jika terdapat kerusakan atau masalah pada peralatan</li>
    <li><strong>Dokumentasi</strong>: Catat setiap penggunaan peralatan dalam logbook yang tersedia</li>
</ol>',
                'type' => 'rich_text',
                'is_active' => true,
            ],

            // Footer Information
            [
                'key' => 'footer_text',
                'title' => 'Teks Footer',
                'content' => '© 2024 Laboratorium Getaran, Optik, dan Spektroskopi - Departemen Fisika FMIPA USU. All rights reserved.',
                'type' => 'text',
                'is_active' => true,
            ],

            // Logo and Branding
            [
                'key' => 'lab_logo',
                'title' => 'Logo Laboratorium',
                'content' => 'images/lab-logo.png',
                'type' => 'image',
                'is_active' => true,
            ],
            [
                'key' => 'institution_logo',
                'title' => 'Logo Institusi',
                'content' => 'images/usu-logo.png',
                'type' => 'image',
                'is_active' => true,
            ],

            // Contact Person
            [
                'key' => 'lab_head',
                'title' => 'Kepala Laboratorium',
                'content' => json_encode([
                    'name' => 'Dr. Ahmad Fadli, M.Si',
                    'email' => 'ahmad.fadli@usu.ac.id',
                    'phone' => '+62 812-3456-7890',
                    'office' => 'Ruang J7-201'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],
            [
                'key' => 'technical_contact',
                'title' => 'Kontak Teknis',
                'content' => json_encode([
                    'name' => 'Dr. Siti Nurhaliza, M.Sc',
                    'email' => 'siti.nurhaliza@usu.ac.id',
                    'phone' => '+62 813-4567-8901',
                    'specialization' => 'Spektroskopi dan Karakterisasi Material'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }

        $this->command->info('✅ Created ' . count($settings) . ' site settings');
    }
}
