<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Piutang — {{ now()->isoFormat('MMMM YYYY') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #fff;
            padding: 28px 32px;
        }

        .clear {
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .muted {
            color: #6b7280;
        }

        /* ══════════════════════════════
           HEADER
        ══════════════════════════════ */
        .header {
            overflow: hidden;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .header-left {
            float: left;
        }

        .header-right {
            float: right;
            text-align: right;
        }

        .logo {
            width: 120px;
            height: auto;
            display: block;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 2px;
        }

        .company-sub {
            font-size: 9.5px;
            color: #6b7280;
            line-height: 1.5;
        }

        .report-title {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 11px;
            color: #4b5563;
            margin-top: 3px;
        }

        /* ══════════════════════════════
           SUMMARY BLOCK
        ══════════════════════════════ */
        .summary-block {
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .summary-col {
            float: left;
            width: 33%;
        }

        .summary-label {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #9ca3af;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .summary-value-small {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
        }

        .badge-piutang {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 4px;
            padding: 2px 10px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ══════════════════════════════
           SECTION TITLE
        ══════════════════════════════ */
        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* ══════════════════════════════
           TABLE
        ══════════════════════════════ */
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table thead tr {
            background: #1e3a5f;
            color: #ffffff;
        }

        .main-table thead th {
            padding: 7px 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
            border: none;
        }

        .main-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .main-table tbody td {
            padding: 8px 8px;
            vertical-align: top;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            line-height: 1.55;
        }

        /* ID badges */
        .id-inv {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 3px;
            padding: 1px 6px;
            font-size: 9px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        .id-book {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            padding: 1px 6px;
            font-size: 9px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        .car-name {
            font-weight: bold;
            color: #1e3a5f;
            margin-top: 3px;
        }

        .nopol {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 3px;
            padding: 1px 5px;
            font-size: 9px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        .date-text {
            color: #4b5563;
            font-size: 9.5px;
            margin-top: 3px;
        }

        /* Durasi badge */
        .durasi-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 10px;
        }

        /* Rincian biaya */
        .biaya-item {
            display: block;
            padding: 2px 0;
            border-bottom: 1px dashed #f1f5f9;
            font-size: 9.5px;
            color: #374151;
        }

        .biaya-item:last-child {
            border-bottom: none;
        }

        .biaya-item.denda {
            color: #b91c1c;
        }

        /* Penyewa */
        .penyewa-name {
            font-weight: bold;
            color: #1e3a5f;
        }

        /* Jumlah */
        .amount-paid {
            color: #15803d;
            font-weight: bold;
        }

        .amount-sisa {
            color: #b91c1c;
            font-weight: bold;
            font-size: 11px;
        }

        /* Grand total row */
        .grand-total-row td {
            background: #1e3a5f !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 11px;
            border: none;
            padding: 8px 8px;
        }

        /* ══════════════════════════════
           SIGNATURE
        ══════════════════════════════ */
        .signature-section {
            float: right;
            width: 170px;
            text-align: center;
            margin-top: 24px;
        }

        .signature-section .hint {
            font-size: 9.5px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .stamp-img {
            height: 72px;
            width: auto;
            display: block;
            margin: 0 auto 6px;
            opacity: .82;
        }

        .sig-line {
            border-top: 1px solid #374151;
            padding-top: 5px;
            font-weight: bold;
            font-size: 10.5px;
            color: #1e3a5f;
        }

        .sig-role {
            font-size: 9.5px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ══════════════════════════════
           FOOTER
        ══════════════════════════════ */
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 32px;
            padding-top: 9px;
            text-align: center;
            font-size: 8.5px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer strong {
            color: #6b7280;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('spt.png');
        $logo = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $stampPath = public_path('stempel.png');
        $stamp = file_exists($stampPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
            : null;
    @endphp

    {{-- ═══════════════════════
     HEADER
═══════════════════════ --}}
    <div class="header">
        <div class="header-left">
            @if ($logo)
                <img src="{{ $logo }}" class="logo">
            @endif
            <div class="company-name">Semeton Pesiar Lombok</div>
            <div class="company-sub">
                Jl. Batu Ringgit No.218, Kota Mataram, NTB<br>
                Telp: 0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com
            </div>
        </div>

        <div class="header-right">
            <div class="report-title">Laporan Piutang</div>
            <div class="report-subtitle">Periode: <strong>{{ now()->isoFormat('MMMM YYYY') }}</strong></div>
            <div class="report-subtitle muted">Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</div>
            <div><span class="badge-piutang">⏳ Belum Lunas</span></div>
        </div>
        <div class="clear"></div>
    </div>

    {{-- ═══════════════════════
     RINGKASAN
═══════════════════════ --}}
    @php
        $grandTotalSisa = 0;
        foreach ($piutang as $inv) {
            $bk = $inv->booking;
            $totalDenda = $bk->penalties->sum('amount');
            $totalTgh = ($bk->estimasi_biaya ?? 0) + ($inv->pickup_dropOff ?? 0) + $totalDenda;
            $totalBayar = $inv->payments->sum('pembayaran');
            $grandTotalSisa += max($totalTgh - $totalBayar, 0);
        }
    @endphp

    <div class="summary-block">
        <div class="summary-col">
            <div class="summary-label">Total Invoice</div>
            <div class="summary-value">{{ $piutang->count() }}</div>
        </div>
        <div class="summary-col">
            <div class="summary-label">Total Piutang</div>
            <div class="summary-value" style="color:#b91c1c">
                Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-col" style="text-align:right">
            <div class="summary-label">Status</div>
            <div class="summary-value-small" style="color:#b91c1c">Belum Lunas</div>
        </div>
        <div class="clear"></div>
    </div>

    {{-- ═══════════════════════
     TABEL DETAIL
═══════════════════════ --}}
    <div class="section-title">Detail Piutang</div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width:29%">Detail Transaksi</th>
                <th style="width:7%" class="text-center">Durasi</th>
                <th style="width:26%">Rincian Biaya</th>
                <th style="width:14%" class="text-center">Penyewa</th>
                <th style="width:12%" class="text-right">Dibayar</th>
                <th style="width:12%" class="text-right">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalSisa = 0; @endphp

            @forelse ($piutang as $invoice)
                @php
                    $booking = $invoice->booking;
                    $car = $booking->car;
                    $totalDenda = $booking->penalties->sum('amount');
                    $totalTagihan = ($booking->estimasi_biaya ?? 0) + ($invoice->pickup_dropOff ?? 0) + $totalDenda;
                    $totalDibayar = $invoice->payments->sum('pembayaran');
                    $sisa = max($totalTagihan - $totalDibayar, 0);
                    $grandTotalSisa += $sisa;
                @endphp

                <tr>
                    {{-- Detail Transaksi --}}
                    <td>
                        <span class="id-inv">#INV{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="id-book">#BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <div class="car-name">
                            {{ $car?->carModel?->name ?? '-' }}
                            <span class="nopol">{{ $car?->nopol ?? '-' }}</span>
                        </div>
                        <div class="date-text">
                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}
                            {{ $booking->waktu_keluar ? '· ' . \Carbon\Carbon::parse($booking->waktu_keluar)->format('H:i') : '' }}
                        </div>
                        <div class="date-text">
                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}
                            {{ $booking->waktu_kembali ? '· ' . \Carbon\Carbon::parse($booking->waktu_kembali)->format('H:i') : '' }}
                            WITA
                        </div>
                    </td>

                    {{-- Durasi --}}
                    <td class="text-center">
                        <span class="durasi-badge">{{ $booking->total_hari }} hr</span>
                    </td>

                    {{-- Rincian Biaya --}}
                    <td>
                        <span class="biaya-item">
                            Sewa: <strong>Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</strong>
                        </span>
                        @if ($invoice->pickup_dropOff > 0)
                            <span class="biaya-item">
                                Antar/Jemput: Rp {{ number_format($invoice->pickup_dropOff, 0, ',', '.') }}
                            </span>
                        @endif
                        @foreach ($booking->penalties as $penalty)
                            <span class="biaya-item denda">
                                {{ ucfirst($penalty->klaim) }}: Rp {{ number_format($penalty->amount, 0, ',', '.') }}
                            </span>
                        @endforeach
                    </td>

                    {{-- Penyewa --}}
                    <td class="text-center">
                        <div class="penyewa-name">{{ $booking->customer->nama }}</div>
                    </td>

                    {{-- Dibayar --}}
                    <td class="text-right">
                        <span class="amount-paid">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                    </td>

                    {{-- Sisa --}}
                    <td class="text-right">
                        <span class="amount-sisa">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center muted" style="padding:20px">
                        Tidak ada data piutang.
                    </td>
                </tr>
            @endforelse

            {{-- Grand total --}}
            <tr class="grand-total-row">
                <td colspan="4" class="text-right">TOTAL SISA PIUTANG</td>
                <td colspan="2" class="text-right">
                    Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ═══════════════════════
     TANDA TANGAN
═══════════════════════ --}}
    <div class="signature-section">
        <div class="hint">Hormat kami,</div>
        @if ($stamp)
            <img src="{{ $stamp }}" class="stamp-img">
        @else
            <div style="height:72px"></div>
        @endif
        <div class="sig-line">ACHMAD MUZAMMIL</div>
        <div class="sig-role">Direktur</div>
    </div>

    <div class="clear"></div>

    {{-- ═══════════════════════
     FOOTER
═══════════════════════ --}}
    <div class="footer">
        <strong>Semeton Pesiar Lombok</strong> &nbsp;·&nbsp;
        Jl. Batu Ringgit No.218, Kota Mataram, NTB &nbsp;·&nbsp;
        Telp: 0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com<br>
        Dokumen ini dibuat otomatis oleh sistem pada {{ now()->format('d F Y H:i') }}
    </div>

</body>

</html>
