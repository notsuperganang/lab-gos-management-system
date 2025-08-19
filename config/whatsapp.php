<?php

return [
    'admin_templates' => [
        'borrow' => [
            'approval' => [
                'id' => 'borrow_approval',
                'name' => 'Persetujuan Peminjaman',
                'template' => "Halo {NAME},\n\nPermohonan peminjaman Anda (ID {REQUEST_ID}) telah disetujui.\n\nPeralatan: {EQUIPMENT_LIST}\nJadwal Pinjam: {BORROW_DATES}\n\nSilakan ikuti instruksi selanjutnya. Terima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'EQUIPMENT_LIST', 'BORROW_DATES', 'ADMIN_NAME']
            ],
            'more_info' => [
                'id' => 'borrow_more_info',
                'name' => 'Permintaan Info Tambahan',
                'template' => "Halo {NAME},\n\nKami memerlukan informasi tambahan terkait permohonan peminjaman (ID {REQUEST_ID}).\n\nMohon balas pesan ini dengan detail berikut:\n- Keperluan spesifik penggunaan alat\n- Rencana jadwal pemakaian harian\n- Lokasi penggunaan alat\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'ADMIN_NAME']
            ],
            'rejection' => [
                'id' => 'borrow_rejection',
                'name' => 'Penolakan Peminjaman',
                'template' => "Halo {NAME},\n\nMohon maaf, permohonan peminjaman Anda (ID {REQUEST_ID}) tidak dapat disetujui.\n\nAlasan: Alat tidak tersedia pada tanggal yang diminta.\n\nSaran alternatif: Silakan ajukan ulang untuk minggu depan.\n\nTerima kasih atas pengertiannya.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'ADMIN_NAME']
            ],
            'reminder' => [
                'id' => 'borrow_reminder',
                'name' => 'Pengingat Pengembalian',
                'template' => "Halo {NAME},\n\nPengingat: Peminjaman alat (ID {REQUEST_ID}) akan berakhir dalam 1 hari.\n\nTanggal pengembalian: {RETURN_DATE}\nPeralatan: {EQUIPMENT_LIST}\n\nMohon pastikan alat dikembalikan tepat waktu dalam kondisi baik.\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'RETURN_DATE', 'EQUIPMENT_LIST', 'ADMIN_NAME']
            ]
        ],
        'visit' => [
            'approval' => [
                'id' => 'visit_approval',
                'name' => 'Persetujuan Kunjungan',
                'template' => "Halo {NAME},\n\nPermohonan kunjungan (ID {REQUEST_ID}) telah disetujui.\n\nJadwal: {DATE} {TIME}\nAlamat Lab: {LAB_ADDRESS}\nJumlah peserta: {GROUP_SIZE} orang\n\nSilakan hadir sesuai jadwal. Terima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'DATE', 'TIME', 'LAB_ADDRESS', 'GROUP_SIZE', 'ADMIN_NAME']
            ],
            'reschedule' => [
                'id' => 'visit_reschedule',
                'name' => 'Penjadwalan Ulang',
                'template' => "Halo {NAME},\n\nKami mengusulkan penjadwalan ulang untuk kunjungan (ID {REQUEST_ID}).\n\nPilihan waktu baru: {DATE} {TIME}\nAlasan: Jadwal sebelumnya bentrok dengan kegiatan lain\n\nMohon konfirmasi kesediaan Anda. Terima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'DATE', 'TIME', 'ADMIN_NAME']
            ],
            'reminder' => [
                'id' => 'visit_reminder',
                'name' => 'Pengingat Kunjungan',
                'template' => "Halo {NAME},\n\nPengingat: Kunjungan laboratorium (ID {REQUEST_ID}) dijadwalkan besok.\n\nJadwal: {DATE} {TIME}\nAlamat: {LAB_ADDRESS}\nJumlah peserta: {GROUP_SIZE} orang\n\nMohon hadir tepat waktu. Terima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'DATE', 'TIME', 'LAB_ADDRESS', 'GROUP_SIZE', 'ADMIN_NAME']
            ],
            'preparation' => [
                'id' => 'visit_preparation',
                'name' => 'Informasi Persiapan',
                'template' => "Halo {NAME},\n\nInformasi persiapan kunjungan (ID {REQUEST_ID}):\n\nYang perlu dibawa:\n- Kartu identitas untuk semua peserta\n- Alat tulis untuk catatan\n- Baju tertutup dan sepatu\n\nJadwal: {DATE} {TIME}\nTitik kumpul: Lobby gedung lab\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'DATE', 'TIME', 'ADMIN_NAME']
            ]
        ],
        'testing' => [
            'acceptance' => [
                'id' => 'testing_acceptance',
                'name' => 'Penerimaan Pengujian',
                'template' => "Halo {NAME},\n\nPermohonan pengujian sampel (ID {REQUEST_ID}) telah diterima.\n\nJenis: {TESTING_TYPE}\nSampel: {SAMPLE_NAME}\nAntar Sampel: {DATE}\nEstimasi Durasi: {DURATION_DAYS} hari (perkiraan selesai {EST_COMPLETION_DATE})\nPerkiraan Biaya: {COST_IDR}\n\nSilakan antar sampel sesuai jadwal. Terima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'TESTING_TYPE', 'SAMPLE_NAME', 'DATE', 'DURATION_DAYS', 'EST_COMPLETION_DATE', 'COST_IDR', 'ADMIN_NAME']
            ],
            'results_ready' => [
                'id' => 'testing_results_ready',
                'name' => 'Hasil Pengujian Siap',
                'template' => "Halo {NAME},\n\nHasil pengujian untuk sampel (ID {REQUEST_ID}) sudah tersedia.\n\nSampel: {SAMPLE_NAME}\nJenis pengujian: {TESTING_TYPE}\nTanggal selesai: {COMPLETION_DATE}\n\nSilakan cek portal atau hubungi kami untuk pengambilan hasil.\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'SAMPLE_NAME', 'TESTING_TYPE', 'COMPLETION_DATE', 'ADMIN_NAME']
            ],
            'sample_issue' => [
                'id' => 'testing_sample_issue',
                'name' => 'Masalah Sampel',
                'template' => "Halo {NAME},\n\nTerdapat kendala dengan sampel pengujian (ID {REQUEST_ID}).\n\nMasalah: Sampel tidak memenuhi syarat untuk pengujian yang diminta\nSolusi: Perlu persiapan ulang sampel atau metode pengujian alternatif\n\nMohon hubungi kami untuk diskusi lebih lanjut.\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'ADMIN_NAME']
            ],
            'cost_confirmation' => [
                'id' => 'testing_cost_confirmation',
                'name' => 'Konfirmasi Biaya',
                'template' => "Halo {NAME},\n\nKonfirmasi biaya pengujian (ID {REQUEST_ID}):\n\nSampel: {SAMPLE_NAME}\nJenis: {TESTING_TYPE}\nTotal biaya: {COST_IDR}\nMetode pembayaran: Transfer bank\n\nMohon konfirmasi persetujuan biaya sebelum kami memulai pengujian.\n\nTerima kasih.\n\n— {ADMIN_NAME}",
                'placeholders' => ['NAME', 'REQUEST_ID', 'SAMPLE_NAME', 'TESTING_TYPE', 'COST_IDR', 'ADMIN_NAME']
            ]
        ]
    ],

    'default_values' => [
        'LAB_ADDRESS' => 'Laboratorium Gelombang, Optik dan Spektroskopi (GOS), Fakultas MIPA Unsyiah, Jl. Syiah Kuala No. 1, Darussalam, Banda Aceh 23111',
        'ADMIN_NAME' => 'Tim Admin Lab GOS'
    ]
];
