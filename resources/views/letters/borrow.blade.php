<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Pemakaian Alat - {{ $request->request_id }}</title>
    <style>
        @page {
            margin: 28mm 22mm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .university-title {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .location {
            font-size: 11pt;
            margin-bottom: 15px;
        }
        
        .document-info {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .document-info td {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 10pt;
        }
        
        .document-info .label {
            font-weight: bold;
            width: 25%;
        }
        
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        .content {
            text-align: justify;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .members-table, .equipment-table {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }
        
        .members-table th, .members-table td,
        .equipment-table th, .equipment-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 10pt;
        }
        
        .members-table th, .equipment-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .equipment-table .number-col {
            width: 8%;
            text-align: center;
        }
        
        .equipment-table .name-col {
            width: 35%;
        }
        
        .equipment-table .spec-col {
            width: 42%;
        }
        
        .equipment-table .qty-col {
            width: 15%;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 30px;
        }
        
        .signature-date {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .signature-container {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            line-height: 1.4;
        }
        
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }
        
        .signature-nip {
            font-size: 10pt;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-15 {
            margin-top: 15px;
        }
        
        .mb-15 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- University Header -->
    <div class="header">
        <img src="{{ public_path('assets/images/Unsyiah-logo.png') }}" alt="Logo Universitas Syiah Kuala" class="logo">
        <div class="university-title">
            DEPARTEMEN FISIKA FAKULTAS MIPA UNIVERSITAS SYIAH KUALA
        </div>
        <div class="location">Darussalam–Banda Aceh</div>
    </div>

    <!-- Document Information Table -->
    <table class="document-info">
        <tr>
            <td class="label">Dokumen Level :</td>
            <td>Standar Operasional Prosedur</td>
        </tr>
        <tr>
            <td class="label">Judul :</td>
            <td><strong>LABORATORIUM GELOMBANG, OPTIK DAN SPEKTROSKOPI</strong></td>
        </tr>
        <tr>
            <td class="label">Kode :</td>
            <td>XX</td>
            <td class="label">Tanggal Dikeluarkan :</td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Area :</td>
            <td>Departemen Fisika</td>
            <td class="label">No. Revisi :</td>
            <td>01</td>
        </tr>
    </table>

    <!-- Document Title -->
    <div class="document-title">
        CONTOH FORM SURAT IZIN PEMAKAIAN ALAT
    </div>

    <!-- Main Content -->
    <div class="content">
        Saya yang bertanda tangan di bawah ini sebagai Dosen Pembimbing/Pimpinan Instansi dari mahasiswa/staf/peneliti:
    </div>

    <!-- Members Table -->
    <table class="members-table">
        <thead>
            <tr>
                <th style="width: 60%;">Nama</th>
                <th style="width: 40%;">NIM</th>
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

    <div class="content">
        Mohon diberikan izin kepada mahasiswa/staf/peneliti tersebut agar dapat memakai peralatan sebagai berikut:
    </div>

    <!-- Equipment Table -->
    <table class="equipment-table">
        <thead>
            <tr>
                <th class="number-col">No.</th>
                <th class="name-col">Nama Alat</th>
                <th class="spec-col">Spesifikasi</th>
                <th class="qty-col">Jumlah</th>
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

    <!-- MODIFIED PARAGRAPH - Removed "dengan judul" and "Waktu :" lines -->
    <div class="content">
        Peralatan tersebut digunakan untuk melaksanakan penelitian di Laboratorium Gelombang, Optik dan Spektroskopi Departemen Fisika Universitas Syiah Kuala pada:
    </div>

    <div class="content mt-15">
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

    <div class="content mt-15">
        Segala sesuatu yang menyebabkan kerugian akan menjadi tanggung jawab mahasiswa yang bersangkutan.
    </div>

    <div class="content">
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

        <div class="text-center mb-15">
            <strong>Menyetujui,</strong>
        </div>

        <div class="signature-container">
            <div class="signature-block">
                <div class="signature-title">
                    Kepala Laboratorium Gelombang, Optik dan Spektroskopi,
                </div>
                <div class="signature-name">
                    Nama Kepala Laboratorium
                </div>
                <div class="signature-nip">
                    NIP.
                </div>
            </div>
            
            <div class="signature-block">
                <div class="signature-title">
                    Pembimbing Penelitian/Pimpinan Instansi,
                </div>
                <div class="signature-name">
                    {{ $request->supervisor_name ?? 'Nama Dosen Pembimbing/Pimpinan' }}
                </div>
                <div class="signature-nip">
                    NIP. {{ $request->supervisor_nip ?? '' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>