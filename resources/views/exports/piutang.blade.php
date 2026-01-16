<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Piutang - {{ now()->isoFormat('MMMM YYYY') }}</title>

    <style>
        body {
            font-family: Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .container {
            width: 100%;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo {
            width: 140px;
            float: left;
        }

        .company {
            float: right;
            text-align: right;
        }

        .company h1 {
            margin: 0;
            font-size: 18px;
        }

        .clear {
            clear: both;
        }

        h2 {
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        ul {
            padding-left: 15px;
            margin: 0;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #777;
        }

        .signature {
            margin-top: 50px;
            width: 200px;
            float: right;
            text-align: center;
        }

        .signature img {
            height: 80px;
            opacity: .75;
        }

        .signature-name {
            font-weight: bold;
            border-top: 1px solid #333;
            margin-top: 10px;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- HEADER --}}
        <div class="header">
            @php
                $logoPath = public_path('spt.png');
                $logo = file_exists($logoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                    : null;
            @endphp

            @if ($logo)
                <img src="{{ $logo }}" class="logo">
            @endif

            <div class="company">
                <h1>LAPORAN PIUTANG</h1>
                <strong>Semeton Pesiar Lombok</strong><br>
                Jl. Batu Ringgit No.218, Kota Mataram
            </div>

            <div class="clear"></div>
        </div>

        {{-- RINGKASAN --}}
        <h2>Ringkasan</h2>
        <p><strong>Total Invoice:</strong> {{ $piutang->count() }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->isoFormat('D MMMM YYYY') }}</p>
        <p><strong>Status:</strong> <em>Belum Lunas</em></p>

        {{-- DETAIL --}}
        <h2>Detail Piutang</h2>

        <table>
            <thead>
                <tr>
                    <th width="30%">Detail Transaksi</th>
                    <th width="8%" class="text-center">Durasi</th>
                    <th width="25%">Rincian Biaya</th>
                    <th width="12%" class="text-center">Penyewa</th>
                    <th width="12%" class="text-right">Dibayar</th>
                    <th width="13%" class="text-right">Sisa</th>
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
                        <td>
                            <strong>INV #{{ $invoice->id }} / BOOK #{{ $booking->id }}</strong><br>
                            {{ $car?->carModel?->name ?? '-' }} ({{ $car?->nopol ?? '-' }})<br>
                            <small>
                                {{ $booking->tanggal_keluar }} s/d {{ $booking->tanggal_kembali }}<br>
                                {{ $booking->waktu_keluar }} – {{ $booking->waktu_kembali }} WITA
                            </small>
                        </td>

                        <td class="text-center">
                            {{ $booking->total_hari }} hari
                        </td>

                        <td>
                            <ul>
                                <li>Sewa: Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</li>

                                @if ($invoice->pickup_dropOff > 0)
                                    <li>Antar/Jemput: Rp {{ number_format($invoice->pickup_dropOff, 0, ',', '.') }}
                                    </li>
                                @endif

                                @foreach ($booking->penalties as $penalty)
                                    <li>{{ ucfirst($penalty->klaim) }}:
                                        Rp {{ number_format($penalty->amount, 0, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </td>

                        <td class="text-center">
                            {{ $booking->customer->nama }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data piutang.</td>
                    </tr>
                @endforelse

                <tr>
                    <td colspan="4" class="text-right"><strong>TOTAL SISA PIUTANG</strong></td>
                    <td colspan="2" class="text-right">
                        <strong>Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- SIGNATURE --}}
        <div class="signature">
            @php
                $stampPath = public_path('stempel.png');
                $stamp = file_exists($stampPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
                    : null;
            @endphp

            @if ($stamp)
                <img src="{{ $stamp }}">
            @endif

            <div class="signature-name">ACHMAD MUZAMMIL</div>
            CEO Company
        </div>

        <div class="clear"></div>

        <div class="footer">
            Dokumen ini dibuat otomatis oleh sistem pada {{ now()->format('d F Y H:i') }}
        </div>

    </div>
</body>

</html>
