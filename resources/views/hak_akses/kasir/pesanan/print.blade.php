<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota #{{ $pesanan->nomor_pesanan }}</title>
    <style>
        /* Hilangkan margin kertas bawaan browser */
        @page {
            size: auto;
            margin: 5mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 72mm; /* Pas untuk kertas thermal 80mm */
            margin: 0 auto;
            color: #000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 2px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .item-row td {
            padding-bottom: 4px;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <strong style="font-size: 14px;">SILAM LAUNDRY</strong><br>
        <span>Sistem Laundry Management</span>
    </div>

    <div class="divider"></div>

    <div style="line-height: 1.4;">
        <strong>No. Nota :</strong> #{{ $pesanan->nomor_pesanan }}<br>
        <strong>Tanggal  :</strong> {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan_dibuat)->format('d/m/Y H:i') }}<br>
        <strong>Pelanggan:</strong> {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}<br>
        <strong>Kasir    :</strong> {{ $pesanan->kasir->nama_user ?? '-' }}
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Layanan</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detail as $d)
            <tr class="item-row">
                <td>
                    {{ $d->layanan->nama_layanan ?? '-' }}<br>
                    <small>@ Rp {{ number_format($d->layanan->harga_satuan ?? 0, 0, ',', '.') }}</small>
                </td>
                <td class="text-center">{{ $d->jumlah_berat }} {{ $d->layanan->satuan ?? 'Kg' }}</td>
                <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="fw-bold">Total Tagihan:</td>
            <td class="text-end fw-bold" style="font-size: 12px;">Rp {{ number_format($pesanan->total_tagihan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Status Bayar:</td>
            <td class="text-end fw-bold">{{ strtoupper($pesanan->status_pembayaran) }}</td>
        </tr>
        <tr>
            <td>Metode Bayar:</td>
            <td class="text-end">{{ $pesanan->metode_pembayaran ?? '-' }}</td>
        </tr>

    @if($pesanan->status_pembayaran === 'lunas' && $pesanan->metode_pembayaran === 'Tunai' && !empty($pesanan->uang_diterima) && $pesanan->uang_diterima > 0)
        <tr>
            <td>Uang Diterima:</td>
            <td class="text-end">Rp {{ number_format($pesanan->uang_diterima, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian:</td>
            <td class="text-end">Rp {{ number_format($pesanan->uang_diterima - $pesanan->total_tagihan, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 10px; line-height: 1.3;">
        <span>Terima kasih atas kepercayaan Anda!</span><br>
        <span>Pengambilan pakaian wajib membawa nota ini.</span>
    </div>

</body>
</html>
