<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Laboratorium GOS
    |--------------------------------------------------------------------------
    | Konfigurasi ini berdasarkan SOP Laboratorium Gelombang, Optik dan Spektroskopi
    | Departemen Fisika FMIPA Universitas Syiah Kuala
    */

    'name' => env('LAB_NAME', 'Laboratorium Gelombang, Optik dan Spektroskopi'),
    'code' => env('LAB_CODE', 'GOS'),
    'department' => env('LAB_DEPARTMENT', 'Departemen Fisika FMIPA USK'),
    'address' => env('LAB_ADDRESS', 'Darussalam-Banda Aceh'),
    
    'vision' => 'Menjadi pusat unggulan dalam pendidikan dan penelitian di bidang Gelombang, Optik dan Spektroskopi untuk mendukung pengembangan ilmu pengetahuan, mitigasi bencana, pengelolaan lingkungan, dan pembangunan berkelanjutan pada tahun 2030.',
    
    'colors' => [
        'primary' => '#1E40AF',      // Biru Fisika USK
        'secondary' => '#FDB813',    // Emas USK
        'white' => '#FFFFFF',        // Putih Bersih
        'text' => '#212529',         // Hitam Teks
        'light_gray' => '#E9ECEF',   // Abu-abu Muda
    ],
    
    'contact' => [
        'phone' => '',
        'email' => 'lab-gos@usk.ac.id',
        'website' => 'https://fisika.usk.ac.id',
    ],
    
    'operational_hours' => [
        'monday' => '08:00-16:00',
        'tuesday' => '08:00-16:00',
        'wednesday' => '08:00-16:00',
        'thursday' => '08:00-16:00',
        'friday' => '08:00-11:30',
        'saturday' => 'Tutup',
        'sunday' => 'Tutup',
    ],
    
    'services' => [
        'equipment_rental' => [
            'name' => 'Penyewaan Alat Laboratorium',
            'enabled' => true,
            'requires_approval' => true,
        ],
        'lab_visit' => [
            'name' => 'Kunjungan Laboratorium',
            'enabled' => true,
            'requires_approval' => true,
            'advance_booking_days' => 7,
        ],
        'testing_service' => [
            'name' => 'Pengujian dan Analisis Sampel',
            'enabled' => true,
            'requires_approval' => true,
        ],
    ],
    
    'rules' => [
        'equipment_damage' => 'Setiap kerusakan alat-alat/instrumen yang diakibatkan kelalaian, peralatan tersebut harus diperbaiki/diganti segera oleh dosen/mahasiswa secara pribadi atau kelompok atau bersama-sama tergantung kesepakatan.',
        'safety_equipment' => 'Peneliti wajib menggunakan Alat Pelindung Diri (APD) selama kegiatan penelitian.',
        'advance_notice' => 'Pemberitahuan tertulis minimal satu minggu sebelum kegiatan penelitian dilakukan.',
        'logbook_required' => 'Logbook pemakaian peralatan diisi dan ditandatangani oleh peneliti yang bersangkutan.',
    ],
    
    'forms' => [
        'research_permission' => [
            'internal_students' => 'Form Izin Penelitian Mahasiswa Internal',
            'external_researchers' => 'Form Izin Penelitian Pihak Eksternal',
            'equipment_usage' => 'Form Izin Pemakaian Alat',
        ],
    ],
];