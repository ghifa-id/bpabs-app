<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Riwayat Pembayaran - BPABS</title>
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
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        
        .header h1 {
            font-size: 28px;
            color: #1e40af;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 20px;
            color: #374151;
            margin-bottom: 12px;
        }
        
        .header .subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .info-section {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #2563eb;
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
            border-radius: 6px;
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
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            text-align: center;
        }
        
        .stat-card.paid {
            border-left-color: #059669;
        }
        
        .stat-card.pending {
            border-left-color: #f59e0b;
        }
        
        .stat-card.failed {
            border-left-color: #dc2626;
        }
        
        .stat-card .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .filters-info {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #92400e;
        }
        
        .table-container {
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            background: white;
        }
        
        th {
            background: #f8fafc;
            color: #374151;
            font-weight: 600;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #fafafa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-failed {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-cancelled {
            background: #f3f4f6;
            color: #374151;
        }
        
        .amount {
            font-weight: 600;
            color: #059669;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Print Controls Header -->
    <div class="print-header no-print">
        <div>
            <h3 style="margin: 0; color: #374151;">Preview Cetak - Riwayat Pembayaran</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 13px;">
                Total {{ $pembayaran->count() }} data pembayaran | 
                Periode: @if(request('tanggal_mulai') && request('tanggal_selesai'))
                    {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d M Y') }}
                @else
                    Semua Waktu
                @endif
            </p>
        </div>
        <div class="print-controls">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Cetak
            </button>
            <a href="{{ route('pelanggan.riwayat.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="content">
        <!-- Header -->
        <div class="header">
            <h1>BPABS</h1>
            <h2>Riwayat Pembayaran Pelanggan</h2>
            <div class="subtitle">Badan Pengelola Air Bersih dan Sanitasi</div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tanggal Cetak</div>
                    <div class="info-value">{{ now()->format('d F Y, H:i') }} WIB</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Pelanggan</div>
                    <div class="info-value">{{ Auth::user()->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Data</div>
                    <div class="info-value">{{ $pembayaran->count() }} Pembayaran</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Periode</div>
                    <div class="info-value">
                        @if(request('tanggal_mulai') && request('tanggal_selesai'))
                            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d M Y') }}
                        @else
                            Semua Waktu
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Information -->
        @if(request('status') || request('metode') || request('tahun') || request('bulan') || request('tanggal_mulai') || request('tanggal_selesai') || request('meteran_id'))
        <div class="filters-info">
            <strong>Filter yang Diterapkan:</strong>
            @php
                $activeFilters = [];
                if(request('status')) $activeFilters[] = "Status: " . ucfirst(request('status'));
                if(request('metode')) $activeFilters[] = "Metode: " . ucfirst(request('metode'));
                if(request('tahun')) $activeFilters[] = "Tahun: " . request('tahun');
                if(request('bulan')) $activeFilters[] = "Bulan: " . ($bulanList[request('bulan')] ?? request('bulan'));
                if(request('meteran_id')) {
                    $selectedMeteran = $meteranList->firstWhere('id', request('meteran_id'));
                    $activeFilters[] = "Meteran: " . ($selectedMeteran->nomor_meteran ?? '');
                }
                if(request('tanggal_mulai')) $activeFilters[] = "Dari: " . \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y');
                if(request('tanggal_selesai')) $activeFilters[] = "Sampai: " . \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d/m/Y');
            @endphp
            {{ !empty($activeFilters) ? implode(' | ', $activeFilters) : 'Tidak ada filter' }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Pembayaran</div>
                <div class="value">{{ $totalPembayaran }}</div>
            </div>
            <div class="stat-card paid">
                <div class="label">Total Dibayar</div>
                <div class="value">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card pending">
                <div class="label">Pending</div>
                <div class="value">{{ $pembayaranPending }}</div>
            </div>
            <div class="stat-card failed">
                <div class="label">Ditolak</div>
                <div class="value">{{ $pembayaranDitolak }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Bulan Ini</div>
                <div class="value">Rp {{ number_format($pembayaranBulanIni, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-title">Daftar Riwayat Pembayaran</div>
            
            @if($pembayaran->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 12%">Periode</th>
                            <th style="width: 12%">Meteran</th>
                            <th style="width: 12%">Jumlah</th>
                            <th style="width: 12%">Metode</th>
                            <th style="width: 15%">Tanggal Bayar</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 17%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran as $index => $item)
                            <tr>
                                <td>{{ ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + $index + 1 }}</td>
                                <td>
                                    {{ $item->tagihan->bulan }} {{ $item->tagihan->tahun }}
                                </td>
                                <td>
                                    {{ $item->tagihan->meteran->nomor_meteran }}
                                </td>
                                <td class="amount">
                                    Rp {{ number_format($item->jumlah ?? $item->jumlah_bayar ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($item->metode_pembayaran)
                                        <span class="status-badge 
                                            @if($item->metode_pembayaran == 'tunai') status-paid
                                            @elseif($item->metode_pembayaran == 'transfer') status-pending
                                            @else status-failed @endif">
                                            {{ ucfirst(str_replace(['-', '_'], ' ', $item->metode_pembayaran)) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->tanggal_pembayaran)
                                        {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($item->status == 'lunas')
                                        <span class="status-badge status-paid">Lunas</span>
                                        @if($item->is_verified)
                                            <div class="mt-1">
                                                <span class="status-badge" style="background: #dbeafe; color: #1e40af;">Terverifikasi</span>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <span class="status-badge" style="background: #ffedd5; color: #9a3412;">Belum Verifikasi</span>
                                            </div>
                                        @endif
                                    @elseif($item->status == 'failed')
                                        <span class="status-badge status-failed">Gagal</span>
                                    @elseif($item->status == 'cancelled')
                                        <span class="status-badge status-cancelled">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Summary Row -->
                <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Total Pembayaran yang Ditampilkan: {{ $pembayaran->count() }} item</strong>
                        </div>
                        <div>
                            <strong>Total Nilai: Rp {{ number_format($pembayaran->sum('jumlah_bayar')) }}</strong>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-data">
                    <i class="fas fa-credit-card" style="font-size: 48px; margin-bottom: 15px; color: #d1d5db;"></i>
                    <h3 style="margin-bottom: 8px; color: #374151;">Tidak ada riwayat pembayaran</h3>
                    <p>Tidak ada pembayaran yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>© {{ date('Y') }} BPABS - Sistem Informasi Manajemen Air Bersih</strong></p>
            <p>Laporan dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
            <p>Halaman {{ $pembayaran->currentPage() }} dari {{ $pembayaran->lastPage() }} | Total {{ $pembayaran->total() }} pembayaran</p>
            <p>Total {{ $pembayaran->count() }} pembayaran</p>
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
                if (confirm('Kembali ke halaman riwayat?')) {
                    window.location.href = '{{ route("pelanggan.riwayat.index") }}';
                }
            }
        });
    </script>
</body>
</html>