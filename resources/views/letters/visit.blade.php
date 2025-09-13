<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Kunjungan Laboratorium - {{ $request->request_id }}</title>
    <style>
        @page {
            margin: 25mm 20mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .logo-cell {
            width: 90px;
            text-align: left;
        }

        .logo {
            width: 85px;
            height: auto;
        }

        .header-text {
            text-align: center;
            padding-left: 10px;
            padding-right: 10px;
        }

        .university-title {
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .location {
            font-size: 11pt;
            font-weight: normal;
        }

        .spacer-cell {
            width: 90px;
        }

        /* Separator Line */
        .separator {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .separator td {
            border-top: 1px solid #000;
            height: 1px;
            padding: 0;
        }

        /* Document Information Table */
        .document-info {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .document-info td {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 10pt;
            vertical-align: top;
        }

        .document-info .label {
            font-weight: bold;
            width: 20%;
        }

        .document-info .content {
            width: 30%;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* Content Text */
        .content-text {
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        /* Visitor and Visit Details Tables */
        .data-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 10pt;
            vertical-align: top;
        }

        .data-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa;
        }

        /* Detail Information Layout */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .detail-table td {
            padding: 3px 0;
            vertical-align: top;
            line-height: 1.3;
        }

        .detail-label {
            width: 25%;
            font-weight: bold;
        }

        .detail-separator {
            width: 3%;
            text-align: center;
        }

        .detail-content {
            width: 72%;
        }

        /* Equipment Table (Optional) */
        .col-no {
            width: 8%;
            text-align: center;
        }

        .col-equipment-name {
            width: 92%;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 25px;
        }

        .signature-date {
            text-align: right;
            margin-bottom: 15px;
        }

        .signature-approval {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .signature-container {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-container td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
            line-height: 1.3;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .signature-nip {
            font-size: 10pt;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="file://{{ public_path('assets/images/Unsyiah-logo.png') }}" alt="Logo Universitas Syiah Kuala" class="logo">
            </td>
            <td class="header-text">
                <div class="university-title">
                    DEPARTEMEN FISIKA FAKULTAS MIPA UNIVERSITAS SYIAH KUALA
                </div>
                <div class="location">Darussalam–Banda Aceh</div>
            </td>
            <td class="spacer-cell"></td>
        </tr>
    </table>

    <!-- Separator Line -->
    <table class="separator">
        <tr>
            <td></td>
        </tr>
    </table>

    <!-- Document Information Table -->
    <table class="document-info">
        <tr>
            <td class="label">Dokumen Level :</td>
            <td class="content">Standar Operasional Prosedur</td>
            <td class="label">Tanggal Dikeluarkan :</td>
            <td class="content">{{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Judul :</td>
            <td class="content"><strong>LABORATORIUM GELOMBANG, OPTIK DAN SPEKTROSKOPI</strong></td>
            <td class="label">No. Revisi :</td>
            <td class="content">01</td>
        </tr>
        <tr>
            <td class="label">Kode :</td>
            <td class="content">XX</td>
            <td class="label">Area :</td>
            <td class="content">Departemen Fisika</td>
        </tr>
    </table>

    <!-- Document Title -->
    <div class="document-title">
        SURAT IZIN KUNJUNGAN LABORATORIUM
    </div>

    <!-- Main Content -->
    <div class="content-text">
        Saya yang bertanda tangan di bawah ini sebagai Kepala Laboratorium Gelombang, Optik dan Spektroskopi, memberikan izin kepada:
    </div>

    <!-- Visitor Information -->
    <table class="detail-table">
        <tr>
            <td class="detail-label">Nama</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->visitor_name }}</td>
        </tr>
        <tr>
            <td class="detail-label">Email</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->visitor_email }}</td>
        </tr>
        <tr>
            <td class="detail-label">No. Telepon</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->visitor_phone }}</td>
        </tr>
        <tr>
            <td class="detail-label">Institusi</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->institution }}</td>
        </tr>
        <tr>
            <td class="detail-label">Tujuan Kunjungan</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">
                @php
                    $purposeLabels = [
                        'study-visit' => 'Studi Kunjungan',
                        'research' => 'Penelitian',
                        'learning' => 'Pembelajaran',
                        'internship' => 'Magang',
                        'others' => 'Lainnya'
                    ];
                    $purposeLabel = $purposeLabels[$request->visit_purpose] ?? $request->visit_purpose;
                @endphp
                {{ $purposeLabel }}
            </td>
        </tr>
        <tr>
            <td class="detail-label">Jumlah Peserta</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->group_size }} orang</td>
        </tr>
    </table>

    <div class="content-text">
        untuk melakukan kunjungan ke Laboratorium Gelombang, Optik dan Spektroskopi Departemen Fisika Universitas Syiah Kuala pada:
    </div>

    <!-- Visit Schedule Information -->
    <table class="detail-table">
        <tr>
            <td class="detail-label">Tanggal Kunjungan</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">
                @php
                    $visitDate = $request->visit_date;
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $visitFormatted = $visitDate->format('j') . ' ' . $months[(int)$visitDate->format('n')] . ' ' . $visitDate->format('Y');
                @endphp
                {{ $visitFormatted }}
            </td>
        </tr>
        <tr>
            <td class="detail-label">Waktu Kunjungan</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">
                @if($request->start_time && $request->end_time)
                    {{ $request->start_time }} - {{ $request->end_time }} WIB
                @else
                    @php
                        $timeLabels = [
                            'morning' => 'Pagi (08:00 - 12:00)',
                            'afternoon' => 'Siang (13:00 - 17:00)'
                        ];
                        $timeLabel = $timeLabels[$request->visit_time] ?? $request->visit_time;
                    @endphp
                    {{ $timeLabel }}
                @endif
            </td>
        </tr>
        @if($request->purpose_description)
        <tr>
            <td class="detail-label">Deskripsi Kegiatan</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->purpose_description }}</td>
        </tr>
        @endif
        @if($request->special_requirements)
        <tr>
            <td class="detail-label">Kebutuhan Khusus</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->special_requirements }}</td>
        </tr>
        @endif
    </table>

    <!-- Optional Equipment Section -->
    @if($request->equipment_needed && is_array($request->equipment_needed) && count($request->equipment_needed) > 0)
    <div class="content-text mt-10">
        <strong>Peralatan yang diperlukan untuk kegiatan kunjungan:</strong>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-equipment-name">Nama Peralatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->equipment_needed as $index => $equipment)
            <tr>
                <td class="text-center">{{ $index + 1 }}.</td>
                <td>{{ is_array($equipment) ? ($equipment['name'] ?? $equipment) : $equipment }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="content-text mt-10">
        Pengunjung diharapkan untuk mematuhi semua peraturan dan tata tertib yang berlaku di Laboratorium Gelombang, Optik dan Spektroskopi. Segala kerusakan atau kerugian yang terjadi akibat kelalaian pengunjung menjadi tanggung jawab penuh pihak pengunjung.
    </div>

    <div class="content-text">
        Demikian surat izin kunjungan ini dibuat untuk dipergunakan sebagaimana mestinya.
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-date">
            @php
                $today = now();
                $todayFormatted = 'Darussalam, ' . $today->format('j') . ' ' . $months[(int)$today->format('n')] . ' ' . $today->format('Y');
            @endphp
            {{ $todayFormatted }}
        </div>

        <div class="signature-approval">
            Menyetujui,
        </div>

        <table class="signature-container">
            <tr>
                <td>
                    <div class="signature-title">
                        Kepala Laboratorium Gelombang Optik dan Spektroskopi,
                    </div>
                    <div class="signature-name">
                        {{ $labHead['name'] ?? 'Kepala Laboratorium' }}
                    </div>
                    <div class="signature-nip">
                        NIP. {{ $labHead['nip'] ?? '' }}
                    </div>
                </td>
                <td>
                    <div class="signature-title">
                        Perwakilan Institusi {{ $request->institution }},<br>&nbsp;
                    </div>
                    <div class="signature-name">
                        {{ $request->visitor_name }}
                    </div>
                    <div class="signature-nip">
                        Penanggung Jawab Kunjungan
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
