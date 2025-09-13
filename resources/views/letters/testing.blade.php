<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Pengujian Sampel - {{ $request->request_id }}</title>
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

        /* Testing Parameters Table */
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

        /* Cost Information Box */
        .cost-box {
            border: 2px solid #000;
            padding: 10px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        .cost-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
            text-align: center;
        }

        /* Terms Section */
        .terms-section {
            margin: 15px 0;
        }

        .terms-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .terms-list {
            margin-left: 15px;
        }

        .terms-list li {
            margin-bottom: 3px;
            line-height: 1.3;
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

        .font-bold {
            font-weight: bold;
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
        SURAT IZIN PENGUJIAN SAMPEL
    </div>

    <!-- Main Content -->
    <div class="content-text">
        Saya yang bertanda tangan di bawah ini sebagai Kepala Laboratorium Gelombang, Optik dan Spektroskopi, memberikan izin untuk melakukan pengujian sampel dengan detail sebagai berikut:
    </div>

    <!-- Client Information -->
    <div class="content-text font-bold mt-10">
        INFORMASI KLIEN:
    </div>
    <table class="detail-table">
        <tr>
            <td class="detail-label">Nama Klien</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->client_name }}</td>
        </tr>
        <tr>
            <td class="detail-label">Organisasi</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->client_organization }}</td>
        </tr>
        <tr>
            <td class="detail-label">Email</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->client_email }}</td>
        </tr>
        <tr>
            <td class="detail-label">No. Telepon</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->client_phone }}</td>
        </tr>
        <tr>
            <td class="detail-label">Alamat</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->client_address }}</td>
        </tr>
    </table>

    <!-- Sample Information -->
    <div class="content-text font-bold mt-10">
        INFORMASI SAMPEL:
    </div>
    <table class="detail-table">
        <tr>
            <td class="detail-label">Nama Sampel</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->sample_name }}</td>
        </tr>
        <tr>
            <td class="detail-label">Deskripsi Sampel</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->sample_description }}</td>
        </tr>
        <tr>
            <td class="detail-label">Jumlah Sampel</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->sample_quantity }}</td>
        </tr>
    </table>

    <!-- Testing Information -->
    <div class="content-text font-bold mt-10">
        INFORMASI PENGUJIAN:
    </div>
    <table class="detail-table">
        <tr>
            <td class="detail-label">Jenis Pengujian</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">
                @php
                    $testingTypeLabels = [
                        'uv_vis_spectroscopy' => 'Spektroskopi UV-Vis',
                        'ftir_spectroscopy' => 'Spektroskopi FTIR',
                        'optical_microscopy' => 'Mikroskopi Optik',
                        'custom' => 'Pengujian Khusus'
                    ];
                    $testingTypeLabel = $testingTypeLabels[$request->testing_type] ?? $request->testing_type;
                @endphp
                {{ $testingTypeLabel }}
            </td>
        </tr>
        <tr>
            <td class="detail-label">Jadwal Penyerahan</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">
                @php
                    $deliveryDate = $request->sample_delivery_schedule;
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $deliveryFormatted = $deliveryDate->format('j') . ' ' . $months[(int)$deliveryDate->format('n')] . ' ' . $deliveryDate->format('Y');
                @endphp
                {{ $deliveryFormatted }}
            </td>
        </tr>
        @if($request->estimated_duration)
        <tr>
            <td class="detail-label">Estimasi Durasi</td>
            <td class="detail-separator">:</td>
            <td class="detail-content">{{ $request->estimated_duration }} hari kerja</td>
        </tr>
        @endif
        @if($request->urgent_request)
        <tr>
            <td class="detail-label">Status Urgent</td>
            <td class="detail-separator">:</td>
            <td class="detail-content"><strong>PENGUJIAN MENDESAK</strong></td>
        </tr>
        @endif
    </table>

    <!-- Testing Parameters (if available) -->
    @if($request->testing_parameters && is_array($request->testing_parameters) && count($request->testing_parameters) > 0)
    <div class="content-text font-bold mt-10">
        PARAMETER PENGUJIAN:
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Parameter</th>
                <th style="width: 60%;">Nilai/Spesifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->testing_parameters as $key => $value)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Cost Information (if available) -->
    @if($request->cost)
    <div class="cost-box">
        <div class="cost-title">INFORMASI BIAYA PENGUJIAN</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Biaya Pengujian</td>
                <td class="detail-separator">:</td>
                <td class="detail-content font-bold">Rp {{ number_format($request->cost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="detail-label">Metode Pembayaran</td>
                <td class="detail-separator">:</td>
                <td class="detail-content">Transfer Bank / Tunai</td>
            </tr>
            <tr>
                <td class="detail-label">Jatuh Tempo</td>
                <td class="detail-separator">:</td>
                <td class="detail-content">Sebelum pengujian dimulai</td>
            </tr>
        </tbody>
        </table>
    </div>
    @endif

    <!-- Terms and Conditions -->
    <div class="terms-section">
        <div class="terms-title">SYARAT DAN KETENTUAN:</div>
        <ol class="terms-list">
            <li>Sampel harus diserahkan sesuai jadwal yang telah ditentukan dalam kondisi baik dan sesuai persyaratan pengujian.</li>
            <li>Laboratorium tidak bertanggung jawab atas kerusakan sampel yang disebabkan oleh sifat alami sampel atau prosedur pengujian yang diperlukan.</li>
            <li>Hasil pengujian akan diserahkan dalam bentuk laporan tertulis disertai data pendukung sesuai standar laboratorium.</li>
            <li>Waktu penyelesaian pengujian dihitung dari tanggal penerimaan sampel yang memenuhi persyaratan.</li>
            <li>Sampel akan disimpan maksimal 1 (satu) bulan setelah hasil diserahkan, kecuali ada permintaan khusus dari klien.</li>
            <li>Pembayaran harus dilunasi sebelum pengujian dimulai atau sesuai kesepakatan tertulis.</li>
            <li>Segala perubahan jadwal atau spesifikasi pengujian harus dikomunikasikan minimum 2 hari sebelum jadwal penyerahan sampel.</li>
        </ol>
    </div>

    <div class="content-text mt-10">
        Demikian surat izin pengujian sampel ini dibuat untuk dipergunakan sebagaimana mestinya. Untuk informasi lebih lanjut dapat menghubungi laboratorium melalui kontak yang tersedia.
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
                        Perwakilan {{ $request->client_organization }},<br>&nbsp;
                    </div>
                    <div class="signature-name">
                        {{ $request->client_name }}
                    </div>
                    <div class="signature-nip">
                        Penanggung Jawab Pengujian
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>