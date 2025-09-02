<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Piutang - {{ now()->isoFormat('MMMM YYYY') }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header-section {
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
            float: left;
        }

        .company-details {
            text-align: right;
            float: right;
        }

        .company-details h1 {
            margin: 0;
            font-size: 20px;
            color: #000;
        }

        .summary-section,
        .details-section {
            margin-bottom: 20px;
        }

        .summary-section h2,
        .details-section h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 9px;
            color: #777;
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .clear {
            clear: both;
        }

        ul {
            padding-left: 15px;
            margin: 0;
        }

        li {
            margin-bottom: 2px;
        }

        .signature-section {
            width: 170px;
            margin-top: 50px;
            text-align: center;
            float: right;
        }

        .signature-container {
            position: relative;
            height: 70px;
        }

        .signature-image,
        .stamp-image {
            position: absolute;
            width: 90px;
            height: auto;
            left: 50%;
            margin-left: -120px;
        }

        .signature-image {
            top: 0;
            z-index: 10;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-section">
            @php
                $imagePath = public_path('spt.png');
                $src = file_exists($imagePath)
                    ? 'data:' . mime_content_type($imagePath) . ';base64,' . base64_encode(file_get_contents($imagePath))
                    : '';
            @endphp
            @if ($src)
                <img src="{{ $src }}" alt="Logo" class="logo" />
            @endif
            <div class="company-details">
                <h1>LAPORAN PIUTANG</h1>
                <p><strong>Semeton Pesiar Lombok</strong></p>
                <p>Jl. Batu Ringgit No.218, Kota Mataram</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="summary-section">
            <h2>RINGKASAN</h2>
            <p><strong>Total Piutang:</strong> {{ $piutang->count() }} transaksi</p>
            <p><strong>Periode Cetak:</strong> {{ now()->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Status:</strong> Semua transaksi dalam laporan ini adalah <em>Belum Lunas</em>.</p>
        </div>

        <div class="details-section">
            <h2>DETAIL PIUTANG</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">DETAIL</th>
                        <th style="width: 20%;" class="text-center">TANGGAL</th>
                        <th style="width: 20%;" class="text-center">PENYEWA</th>
                        <th style="width: 20%;" class="text-right">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($piutang as $item)
                        <tr>
                            <td>
                                <strong>INV #{{ $item->invoice->id }}</strong><br>
                                {{ $item->invoice->booking->car->carModel->name }}
                                ({{ $item->invoice->booking->car->nopol }})
                            </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d-m-Y') }}</td>
                            <td class="text-center">{{ $item->invoice->booking->customer->nama }}</td>
                            <td class="text-right">Rp {{ number_format($item->pembayaran, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data piutang.</td>
                        </tr>
                    @endforelse
                    @php
                        $grandTotal = $piutang->sum('pembayaran');
                    @endphp
                    <tr>
                        <td colspan="3" class="text-right"><strong>TOTAL PIUTANG</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signature-section">
            @php
                $stampPath = public_path('stempel.png');
                $stampData = file_exists($stampPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
                    : '';
            @endphp

            <p>Hormat kami,</p>
            <div class="signature-container">
                @if ($stampData)
                    <img src="{{ $stampData }}" alt="Stempel"
                        style="height: 80px; width: auto; opacity: 0.75; display: inline-block; vertical-align: middle;">
                @endif
            </div>
            <p class="signature-name">ACHMAD MUZAMMIL</p>
            <p>CEO Company</p>
        </div>

        <div class="footer">
            <p>Dokumen ini dibuat oleh sistem Semeton Pesiar pada {{ now()->locale('id')->format('d F Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
