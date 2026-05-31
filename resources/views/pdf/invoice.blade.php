<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur #{{ $invoice->id }} — Semeton Pesiar</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            padding: 32px 36px;
        }

        /* ══════════════════════════════════
           WARNA & UTILITAS
        ══════════════════════════════════ */
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .clear       { clear: both; }
        .bold        { font-weight: bold; }
        .muted       { color: #6b7280; }

        /* ══════════════════════════════════
           HEADER
        ══════════════════════════════════ */
        .header {
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 18px;
            margin-bottom: 22px;
            overflow: hidden;
        }

        .header-left  { float: left; }
        .header-right { float: right; text-align: right; }

        .logo {
            width: 120px;
            height: auto;
            display: block;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 2px;
        }

        .company-sub {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .invoice-number {
            font-size: 13px;
            color: #4b5563;
            margin-top: 4px;
        }

        /* Status Lunas / Belum */
        .status-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .status-lunas      { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-belum      { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* ══════════════════════════════════
           META BLOCK (Faktur + Billing)
        ══════════════════════════════════ */
        .meta-block {
            overflow: hidden;
            margin-bottom: 24px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
        }

        .meta-col {
            float: left;
            width: 48%;
        }

        .meta-col-right {
            float: right;
            width: 48%;
            text-align: right;
        }

        .meta-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .meta-row {
            margin-bottom: 3px;
            color: #374151;
            line-height: 1.6;
        }

        .meta-row strong {
            color: #1e3a5f;
        }

        /* ══════════════════════════════════
           TABEL RINCIAN
        ══════════════════════════════════ */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #1e3a5f;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1e3a5f;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead tr {
            background: #1e3a5f;
            color: #fff;
        }

        .items-table thead th {
            padding: 8px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tbody td {
            padding: 9px 10px;
            vertical-align: top;
            color: #374151;
            line-height: 1.5;
        }

        .item-detail {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        .item-detail span {
            margin-right: 8px;
        }

        /* ══════════════════════════════════
           TOTALS + PEMBAYARAN (side by side)
        ══════════════════════════════════ */
        .bottom-section {
            overflow: hidden;
            margin-top: 4px;
        }

        /* Pembayaran — kiri */
        .payment-box {
            float: left;
            width: 48%;
        }

        .bank-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 6px;
        }

        .bank-name {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 11px;
        }

        .bank-detail {
            font-size: 10.5px;
            color: #374151;
            margin-top: 1px;
            font-family: 'Courier New', monospace;
            letter-spacing: .04em;
        }

        .bank-holder {
            font-size: 10px;
            color: #6b7280;
            margin-top: 1px;
        }

        /* Totals — kanan */
        .totals-box {
            float: right;
            width: 46%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .totals-table tr:last-child td { border-bottom: none; }

        .totals-table .total-row td {
            font-weight: bold;
            font-size: 12px;
            color: #1e3a5f;
            padding-top: 8px;
            border-top: 2px solid #1e3a5f;
        }

        .totals-table .sisa-row td {
            font-weight: bold;
            font-size: 13px;
            background: #fef2f2;
            color: #b91c1c;
        }

        .totals-table .sisa-lunas td {
            font-weight: bold;
            font-size: 13px;
            background: #f0fdf4;
            color: #15803d;
        }

        /* ══════════════════════════════════
           TANDA TANGAN
        ══════════════════════════════════ */
        .signature-section {
            float: right;
            width: 170px;
            text-align: center;
            margin-top: 28px;
        }

        .signature-img {
            height: 72px;
            width: auto;
            display: block;
            margin: 0 auto 8px;
            opacity: .85;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 5px;
            font-weight: bold;
            font-size: 11px;
            color: #1e3a5f;
        }

        .signature-role {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ══════════════════════════════════
           FOOTER
        ══════════════════════════════════ */
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 36px;
            padding-top: 10px;
            text-align: center;
            font-size: 9.5px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer strong { color: #6b7280; }
    </style>
</head>
<body>

@php
    $logoPath = public_path('spt.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:' . mime_content_type($logoPath) . ';base64,' . base64_encode(file_get_contents($logoPath))
        : '';

    $stampPath = public_path('stempel.png');
    $stampData = file_exists($stampPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
        : '';

    $booking    = $invoice->booking;
    $customer   = $booking->customer;
    $car        = $booking->car;
    $isLunas    = $invoice->sisa_pembayaran <= 0;

    $hargaPerHari = $booking->total_hari > 0
        ? $booking->estimasi_biaya / $booking->total_hari
        : $booking->estimasi_biaya;
@endphp

{{-- ═══════════════════════════════
     HEADER
═══════════════════════════════ --}}
<div class="header">
    <div class="header-left">
        @if ($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif
        <div class="company-name">Semeton Pesiar Trans</div>
        <div class="company-sub">
            Jl. Batu Ringgit No.218, Kota Mataram, NTB<br>
            Telp: 0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com
        </div>
    </div>

    <div class="header-right">
        <div class="invoice-title">Faktur Sewa</div>
        <div class="invoice-number">No. <strong>#INV{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
        <div class="invoice-number muted">Tgl: {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}</div>
        <div>
            <span class="status-badge {{ $isLunas ? 'status-lunas' : 'status-belum' }}">
                {{ $isLunas ? 'LUNAS' : 'BELUM LUNAS' }}
            </span>
        </div>
    </div>
    <div class="clear"></div>
</div>

{{-- ═══════════════════════════════
     META: FAKTUR + BILLING
═══════════════════════════════ --}}
<div class="meta-block">
    <div class="meta-col">
        <div class="meta-label">Detail Faktur</div>
        <div class="meta-row"><strong>No. Faktur</strong> &nbsp; #INV{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</div>
        <div class="meta-row"><strong>No. Booking</strong> &nbsp; #BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</div>
        <div class="meta-row"><strong>Tanggal</strong> &nbsp; {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}</div>
    </div>

    <div class="meta-col-right">
        <div class="meta-label">Ditagihkan Kepada</div>
        <div class="meta-row bold">{{ $customer->nama }}</div>
        <div class="meta-row">{{ $customer->alamat }}</div>
        <div class="meta-row">{{ $customer->no_telp }}</div>
    </div>
    <div class="clear"></div>
</div>

{{-- ═══════════════════════════════
     RINCIAN SEWA
═══════════════════════════════ --}}
<div class="section-title">Rincian Sewa</div>

<table class="items-table">
    <thead>
        <tr>
            <th style="width:65%">Deskripsi</th>
            <th class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        {{-- Sewa Mobil --}}
        <tr>
            <td>
                <span class="bold">Sewa Mobil</span> —
                {{ $car->carModel->brand->name }} {{ $car->carModel->name }}
                <span class="muted">({{ $car->nopol }})</span>
                <div class="item-detail">
                    <span>Keluar: {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}{{ $booking->waktu_keluar ? ' · ' . \Carbon\Carbon::parse($booking->waktu_keluar)->format('H:i') . ' WITA' : '' }}</span>
                    <span>Kembali: {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}{{ $booking->waktu_kembali ? ' · ' . \Carbon\Carbon::parse($booking->waktu_kembali)->format('H:i') . ' WITA' : '' }}</span>
                </div>
                <div class="item-detail">
                    <span>Durasi: {{ $booking->total_hari }} hari</span>
                    <span>Rp {{ number_format($hargaPerHari, 0, ',', '.') }} / hari</span>
                </div>
            </td>
            <td class="text-right bold">
                Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}
            </td>
        </tr>

        {{-- Antar / Jemput --}}
        @if ($invoice->pickup_dropOff > 0)
        <tr>
            <td>Biaya Antar / Jemput</td>
            <td class="text-right">Rp {{ number_format($invoice->pickup_dropOff, 0, ',', '.') }}</td>
        </tr>
        @endif

        {{-- Denda / Klaim --}}
        @foreach ($booking->penalties as $penalty)
        <tr>
            <td>
                <span class="bold">{{ ucfirst($penalty->klaim) }}</span>
                @if ($penalty->description)
                    <div class="item-detail">{{ $penalty->description }}</div>
                @endif
            </td>
            <td class="text-right">Rp {{ number_format($penalty->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ═══════════════════════════════
     BOTTOM: PEMBAYARAN + TOTALS
═══════════════════════════════ --}}
<div class="bottom-section">

    {{-- Rekening --}}
    <div class="payment-box">
        <div class="section-title" style="margin-bottom:10px">Metode Pembayaran</div>

        <div class="bank-item">
            <div class="bank-name">Bank Mandiri</div>
            <div class="bank-detail">1610 006 892 835</div>
            <div class="bank-holder">a.n. ACHMAD MUZAMMIL</div>
        </div>

        <div class="bank-item">
            <div class="bank-name">Bank BCA</div>
            <div class="bank-detail">2320 418 758</div>
            <div class="bank-holder">a.n. SRI NOVYANA</div>
        </div>

        <div style="font-size:10px;color:#6b7280;margin-top:8px">
            Mohon konfirmasi setelah melakukan pembayaran.<br>
            Terima kasih atas kepercayaan Anda.
        </div>
    </div>

    {{-- Totals --}}
    <div class="totals-box">
        <table class="totals-table">
            <tr>
                <td>Biaya Sewa</td>
                <td class="text-right">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</td>
            </tr>
            @if ($invoice->pickup_dropOff > 0)
            <tr>
                <td>Biaya Antar/Jemput</td>
                <td class="text-right">Rp {{ number_format($invoice->pickup_dropOff, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if ($invoice->total_denda > 0)
            <tr>
                <td>Denda / Klaim</td>
                <td class="text-right">Rp {{ number_format($invoice->total_denda, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Tagihan</td>
                <td class="text-right">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="muted">Total Dibayar</td>
                <td class="text-right muted">Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</td>
            </tr>
            <tr class="{{ $isLunas ? 'sisa-lunas' : 'sisa-row' }}">
                <td>Sisa Pembayaran</td>
                <td class="text-right">Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>
</div>

{{-- ═══════════════════════════════
     TANDA TANGAN
═══════════════════════════════ --}}
<div class="signature-section">
    <div class="muted" style="font-size:10px;margin-bottom:6px">Hormat kami,</div>
    @if ($stampData)
        <img src="{{ $stampData }}" alt="Stempel" class="signature-img">
    @else
        <div style="height:72px;"></div>
    @endif
    <div class="signature-line">ACHMAD MUZAMMIL</div>
    <div class="signature-role">Direktur</div>
</div>

<div class="clear"></div>

{{-- ═══════════════════════════════
     FOOTER
═══════════════════════════════ --}}
<div class="footer">
    <strong>Semeton Pesiar Trans</strong> &nbsp;·&nbsp;
    Jl. Batu Ringgit No.218, Kota Mataram, NTB &nbsp;·&nbsp;
    Telp: 0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com<br>
    Dokumen ini diterbitkan secara digital dan sah tanpa tanda tangan basah.
</div>

</body>
</html>
