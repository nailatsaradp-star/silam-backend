<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan &amp; Transaksi - Periode {{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        /* Kop Laporan */
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #2C4679;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-kop h2 {
            margin: 0;
            color: #2C4679;
            text-transform: uppercase;
            font-size: 18pt;
            letter-spacing: 1px;
        }
        .header-kop p {
            margin: 3px 0;
            font-size: 10pt;
            color: #555;
        }

        /* Kotak Ringkasan */
        .rekap-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }
        .rekap-box {
            flex: 1;
            border: 1px solid #ddd;
            border-left: 4px solid #2C4679;
            border-radius: 4px;
            padding: 8px 12px;
            background: #fdfdfd;
        }
        .rekap-box.green { border-left-color: #10B981; }
        .rekap-box.red { border-left-color: #EF4444; }
        .rekap-box.yellow { border-left-color: #F59E0B; }
        .rekap-title {
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
        }
        .rekap-value {
            font-size: 12pt;
            font-weight: bold;
            color: #111;
        }

        /* Tabel Transaksi */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 25px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #2C4679;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        /* Badge Status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-lunas { background: #DCFCE7; color: #15803D; }
        .badge-belum { background: #FEE2E2; color: #B91C1C; }

        /* Tanda Tangan */
        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
            width: 220px;
        }
        .ttd-space {
            height: 60px;
        }

        /* Tombol Aksi di Layar Komputer */
        .no-print {
            background: #f1f5f9;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            border: none;
            font-size: 10pt;
        }
        .btn-print { background: #2C4679; color: #fff; }
        .btn-close { background: #64748b; color: #fff; }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    {{-- Tombol Aksi Layar --}}
    <div class="no-print">
        <div>
            <b>Pratinjau Dokumen Cetak A4 (Landscape)</b>
            <span style="color: #64748b; margin-left: 10px;">Total {{ count($laporan) }} transaksi termuat seluruhnya.</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn btn-close">Tutup</button>
        </div>
    </div>

    {{-- KOP LAPORAN --}}
    <div class="header-kop">
        <h2>SILAM LAUNDRY &amp; DRY CLEANING</h2>
        <p>Jasa Laundry Kiloan &amp; Satuan Profesional | Telp/WA: 0812-3456-7890</p>
        <p><b>LAPORAN KEUANGAN &amp; OPERASIONAL TRANSAKSI</b></p>
        <p style="color: #2C4679; font-weight: bold; margin-top: 5px;">
            Periode: {{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}
        </p>
    </div>

    {{-- KOTAK REKAPITULASI LAPORAN --}}
    <div class="rekap-container">
        <div class="rekap-box">
            <div class="rekap-title">Total Transaksi</div>
            <div class="rekap-value">{{ $totalTransaksi }} Nota</div>
        </div>
        <div class="rekap-box green">
            <div class="rekap-title">Total Omset (Lunas)</div>
            <div class="rekap-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="rekap-box red">
            <div class="rekap-title">Piutang (Belum Lunas)</div>
            <div class="rekap-value">Rp {{ number_format($belumLunas, 0, ',', '.') }}</div>
        </div>
        <div class="rekap-box yellow">
            <div class="rekap-title">Sudah Diambil</div>
            <div class="rekap-value">{{ $sudahDiambil }} Pesanan</div>
        </div>
    </div>

    {{-- TABEL DATA TRANSAKSI LENGKAP --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 100px;">Kode Nota</th>
                <th>Nama Pelanggan</th>
                <th>Layanan Cucian</th>
                <th style="width: 90px;" class="text-center">Status Kerja</th>
                <th style="width: 95px;" class="text-center">Pembayaran</th>
                <th style="width: 110px;" class="text-end">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_pesanan_dibuat ?? $row->created_at)->format('d/m/Y') }}</td>
                <td class="fw-bold">#{{ $row->nomor_pesanan ?? ($row->kode_transaksi ?? $row->id_pesanan) }}</td>
                <td>{{ $row->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                <td>
                    @if(isset($row->detail) && $row->detail->isNotEmpty())
                        {{ $row->detail->pluck('layanan.nama_layanan')->filter()->implode(', ') ?: '-' }}
                    @else
                        {{ $row->layanan->nama_layanan ?? '-' }}
                    @endif
                </td>
                <td class="text-center">
                    {{ ucfirst(str_replace('_', ' ', $row->status_kerja ?? '-')) }}
                </td>
                <td class="text-center">
                    @if(($row->status_pembayaran ?? '') === 'lunas')
                        <span class="badge badge-lunas">Lunas</span>
                    @else
                        <span class="badge badge-belum">Belum Lunas</span>
                    @endif
                </td>
                <td class="text-end fw-bold">
                    Rp {{ number_format($row->total_tagihan ?? ($row->total_bayar ?? 0), 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">
                    Tidak ada rekaman transaksi pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="7" class="text-end" style="padding: 8px;">TOTAL AKUMULASI TRANSAKSI:</td>
                <td class="text-end" style="padding: 8px; color: #2C4679; font-size: 10pt;">
                    Rp {{ number_format($laporan->sum('total_tagihan'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN DOKUMEN RESMI --}}
    <div class="ttd-wrapper">
        <div class="ttd-box">
            <p>Dibuat Oleh,<br><b>Petugas / Kasir</b></p>
            <div class="ttd-space"></div>
            <p>( ____________________ )</p>
        </div>
        <div class="ttd-box">
            <p>Dicetak pada: {{ date('d F Y') }}<br>Mengetahui,<br><b>Pemilik Usaha</b></p>
            <div class="ttd-space"></div>
            <p>( ____________________ )</p>
        </div>
    </div>

    {{-- Otomatis membuka dialog print saat tab terbuka --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
