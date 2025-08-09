<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for publishing
        $users = User::where('is_active', true)->get();
        $superAdmin = $users->where('role', 'super_admin')->first();
        $admins = $users->where('role', 'admin');

        $articles = [
            // Research Articles
            [
                'title' => 'Karakterisasi Sifat Optik Nanopartikel Emas Menggunakan Spektroskopi UV-Vis',
                'slug' => 'karakterisasi-sifat-optik-nanopartikel-emas-spektroskopi-uv-vis',
                'excerpt' => 'Penelitian ini mengkaji sifat optik nanopartikel emas yang disintesis dengan metode reduksi kimia menggunakan spektroskopi UV-Vis dan analisis data komprehensif.',
                'content' => '<h2>Abstrak</h2>
<p>Nanopartikel emas (AuNPs) telah menarik perhatian besar dalam berbagai aplikasi biomedis dan teknologi karena sifat optiknya yang unik. Penelitian ini bertujuan untuk mengkarakterisasi sifat optik AuNPs yang disintesis menggunakan metode reduksi kimia dengan natrium borohydrida sebagai agen pereduksi.</p>

<h2>Metodologi</h2>
<p>Sintesis AuNPs dilakukan dengan mereduksi HAuCl₄ menggunakan NaBH₄ dalam suasana basa. Karakterisasi dilakukan menggunakan spektrometer UV-Vis (Shimadzu UV-2600i) pada rentang panjang gelombang 300-800 nm. Analisis morfologi dilakukan menggunakan mikroskop elektron transmisi (TEM).</p>

<h2>Hasil dan Pembahasan</h2>
<p>Spektrum UV-Vis menunjukkan puncak absorbansi karakteristik pada 520 nm yang merupakan ciri khas plasmon resonans permukaan AuNPs. Intensitas dan posisi puncak dipengaruhi oleh ukuran dan konsentrasi nanopartikel. Analisis TEM mengkonfirmasi pembentukan nanopartikel sferis dengan diameter rata-rata 15 ± 3 nm.</p>

<h2>Kesimpulan</h2>
<p>Metode sintesis yang digunakan berhasil menghasilkan AuNPs dengan sifat optik yang stabil dan reproducible. Spektroskopi UV-Vis terbukti efektif untuk karakterisasi cepat sifat optik nanopartikel emas.</p>

<p><strong>Keywords:</strong> nanopartikel emas, spektroskopi UV-Vis, plasmon resonans, karakterisasi optik</p>',
                'featured_image_path' => 'articles/aunps-uv-vis-research.jpg',
                'author_name' => 'Dr. Ahmad Fadli, M.Si',
                'category' => 'research',
                'tags' => ['nanopartikel', 'spektroskopi', 'UV-Vis', 'karakterisasi', 'optik'],
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'published_by' => $superAdmin->id,
                'views_count' => 245,
            ],
            [
                'title' => 'Pengembangan Sensor Fiber Optik untuk Monitoring Kualitas Air Real-Time',
                'slug' => 'pengembangan-sensor-fiber-optik-monitoring-kualitas-air',
                'excerpt' => 'Penelitian ini mengembangkan sistem sensor berbasis fiber optik untuk monitoring parameter kualitas air secara real-time dengan akurasi tinggi.',
                'content' => '<h2>Pendahuluan</h2>
<p>Monitoring kualitas air secara real-time merupakan tantangan penting dalam menjaga kesehatan lingkungan. Sensor konvensional seringkali memiliki keterbatasan dalam hal response time dan stability jangka panjang.</p>

<h2>Desain Sensor</h2>
<p>Sensor yang dikembangkan menggunakan fiber optic multimode dengan coating khusus yang sensitif terhadap perubahan indeks refraksi medium. Sistem deteksi menggunakan LED sebagai sumber cahaya dan photodiode sebagai detektor.</p>

<h2>Hasil Pengujian</h2>
<p>Sensor menunjukkan sensitivitas tinggi terhadap perubahan konsentrasi kontaminan dengan limit deteksi 0.1 ppm untuk logam berat. Response time kurang dari 30 detik dengan stabilitas long-term yang baik (drift < 2% per bulan).</p>

<h2>Aplikasi</h2>
<p>Sistem sensor ini telah diuji coba di beberapa lokasi monitoring kualitas air dan menunjukkan hasil yang konsisten dengan metode analisis konvensional.</p>',
                'featured_image_path' => 'articles/fiber-optic-sensor.jpg',
                'author_name' => 'Dr. Rahmat Hidayat, M.Eng',
                'category' => 'research',
                'tags' => ['fiber optik', 'sensor', 'monitoring', 'kualitas air', 'real-time'],
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'published_by' => $admins->random()->id,
                'views_count' => 189,
            ],

            // News Articles
            [
                'title' => 'Lab GOS Raih Penghargaan Laboratorium Terbaik Tingkat Nasional 2024',
                'slug' => 'lab-gos-raih-penghargaan-laboratorium-terbaik-nasional-2024',
                'excerpt' => 'Laboratorium Getaran, Optik, dan Spektroskopi USU meraih penghargaan sebagai Laboratorium Fisika Terbaik tingkat nasional dalam ajang Laboratory Excellence Award 2024.',
                'content' => '<p>Medan, 15 Februari 2024 - Laboratorium Getaran, Optik, dan Spektroskopi (Lab GOS) Departemen Fisika FMIPA USU berhasil meraih penghargaan bergengsi sebagai "Laboratorium Fisika Terbaik Tingkat Nasional" dalam ajang Laboratory Excellence Award 2024 yang diselenggarakan oleh Asosiasi Laboratorium Indonesia.</p>

<p>Penghargaan ini diberikan berdasarkan penilaian komprehensif yang meliputi kualitas fasilitas, manajemen laboratorium, kontribusi penelitian, dan dampak terhadap pengembangan sumber daya manusia di bidang sains dan teknologi.</p>

<blockquote>
<p>"Penghargaan ini adalah hasil kerja keras seluruh tim Lab GOS selama bertahun-tahun. Kami berkomitmen untuk terus meningkatkan kualitas layanan dan berkontribusi pada pengembangan ilmu pengetahuan di Indonesia," ungkap Dr. Ahmad Fadli, M.Si, Kepala Lab GOS.</p>
</blockquote>

<p>Lab GOS dinilai unggul dalam hal:</p>
<ul>
<li>Fasilitas peralatan yang modern dan terawat dengan baik</li>
<li>Sistem manajemen kualitas yang memenuhi standar ISO 17025</li>
<li>Produktivitas penelitian dengan lebih dari 50 publikasi internasional dalam 3 tahun terakhir</li>
<li>Program pelatihan dan capacity building yang berkelanjutan</li>
<li>Kerjasama yang luas dengan industri dan institusi penelitian</li>
</ul>

<p>Penghargaan ini semakin memperkuat posisi USU sebagai salah satu universitas terdepan dalam bidang penelitian dan pendidikan tinggi di Indonesia.</p>',
                'featured_image_path' => 'articles/lab-award-2024.jpg',
                'author_name' => 'Tim Lab GOS',
                'category' => 'news',
                'tags' => ['penghargaan', 'laboratorium', 'nasional', 'excellence', 'achievement'],
                'is_published' => true,
                'published_at' => now()->subDays(30),
                'published_by' => $superAdmin->id,
                'views_count' => 892,
            ],
            [
                'title' => 'Workshop Spektroskopi FTIR untuk Industri Farmasi',
                'slug' => 'workshop-spektroskopi-ftir-industri-farmasi',
                'excerpt' => 'Lab GOS menyelenggarakan workshop khusus tentang aplikasi spektroskopi FTIR dalam quality control industri farmasi untuk meningkatkan kompetensi SDM industri.',
                'content' => '<p>Lab GOS akan menyelenggarakan workshop intensif dengan tema "Aplikasi Spektroskopi FTIR dalam Quality Control Industri Farmasi" pada tanggal 25-26 Maret 2024. Workshop ini ditujukan untuk profesional industri farmasi, quality control analyst, dan peneliti di bidang pharmaceutical sciences.</p>

<h3>Materi Workshop</h3>
<ul>
<li>Prinsip dasar spektroskopi FTIR</li>
<li>Sample preparation techniques untuk sediaan farmasi</li>
<li>Analisis kualitatif dan kuantitatif</li>
<li>Validasi metode analisis</li>
<li>Troubleshooting dan maintenance peralatan</li>
<li>Hands-on practice dengan sampel industri</li>
</ul>

<h3>Narasumber</h3>
<p>Workshop akan dipandu oleh tim ahli Lab GOS yang berpengalaman, didukung oleh narasumber dari industri farmasi terkemuka dan regulatory body.</p>

<h3>Fasilitas</h3>
<p>Peserta akan mendapatkan akses langsung ke peralatan FTIR terbaru (PerkinElmer Spectrum Two) dan software analisis data professional. Setiap peserta akan mendapatkan sertifikat dan modul pembelajaran komprehensif.</p>

<h3>Pendaftaran</h3>
<p>Pendaftaran dibuka hingga 20 Maret 2024 dengan kapasitas terbatas 20 peserta. Biaya workshop Rp 2.500.000 per peserta sudah termasuk materi, konsumsi, dan sertifikat.</p>

<p>Info lengkap dan pendaftaran: <a href="mailto:training@labgos.usu.ac.id">training@labgos.usu.ac.id</a></p>',
                'featured_image_path' => 'articles/ftir-workshop-pharma.jpg',
                'author_name' => 'Dr. Siti Nurhaliza, M.Sc',
                'category' => 'announcement',
                'tags' => ['workshop', 'FTIR', 'farmasi', 'training', 'quality control'],
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'published_by' => $admins->random()->id,
                'views_count' => 156,
            ],

            // Publication Articles
            [
                'title' => 'Publikasi Terbaru: "Advanced Optical Characterization of 2D Materials"',
                'slug' => 'publikasi-advanced-optical-characterization-2d-materials',
                'excerpt' => 'Tim peneliti Lab GOS berhasil menerbitkan paper di jurnal internasional Nature Photonics tentang teknik karakterisasi optik material 2D generasi baru.',
                'content' => '<p>Tim peneliti Lab GOS yang dipimpin oleh Prof. Dr. Andi Rahman, M.Sc berhasil menerbitkan paper breakthrough di jurnal bergengsi Nature Photonics dengan judul "Advanced Optical Characterization of Two-Dimensional Materials Using Multi-Modal Spectroscopy".</p>

<h3>Inovasi Penelitian</h3>
<p>Penelitian ini mengembangkan metodologi baru untuk karakterisasi material 2D menggunakan kombinasi spektroskopi Raman, fotoluminensi, dan reflektansi diferensial. Pendekatan multi-modal ini memungkinkan analisis comprehensive sifat elektronik dan optik material 2D dengan akurasi yang belum pernah dicapai sebelumnya.</p>

<h3>Impact dan Aplikasi</h3>
<p>Metode yang dikembangkan memiliki aplikasi luas dalam:</p>
<ul>
<li>Quality control produksi material 2D untuk industri elektronik</li>
<li>Pengembangan device optoelektronik generasi baru</li>
<li>Fundamental research dalam condensed matter physics</li>
<li>Environmental monitoring menggunakan 2D sensors</li>
</ul>

<h3>Kolaborasi Internasional</h3>
<p>Penelitian ini merupakan hasil kolaborasi dengan MIT (USA), University of Cambridge (UK), dan RIKEN (Japan). Tim Lab GOS berkontribusi dalam pengembangan setup eksperimental dan analisis data spektroskopi.</p>

<h3>Citation dan Recognition</h3>
<p>Paper ini telah menerima lebih dari 25 citasi dalam 2 bulan pertama publikasi dan menjadi highlighted article di Nature Photonics. Penelitian ini juga dipresentasikan di International Conference on 2D Materials 2024 di Barcelona.</p>

<p><strong>Full Citation:</strong><br>
Rahman, A., et al. "Advanced Optical Characterization of Two-Dimensional Materials Using Multi-Modal Spectroscopy." Nature Photonics 18, 123-135 (2024). DOI: 10.1038/s41566-024-01234-5</p>',
                'featured_image_path' => 'articles/nature-photonics-publication.jpg',
                'author_name' => 'Prof. Dr. Andi Rahman, M.Sc',
                'category' => 'publication',
                'tags' => ['publikasi', 'Nature Photonics', '2D materials', 'spektroskopi', 'international'],
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'published_by' => $admins->random()->id,
                'views_count' => 423,
            ],

            // More News
            [
                'title' => 'Kerjasama Penelitian dengan PT Pertamina dalam Pengembangan Sensor Gas',
                'slug' => 'kerjasama-penelitian-pertamina-pengembangan-sensor-gas',
                'excerpt' => 'Lab GOS menandatangani MoU dengan PT Pertamina untuk mengembangkan sensor gas berbasis optik untuk monitoring keselamatan di fasilitas industri migas.',
                'content' => '<p>Lab GOS resmi menandatangani Memorandum of Understanding (MoU) dengan PT Pertamina (Persero) untuk kerjasama penelitian pengembangan sensor gas berbasis teknologi optik. Penandatanganan dilakukan di Kantor Pusat Pertamina, Jakarta, pada 10 Februari 2024.</p>

<p>Kerjasama ini bertujuan mengembangkan sistem sensor gas yang dapat mendeteksi berbagai jenis gas berbahaya seperti H₂S, CH₄, dan CO dengan sensitivitas tinggi dan response time yang cepat untuk aplikasi monitoring keselamatan di fasilitas industri migas.</p>

<h3>Ruang Lingkup Kerjasama</h3>
<ul>
<li>Penelitian dan pengembangan sensor gas berbasis fiber optik</li>
<li>Pengujian dan validasi di fasilitas Pertamina</li>
<li>Program magang mahasiswa di R&D Center Pertamina</li>
<li>Joint publication dan intellectual property development</li>
<li>Technology transfer dan commercialization</li>
</ul>

<p>Proyek ini direncanakan berlangsung selama 3 tahun dengan total dana penelitian Rp 8,5 miliar. Tim Lab GOS akan dipimpin oleh Dr. Rahmat Hidayat, M.Eng sebagai Principal Investigator.</p>

<blockquote>
<p>"Kerjasama ini merupakan bentuk komitmen Pertamina dalam mendukung pengembangan teknologi dalam negeri. Kami yakin teknologi sensor yang dikembangkan akan berkontribusi signifikan pada peningkatan safety standard industri migas Indonesia," ujar Direktur R&D Pertamina.</p>
</blockquote>',
                'featured_image_path' => 'articles/pertamina-collaboration.jpg',
                'author_name' => 'Tim Lab GOS',
                'category' => 'news',
                'tags' => ['kerjasama', 'Pertamina', 'sensor gas', 'industri', 'penelitian'],
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'published_by' => $superAdmin->id,
                'views_count' => 267,
            ],

            // Draft Article
            [
                'title' => 'Studi Komparatif Metode Preparasi Sampel untuk Analisis FTIR Material Polimer',
                'slug' => 'studi-komparatif-preparasi-sampel-analisis-ftir-polimer',
                'excerpt' => 'Penelitian mendalam tentang pengaruh berbagai metode preparasi sampel terhadap kualitas spektrum FTIR untuk karakterisasi material polimer.',
                'content' => '<p>Draft artikel penelitian tentang optimasi preparasi sampel untuk analisis FTIR material polimer. Artikel ini masih dalam tahap review dan akan dipublikasikan setelah revisi final.</p>

<h2>Outline Penelitian</h2>
<ul>
<li>Perbandingan metode KBr pellet, ATR, dan transmission</li>
<li>Pengaruh thickness sampel terhadap kualitas spektrum</li>
<li>Optimasi kondisi pengukuran</li>
<li>Validasi hasil dengan metode karakterisasi lain</li>
</ul>

<p>Artikel lengkap akan dipublikasikan dalam minggu mendatang setelah proses peer review selesai.</p>',
                'featured_image_path' => 'articles/ftir-polymer-draft.jpg',
                'author_name' => 'Dr. Siti Nurhaliza, M.Sc',
                'category' => 'research',
                'tags' => ['FTIR', 'polimer', 'preparasi sampel', 'karakterisasi'],
                'is_published' => false,
                'published_at' => null,
                'published_by' => null,
                'views_count' => 0,
            ],

            // Announcement
            [
                'title' => 'Maintenance Rutin Peralatan Spektroskopi - Maret 2024',
                'slug' => 'maintenance-rutin-peralatan-spektroskopi-maret-2024',
                'excerpt' => 'Pemberitahuan jadwal maintenance rutin peralatan spektroskopi UV-Vis dan FTIR yang akan dilaksanakan pada awal Maret 2024.',
                'content' => '<h2>Jadwal Maintenance Peralatan</h2>
<p>Dalam rangka menjaga kualitas dan akurasi hasil analisis, Lab GOS akan melaksanakan maintenance rutin untuk peralatan spektroskopi pada jadwal berikut:</p>

<h3>UV-Vis Spectrophotometer (Shimadzu UV-2600i)</h3>
<ul>
<li><strong>Tanggal:</strong> 4-5 Maret 2024</li>
<li><strong>Kegiatan:</strong> Kalibrasi wavelength dan photometric accuracy, penggantian lamp, cleaning optical components</li>
<li><strong>Engineer:</strong> Certified technician dari Shimadzu Indonesia</li>
</ul>

<h3>FTIR Spectrometer (PerkinElmer Spectrum Two)</h3>
<ul>
<li><strong>Tanggal:</strong> 6-7 Maret 2024</li>
<li><strong>Kegiatan:</strong> Cleaning interferometer, penggantian desiccant, kalibrasi frequency, performance verification</li>
<li><strong>Engineer:</strong> Certified technician dari PerkinElmer Indonesia</li>
</ul>

<h3>Dampak Terhadap Layanan</h3>
<p>Selama periode maintenance, peralatan tidak dapat digunakan untuk analisis rutin. Mohon untuk:</p>
<ul>
<li>Mengatur jadwal analisis sebelum atau setelah periode maintenance</li>
<li>Menghubungi lab manager untuk penjadwalan ulang jika diperlukan</li>
<li>Mempertimbangkan alternative method untuk analisis urgent</li>
</ul>

<h3>Sertifikat Kalibrasi</h3>
<p>Setelah maintenance, setiap peralatan akan dilengkapi dengan sertifikat kalibrasi yang dapat digunakan untuk keperluan akreditasi dan quality assurance.</p>

<p><strong>Contact Person:</strong><br>
Ir. Dewi Kusuma, M.T (Technical Manager)<br>
Email: dewi.kusuma@usu.ac.id<br>
Phone: +62 817-8901-2345</p>',
                'featured_image_path' => 'articles/maintenance-announcement.jpg',
                'author_name' => 'Ir. Dewi Kusuma, M.T',
                'category' => 'announcement',
                'tags' => ['maintenance', 'spektroskopi', 'kalibrasi', 'jadwal'],
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'published_by' => $admins->random()->id,
                'views_count' => 89,
            ],
        ];

        foreach ($articles as $article) {
            // Generate slug if not provided
            if (empty($article['slug'])) {
                $article['slug'] = Str::slug($article['title']);
            }

            Article::create($article);
        }

        $this->command->info('✅ Created ' . count($articles) . ' articles (includes drafts and published)');
    }
}
