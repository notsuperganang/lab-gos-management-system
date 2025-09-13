<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Pemakaian Alat - {{ $request->request_id }}</title>
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

        /* Member and Equipment Tables */
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

        /* Column Widths */
        .col-no {
            width: 8%;
            text-align: center;
        }

        .col-name {
            width: 46%;
        }

        .col-nim {
            width: 46%;
        }

        .col-equipment-name {
            width: 30%;
        }

        .col-spec {
            width: 42%;
        }

        .col-qty {
            width: 20%;
            text-align: center;
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
        SURAT IZIN PEMAKAIAN ALAT
    </div>

    <!-- Main Content -->
    <div class="content-text">
        Saya yang bertanda tangan di bawah ini sebagai Dosen Pembimbing/Pimpinan Instansi dari mahasiswa/staf/peneliti:
    </div>

    <!-- Members Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-name">Nama</th>
                <th class="col-nim">NIM</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Defensive JSON decode for members
                $members = is_array($request->members) ? $request->members : json_decode($request->members ?? '[]', true) ?? [];
            @endphp
            @forelse($members as $index => $member)
            <tr>
                <td>{{ $member['name'] ?? '-' }}</td>
                <td>{{ $member['nim'] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td>-</td>
                <td>-</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="content-text">
        Mohon diberikan izin kepada mahasiswa/staf/peneliti tersebut agar dapat memakai peralatan sebagai berikut:
    </div>

    <!-- Equipment Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-equipment-name">Nama Alat</th>
                <th class="col-spec">Spesifikasi</th>
                <th class="col-qty">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($request->borrowRequestItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}.</td>
                <td>{{ $item->equipment->name ?? '-' }}</td>
                <td>
                    @if($item->equipment)
                        @if($item->equipment->specifications)
                            {{ is_array($item->equipment->specifications) ? implode(', ', $item->equipment->specifications) : $item->equipment->specifications }}
                        @else
                            {{ $item->equipment->model ?? '-' }}
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity_approved ?? $item->quantity_requested ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td class="text-center">1.</td>
                <td>-</td>
                <td>-</td>
                <td class="text-center">-</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="content-text">
        Peralatan tersebut digunakan untuk melaksanakan penelitian di Laboratorium Gelombang, Optik dan Spektroskopi Departemen Fisika Universitas Syiah Kuala pada:
    </div>

    <div class="content-text mt-10">
        <strong>Tanggal/Tahun :</strong>
        @php
            $borrowDate = $request->borrow_date;
            $returnDate = $request->return_date;

            // Format dates in Indonesian
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $borrowFormatted = $borrowDate->format('j') . ' ' . $months[(int)$borrowDate->format('n')] . ' ' . $borrowDate->format('Y');
            $returnFormatted = $returnDate->format('j') . ' ' . $months[(int)$returnDate->format('n')] . ' ' . $returnDate->format('Y');
        @endphp

        @if($borrowDate->format('Y-m-d') === $returnDate->format('Y-m-d'))
            {{ $borrowFormatted }}
        @else
            {{ $borrowFormatted }} – {{ $returnFormatted }}
        @endif
    </div>

    <div class="content-text mt-10">
        Segala sesuatu yang menyebabkan kerugian akan menjadi tanggung jawab mahasiswa yang bersangkutan.
    </div>

    <div class="content-text">
        Demikian surat ini dibuat, untuk dipergunakan sebagaimana mestinya.
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
                        Kepala Laboratorium Gelombang, Optik dan Spektroskopi,
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
                        Pembimbing Penelitian/Pimpinan Instansi,
                    </div>
                    <div class="signature-name">
                        {{ $request->supervisor_name ?? 'Nama Dosen Pembimbing/Pimpinan' }}
                    </div>
                    <div class="signature-nip">
                        NIP. {{ $request->supervisor_nip ?? '' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
