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
                'content' => 'Universitas Syiah Kuala',
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
                'content' => 'Departemen Fisika FMIPA Universitas Syiah Kuala
Jl. Teuku Nyak Arief, Darussalam
Banda Aceh, Aceh 23111',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'phone',
                'title' => 'Nomor Telepon',
                'content' => '+62 651-7551843',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'email',
                'title' => 'Email Laboratorium',
                'content' => 'labgos@unsyiah.ac.id',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'website',
                'title' => 'Website Laboratorium',
                'content' => 'https://labgos.fisika.unsyiah.ac.id',
                'type' => 'text',
                'is_active' => true,
            ],


            // About Laboratory
            [
                'key' => 'about',
                'title' => 'Tentang Laboratorium',
                'content' => '<p>Laboratorium Getaran, Optik, dan Spektroskopi (Lab GOS) merupakan salah satu laboratorium unggulan di Departemen Fisika, Fakultas MIPA, Universitas Syiah Kuala. Laboratorium ini didirikan dengan tujuan untuk mendukung kegiatan pembelajaran dan penelitian dalam bidang fisika instrumentasi.</p>

<p>Lab GOS dilengkapi dengan peralatan canggih dan modern untuk berbagai keperluan penelitian dan praktikum mahasiswa. Beberapa peralatan unggulan yang tersedia meliputi spektrometer UV-Vis, FTIR, mikroskop optik, sistem laser, dan berbagai peralatan elektronik instrumentasi.</p>

<p>Laboratorium ini melayani kegiatan praktikum untuk mata kuliah Fisika Instrumentasi, Getaran dan Gelombang, Optik, dan Spektroskopi. Selain itu, Lab GOS juga membuka layanan untuk penelitian mahasiswa S1, S2, S3, serta kerjasama penelitian dengan pihak eksternal.</p>',
                'type' => 'rich_text',
                'is_active' => true,
            ],


            // Social Media
            [
                'key' => 'social_media',
                'title' => 'Media Sosial',
                'content' => json_encode([
                    'instagram' => '@labgos_unsyiah',
                    'twitter' => '@labgos_unsyiah',
                    'youtube' => 'Lab GOS Unsyiah',
                    'linkedin' => 'company/lab-gos-unsyiah'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],


            // Contact Person
            [
                'key' => 'lab_head',
                'title' => 'Kepala Laboratorium',
                'content' => json_encode([
                    'name' => 'Dr. Ir. Muhammad Syukri, M.Sc',
                    'nip' => '196808121994031002',
                    'email' => 'muhammad.syukri@unsyiah.ac.id',
                    'phone' => '+62 651-7551843',
                    'office' => 'Laboratorium Gelombang, Optik dan Spektroskopi'
                ]),
                'type' => 'json',
                'is_active' => true,
            ],
            [
                'key' => 'technical_contact',
                'title' => 'Kontak Teknis',
                'content' => json_encode([
                    'name' => 'Dr. Ir. Sulaiman, M.Si',
                    'email' => 'sulaiman@unsyiah.ac.id',
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
