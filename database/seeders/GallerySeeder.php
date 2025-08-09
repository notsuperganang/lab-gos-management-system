<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            // Lab Facilities
            [
                'title' => 'Ruang Spektroskopi Utama',
                'description' => 'Ruang spektroskopi yang dilengkapi dengan peralatan UV-Vis dan FTIR spectrometer untuk analisis karakterisasi material.',
                'image_path' => 'gallery/lab-facilities/spektroskopi-room-main.jpg',
                'alt_text' => 'Ruang spektroskopi dengan peralatan UV-Vis dan FTIR',
                'category' => 'lab_facilities',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Lab Optik dan Laser',
                'description' => 'Laboratorium optik yang dilengkapi dengan optical bench, laser He-Ne, dan berbagai komponen optik untuk eksperimen interferometri dan holografi.',
                'image_path' => 'gallery/lab-facilities/optics-laser-lab.jpg',
                'alt_text' => 'Laboratorium optik dengan optical bench dan laser',
                'category' => 'lab_facilities',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Ruang Mikroskopi',
                'description' => 'Ruang mikroskopi yang dilengkapi dengan berbagai jenis mikroskop optik untuk pengamatan struktur mikro material.',
                'image_path' => 'gallery/lab-facilities/microscopy-room.jpg',
                'alt_text' => 'Ruang mikroskopi dengan berbagai jenis mikroskop',
                'category' => 'lab_facilities',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Lab Elektronik dan Instrumentasi',
                'description' => 'Laboratorium elektronik yang dilengkapi dengan oscilloscope, function generator, dan peralatan elektronik untuk pengembangan instrumentasi.',
                'image_path' => 'gallery/lab-facilities/electronics-lab.jpg',
                'alt_text' => 'Laboratorium elektronik dengan peralatan instrumentasi',
                'category' => 'lab_facilities',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Ruang Analisis Data',
                'description' => 'Ruang komputasi yang dilengkapi dengan workstation untuk analisis data spektroskopi dan pemodelan menggunakan software khusus.',
                'image_path' => 'gallery/lab-facilities/data-analysis-room.jpg',
                'alt_text' => 'Ruang analisis data dengan workstation komputer',
                'category' => 'lab_facilities',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Storage dan Preparasi Sampel',
                'description' => 'Ruang penyimpanan dan preparasi sampel yang dilengkapi dengan fume hood dan peralatan preparasi untuk berbagai jenis material.',
                'image_path' => 'gallery/lab-facilities/sample-prep-storage.jpg',
                'alt_text' => 'Ruang preparasi sampel dengan fume hood',
                'category' => 'lab_facilities',
                'sort_order' => 6,
                'is_active' => true,
            ],

            // Equipment
            [
                'title' => 'UV-Vis Spectrophotometer Shimadzu UV-2600i',
                'description' => 'Spektrofotometer UV-Vis terbaru untuk analisis kualitatif dan kuantitatif material dengan rentang panjang gelombang 185-900 nm.',
                'image_path' => 'gallery/equipment/uv-vis-shimadzu-2600i.jpg',
                'alt_text' => 'UV-Vis Spectrophotometer Shimadzu UV-2600i',
                'category' => 'equipment',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'FTIR Spectrometer PerkinElmer Spectrum Two',
                'description' => 'Spektrometer FTIR untuk analisis struktur molekul dan identifikasi senyawa organik dengan teknologi interferometer RockSolid.',
                'image_path' => 'gallery/equipment/ftir-perkinelmer-spectrum-two.jpg',
                'alt_text' => 'FTIR Spectrometer PerkinElmer Spectrum Two',
                'category' => 'equipment',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Optical Microscope Nikon Eclipse Ci-L',
                'description' => 'Mikroskop optik dengan sistem illuminasi LED dan objektif berkualitas tinggi untuk pengamatan struktur mikro material.',
                'image_path' => 'gallery/equipment/optical-microscope-nikon.jpg',
                'alt_text' => 'Optical Microscope Nikon Eclipse Ci-L',
                'category' => 'equipment',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'He-Ne Laser System',
                'description' => 'Sistem laser Helium-Neon dengan output 632.8 nm untuk eksperimen interferometri dan aplikasi optik presisi.',
                'image_path' => 'gallery/equipment/he-ne-laser-system.jpg',
                'alt_text' => 'He-Ne Laser System untuk eksperimen optik',
                'category' => 'equipment',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Digital Oscilloscope Tektronix',
                'description' => 'Oscilloscope digital dengan bandwidth 200 MHz untuk analisis sinyal dan pengembangan sistem elektronik.',
                'image_path' => 'gallery/equipment/oscilloscope-tektronix.jpg',
                'alt_text' => 'Digital Oscilloscope Tektronix TBS1202B',
                'category' => 'equipment',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Atomic Force Microscope Bruker',
                'description' => 'AFM untuk karakterisasi topografi permukaan material dengan resolusi sub-angstrom dan berbagai mode pengukuran.',
                'image_path' => 'gallery/equipment/afm-bruker-dimension.jpg',
                'alt_text' => 'Atomic Force Microscope Bruker Dimension Icon',
                'category' => 'equipment',
                'sort_order' => 6,
                'is_active' => true,
            ],

            // Activities
            [
                'title' => 'Praktikum Spektroskopi UV-Vis',
                'description' => 'Kegiatan praktikum mahasiswa S1 dalam mata kuliah Fisika Instrumentasi menggunakan spektrometer UV-Vis untuk analisis larutan.',
                'image_path' => 'gallery/activities/praktikum-uv-vis.jpg',
                'alt_text' => 'Mahasiswa sedang melakukan praktikum UV-Vis',
                'category' => 'activities',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Training Penggunaan FTIR',
                'description' => 'Sesi training penggunaan spektrometer FTIR untuk mahasiswa pascasarjana dan peneliti, termasuk teknik preparasi sampel.',
                'image_path' => 'gallery/activities/training-ftir.jpg',
                'alt_text' => 'Sesi training penggunaan FTIR spectrometer',
                'category' => 'activities',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Eksperimen Interferometri Laser',
                'description' => 'Mahasiswa melakukan eksperimen interferometri menggunakan laser He-Ne untuk mempelajari sifat gelombang cahaya.',
                'image_path' => 'gallery/activities/laser-interferometry-experiment.jpg',
                'alt_text' => 'Eksperimen interferometri dengan laser He-Ne',
                'category' => 'activities',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Penelitian Material Nanokomposit',
                'description' => 'Kegiatan penelitian karakterisasi material nanokomposit menggunakan berbagai teknik spektroskopi dan mikroskopi.',
                'image_path' => 'gallery/activities/nanocomposite-research.jpg',
                'alt_text' => 'Penelitian karakterisasi material nanokomposit',
                'category' => 'activities',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Workshop Instrumentasi untuk Industri',
                'description' => 'Workshop aplikasi instrumentasi untuk QC industri yang dihadiri oleh professional dari berbagai perusahaan.',
                'image_path' => 'gallery/activities/industry-workshop.jpg',
                'alt_text' => 'Workshop instrumentasi untuk industri',
                'category' => 'activities',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Kalibrasi Peralatan Rutin',
                'description' => 'Kegiatan kalibrasi rutin peralatan spektroskopi oleh teknisi certified untuk menjaga akurasi dan kualitas hasil analisis.',
                'image_path' => 'gallery/activities/equipment-calibration.jpg',
                'alt_text' => 'Kalibrasi peralatan spektroskopi oleh teknisi',
                'category' => 'activities',
                'sort_order' => 6,
                'is_active' => true,
            ],

            // Events
            [
                'title' => 'Lab GOS Open House 2024',
                'description' => 'Acara open house tahunan Lab GOS yang mengundang mahasiswa, dosen, dan masyarakat umum untuk mengenal fasilitas laboratorium.',
                'image_path' => 'gallery/events/open-house-2024.jpg',
                'alt_text' => 'Acara Lab GOS Open House 2024',
                'category' => 'events',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Seminar Nasional Spektroskopi 2024',
                'description' => 'Seminar nasional tentang perkembangan terkini teknologi spektroskopi dan aplikasinya dalam penelitian material.',
                'image_path' => 'gallery/events/seminar-nasional-spektroskopi.jpg',
                'alt_text' => 'Seminar Nasional Spektroskopi 2024',
                'category' => 'events',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Kunjungan Delegasi Universitas Malaysia',
                'description' => 'Kunjungan delegasi dari Universiti Sains Malaysia untuk benchmarking fasilitas laboratorium dan diskusi kerjasama penelitian.',
                'image_path' => 'gallery/events/malaysia-delegation-visit.jpg',
                'alt_text' => 'Kunjungan delegasi Universitas Malaysia',
                'category' => 'events',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Ceremony Penghargaan Lab Terbaik',
                'description' => 'Ceremony penerimaan penghargaan Laboratory Excellence Award 2024 yang diselenggarakan di Jakarta.',
                'image_path' => 'gallery/events/award-ceremony-2024.jpg',
                'alt_text' => 'Ceremony penghargaan Laboratory Excellence Award',
                'category' => 'events',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Launching Collaboration dengan Pertamina',
                'description' => 'Acara launching kerjasama penelitian dengan PT Pertamina untuk pengembangan sensor gas berbasis optik.',
                'image_path' => 'gallery/events/pertamina-collaboration-launch.jpg',
                'alt_text' => 'Launching kerjasama dengan PT Pertamina',
                'category' => 'events',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Student Research Competition 2024',
                'description' => 'Kompetisi penelitian mahasiswa tingkat nasional yang diselenggarakan oleh Lab GOS dengan tema instrumentasi dan karakterisasi material.',
                'image_path' => 'gallery/events/student-research-competition.jpg',
                'alt_text' => 'Student Research Competition 2024',
                'category' => 'events',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'International Conference on Optical Sciences',
                'description' => 'Konferensi internasional tentang optical sciences yang dihadiri oleh peneliti dari berbagai negara.',
                'image_path' => 'gallery/events/international-optics-conference.jpg',
                'alt_text' => 'International Conference on Optical Sciences',
                'category' => 'events',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Safety Training Session',
                'description' => 'Sesi pelatihan keselamatan laboratorium yang wajib diikuti oleh semua pengguna fasilitas Lab GOS.',
                'image_path' => 'gallery/events/safety-training-session.jpg',
                'alt_text' => 'Safety Training Session untuk pengguna lab',
                'category' => 'events',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $gallery) {
            Gallery::create($gallery);
        }

        $this->command->info('✅ Created ' . count($galleries) . ' gallery items across all categories');
    }
}
