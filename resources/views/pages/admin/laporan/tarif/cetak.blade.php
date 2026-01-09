<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Tarif - BPABS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .print-header {
            background: #f8fafc;
            padding: 15px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .print-controls {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        @media print {
            .print-header {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        /* Content styles */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Koperasi Style - CLEANED */
        .header-koperasi {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
        }
        
        .logo-container {
            margin-right: 20px;
            position: relative;
        }
        
        /* Logo styling sesuai app.blade.php - CLEANED */
        .logo-main {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .logo-small {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        
        .logo-mini {
            width: 24px;
            height: 24px;
            object-fit: contain;
            border-radius: 0.375rem;
        }
        
        /* Fallback logo styling */
        .logo-fallback {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Header logo animation */
        .header-logo {
            transition: transform 0.2s ease-in-out;
        }

        .header-logo:hover {
            transform: scale(1.05);
        }
        
        .header-text {
            flex: 1;
            text-align: center;
        }
        
        .header-text h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-address {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #6b7280;
        }
        
        .report-title {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
        }
        
        .report-title h2 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Info Grid - Simplified */
        .info-section {
            background: #f8fafc;
            padding: 20px;
            border: 1px solid #e5e7eb;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: white;
            padding: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .info-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        
        /* Statistics Cards - Clean Style */
        .stats-section {
            margin-bottom: 30px;
        }
        
        .stats-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border: 1px solid #d1d5db;
            text-align: center;
        }
        
        .stat-card .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        
        /* Table Style - Clean and Professional */
        .table-container {
            margin-bottom: 30px;
            border: 1px solid #d1d5db;
        }
        
        .table-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            text-transform: uppercase;
            background: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #d1d5db;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: white;
        }
        
        th {
            background: #f8fafc;
            color: #374151;
            font-weight: 600;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #e5e7eb;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #fafafa;
        }
        
        /* Signature Section - CLEANED */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        
        .signature-box {
            text-align: center;
            min-width: 200px;
            position: relative;
        }
        
        .signature-location {
            font-size: 12px;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .signature-date {
            font-size: 12px;
            color: #374151;
            margin-bottom: 30px;
        }
        
        .signature-title {
            font-size: 12px;
            color: #374151;
            margin-bottom: 60px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .signature-name {
            font-size: 12px;
            color: #374151;
            font-weight: bold;
            border-bottom: 1px solid #374151;
            padding-bottom: 5px;
            margin: 0 10px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }
        
        .footer .flex {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
        
        @media print {
            .footer .flex {
                display: flex !important;
            }
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-style: italic;
        }
        
        /* Filters info - cleaner style */
        .filters-info {
            background: #fff7ed;
            border: 1px solid #f59e0b;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <!-- Print Controls Header -->
    <div class="print-header no-print">
        <div>
            <h3 style="margin: 0; color: #374151;">Preview Cetak - Laporan Tarif</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 13px;">
                Total {{ $tarif->count() }} data tarif | 
                Filter: {{ $search ? "\"$search\"" : 'Semua' }}
            </p>
        </div>
        <div class="print-controls">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Cetak
            </button>
            <a href="{{ route('admin.laporan.tarif') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="content">
        <!-- Header Koperasi Style - CLEANED -->
        <div class="header-koperasi">
            <div class="logo-container">
                <!-- Logo utama dengan fallback seperti app.blade.php -->
                <img src="{{ asset('assets/img/logo.png') }}" 
                     alt="BPABS Logo" 
                     class="logo-main bg-white p-1"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <!-- Fallback icon jika logo tidak ditemukan -->
                <div class="logo-fallback" style="display: none;">
                    <i class="fas fa-tint text-white text-2xl"></i>
                </div>
            </div>
            <div class="header-text">
                <h1>BADAN PENGELOLA AIR BERSIH DAN SANITASI (BPABS)<br>
                NAGARI SUNGAI SARIAK, LUMPO<br>
                KECAMATAN IV JURAI, KAB. PESISIR SELATAN</h1>
                
                <div class="header-address">
                    <span>Jln Sungai Sariak, Lumpo, Kec.IV Jurai</span>
                </div>
            </div>
        </div>

        <!-- Report Title -->
        <div class="report-title">
            <h2>Laporan Data Tarif</h2>
        </div>

        <!-- Filter Information -->
        @if($search || $tanggal_dari || $tanggal_sampai)
        <div class="filters-info">
            <strong>Filter yang Diterapkan:</strong>
            @php
                $activeFilters = [];
                if($search) $activeFilters[] = "Pencarian: \"$search\"";
                if($tanggal_dari) $activeFilters[] = "Dari: " . \Carbon\Carbon::parse($tanggal_dari)->format('d/m/Y');
                if($tanggal_sampai) $activeFilters[] = "Sampai: " . \Carbon\Carbon::parse($tanggal_sampai)->format('d/m/Y');
            @endphp
            {{ !empty($activeFilters) ? implode(' | ', $activeFilters) : 'Tidak ada filter' }}
        </div>
        @endif
        
        <!-- Data Table -->
        <div class="table-container">
            <div class="table-title">Daftar Tarif</div>
            
            @if($tarif->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 25%">Nama Tarif</th>
                            <th style="width: 15%">Harga per m³</th>
                            <th style="width: 35%">Deskripsi</th>
                            <th style="width: 20%">Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tarif as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $item->nama_tarif }}</div>
                                    <div style="font-size: 10px; color: #6b7280;">ID: {{ $item->id }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: bold;">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    @if($item->deskripsi)
                                        {{ $item->deskripsi }}
                                    @else
                                        <div style="color: #6b7280; font-style: italic;">-</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div style="font-size: 10px; color: #6b7280;">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <h3 style="margin-bottom: 8px; color: #374151;">Tidak ada data tarif</h3>
                    <p>Tidak ada tarif yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>

        <!-- Signature Section - CLEANED -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-location">Painan</div>
                <div class="signature-date">{{ now()->format('d F Y') }}</div>
                <div class="signature-title">Administrator</div>
                <div class="signature-name">{{ Auth::user()->name ?? 'Admin' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="flex">
                <img src="{{ asset('assets/img/logo.png') }}" 
                     alt="BPABS Logo" 
                     class="logo-mini"
                     onerror="this.style.display='none';">
                <p><strong>© {{ date('Y') }} BPABS - Sistem Informasi Manajemen Air Bersih</strong></p>
            </div>
            <p>Laporan dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
            <p>Total {{ $tarif->count() }} tarif</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Auto focus on load
        window.onload = function() {
            document.body.focus();
        };

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P to print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Escape to go back
            if (e.key === 'Escape') {
                if (confirm('Kembali ke halaman laporan?')) {
                    window.location.href = '{{ route("admin.laporan.tarif") }}';
                }
            }
        });
    </script>
</body>
</html>