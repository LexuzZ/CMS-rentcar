<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Perjanjian Sewa — {{ $booking->customer?->nama }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #1a1a2e;
            background: #fff;
            padding: 28px 32px;
            line-height: 1.5;
        }

        .clear {
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .muted {
            color: #6b7280;
        }

        /* ══ HEADER ══ */
        .header {
            overflow: hidden;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 14px;
            margin-bottom: 18px;
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
            margin-bottom: 4px;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 2px;
        }

        .company-sub {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.5;
        }

        .doc-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .doc-sub {
            font-size: 10px;
            color: #4b5563;
            margin-top: 3px;
        }

        /* ══ SECTION TITLE ══ */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 4px;
            margin: 16px 0 10px;
        }

        /* ══ ATURAN ══ */
        .rules {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .rules ol {
            padding-left: 16px;
        }

        .rules li {
            margin-bottom: 4px;
            font-size: 9px;
            color: #374151;
            line-height: 1.55;
        }

        .rules li::marker {
            color: #1e3a5f;
            font-weight: bold;
        }

        /* ══ DATA TABLE ══ */
        .data-wrap {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .data-table .td-label {
            width: 34%;
            color: #6b7280;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .data-table .td-val {
            color: #1e293b;
            font-size: 9.5px;
        }

        /* Highlight rows */
        .row-highlight td {
            background: #eff6ff !important;
        }

        .row-total td {
            background: #f0fdf4 !important;
            font-weight: bold;
        }

        .row-danger td {
            background: #fef2f2 !important;
        }

        /* Badges inline */
        .badge {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: .04em;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-amber {
            background: #fef3c7;
            color: #92400e;
        }

        /* ══ FOTO ══ */
        .foto-section {
            margin-bottom: 16px;
        }

        .foto-table {
            width: 100%;
            border-collapse: collapse;
        }

        .foto-table td {
            width: 33%;
            text-align: center;
            padding: 8px 6px;
            vertical-align: top;
        }

        .foto-label {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .foto-frame {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: #f8fafc;
            padding: 3px;
        }

        .foto-frame img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            display: block;
        }

        /* ══ SIGNATURE ══ */
        .sig-section {
            overflow: hidden;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .sig-block {
            float: left;
            width: 48%;
            text-align: center;
        }

        .sig-block-right {
            float: right;
        }

        .sig-role {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .sig-img-wrap {
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #374151;
            margin-bottom: 5px;
        }

        .sig-img-wrap img {
            max-height: 85px;
            width: auto;
            opacity: .82;
        }

        .sig-empty {
            font-size: 9px;
            color: #94a3b8;
            font-style: italic;
        }

        .sig-name {
            font-weight: bold;
            font-size: 9.5px;
            color: #1e293b;
        }

        .sig-title {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        /* ══ FOOTER ══ */
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 24px;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
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
        $logoSrc = file_exists($logoPath)
            ? 'data:' . mime_content_type($logoPath) . ';base64,' . base64_encode(file_get_contents($logoPath))
            : '';

        $stampPath = public_path('stempel.png');
        $stampData = file_exists($stampPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
            : '';

        $isLunas = ($booking->invoice->sisa_pembayaran ?? 1) <= 0;

        $methods = $booking->invoice?->payments
                ?->pluck('metode_pembayaran')
            ->unique()
            ->map(fn($m) => ucfirst(strtolower($m)))
            ->implode(', ') ?? '—';
    @endphp

    {{-- ═══════════════════ HEADER ═══════════════════ --}}
    <div class="header">
        <div class="header-left">
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" class="logo" alt="Logo">
            @endif
            <div class="company-name">Semeton Pesiar Trans</div>
            <div class="company-sub">
                Jl. Batu Ringgit No.218, Kota Mataram, NTB<br>
                Telp: 0811-2894-8884 · www.semetonpesiar.com
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">Perjanjian Sewa</div>
            <div class="doc-sub">Kendaraan</div>
            <div style="margin-top:6px;">
                <span class="badge {{ $isLunas ? 'badge-green' : 'badge-red' }}">
                    {{ $isLunas ? '✓ LUNAS' : '⏳ BELUM LUNAS' }}
                </span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    {{-- ═══════════════════ ATURAN ═══════════════════ --}}
    <div class="section-title">Syarat & Ketentuan Sewa</div>
    <div class="rules">
        <ol>
            <li>Pembatalan sewa kurang dari 1×24 jam dikenakan biaya penuh rental selama 1 hari sesuai type kendaraan.
            </li>
            <li>Durasi rental adalah 12 jam atau 24 jam.</li>
            <li>Pihak penyewa bersedia dan tidak keberatan bilamana pihak PT. SEMETON PESIAR TRANS melakukan survey,
                verifikasi data dan dokumen kepada instansi terkait, bank, tetangga rumah, ataupun di tempat kerja.</li>
            <li>Calon penyewa wajib menunjukkan KTP asli dan dokumen asli lainnya serta memberikan deposit minimal
                <strong>Rp 2.500.000</strong> (mobil) dan <strong>Rp 1.000.000</strong> (motor). Pengambilan deposit
                hanya melalui transfer bank maksimal 1×24 jam setelah kendaraan diterima kembali.</li>
            <li>Mobil tidak dilengkapi asuransi All-Risk. Apabila terjadi kecelakaan, penyewa wajib membayar biaya
                perbaikan bengkel dan biaya sewa selama di bengkel sebesar <strong>75%</strong> dari harga sewa yang
                disepakati.</li>
            <li>Pembayaran sewa kendaraan penuh wajib maksimal pada saat penyerahan kendaraan.</li>
            <li>Overtime dikenakan denda <strong>20%/jam</strong> dari harga sewa. Kelebihan di atas 3 jam dihitung
                sebagai 1 hari penuh.</li>
            <li>Pengembalian/pengiriman ke kantor gratis. Pengiriman ke bandara: <strong>Rp 100.000</strong> (kendaraan
                pribadi) atau <strong>Rp 75.000</strong> (diantar ke kantor).</li>
            <li>Penyewa <strong>DILARANG</strong> memindahtangankan, menyewakan, menggadaikan, atau menjual kendaraan.
                Pelanggaran dikenakan denda <strong>Rp 100.000.000</strong>.</li>
            <li>Seluruh pembayaran (termasuk deposit) tidak dapat di-refund apabila penyewa terbukti melanggar poin No.
                9.</li>
            <li>Pihak rental berhak mengambil tindakan langsung untuk mengamankan kendaraan tanpa tuntutan hukum dari
                penyewa.</li>
            <li>Pihak rental tidak bertanggung jawab atas kehilangan barang yang tertinggal dalam kendaraan.</li>
            <li>Kendaraan hanya boleh digunakan di <strong>Pulau Lombok</strong>. Overland dikenakan biaya <strong>Rp
                    200.000/hari</strong>.</li>
            <li>Penyewa wajib mengembalikan BBM seperti semula. Kekurangan BBM: <strong>Rp 50.000/bar</strong> atau
                <strong>Rp 10.000/10 km</strong>.</li>
            <li>Kendaraan diserahkan dalam keadaan bersih dan harus dikembalikan bersih. Biaya cuci jika dikembalikan
                kotor: <strong>Rp 25.000</strong>.</li>
            <li>Penyewa wajib foto bersama kendaraan saat serah terima. Seluruh dokumentasi adalah hak rental.</li>
            <li>Kerusakan atau kehilangan kelengkapan kendaraan tanpa seizin PT. SEMETON PESIAR TRANS menjadi tanggung
                jawab penyewa.</li>
        </ol>
    </div>

    {{-- ═══════════════════ DATA PENYEWA ═══════════════════ --}}
    <div class="section-title">Data Penyewa & Detail Sewa</div>
    <div class="data-wrap">
        <table class="data-table">
            <tr>
                <td class="td-label">Nama Penyewa</td>
                <td class="td-val bold">{{ $booking->customer?->nama ?? '—' }}</td>
            </tr>
            <tr>
                <td class="td-label">No. KTP</td>
                <td class="td-val" style="font-family:monospace;letter-spacing:.05em;">
                    {{ $booking->customer?->ktp ?? '—' }}</td>
            </tr>
            <tr>
                <td class="td-label">No. Telepon</td>
                <td class="td-val">{{ $booking->customer?->no_telp ?? '—' }}</td>
            </tr>
            <tr class="row-highlight">
                <td class="td-label">Kendaraan</td>
                <td class="td-val bold">
                    {{ $booking->car?->carModel?->name }}
                    <span class="badge badge-amber" style="margin-left:6px">{{ $booking->car?->nopol }}</span>
                </td>
            </tr>
            <tr>
                <td class="td-label">Periode Sewa</td>
                <td class="td-val">
                    {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}{{ $booking->waktu_keluar ? ' · ' . \Carbon\Carbon::parse($booking->waktu_keluar)->format('H:i') . ' WITA' : '' }}
                    <span class="muted"> → </span>
                    {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}{{ $booking->waktu_kembali ? ' · ' . \Carbon\Carbon::parse($booking->waktu_kembali)->format('H:i') . ' WITA' : '' }}
                    <span class="badge badge-blue" style="margin-left:4px">{{ $booking->total_hari }} hari</span>
                </td>
            </tr>
            <tr>
                <td class="td-label">Lokasi Pengantaran</td>
                <td class="td-val">{{ $booking->lokasi_pengantaran ?? '—' }}</td>
            </tr>
            <tr>
                <td class="td-label">Lokasi Pengembalian</td>
                <td class="td-val">{{ $booking->lokasi_pengembalian ?? '—' }}</td>
            </tr>
            <tr>
                <td class="td-label">Jaminan Sewa</td>
                <td class="td-val">☑ Motor &nbsp;&nbsp; ☑ STNK</td>
            </tr>
            <tr>
                <td class="td-label">Metode Bayar</td>
                <td class="td-val">{{ $methods }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════ RINCIAN BIAYA ═══════════════════ --}}
    <div class="section-title">Rincian Biaya</div>
    <div class="data-wrap">
        <table class="data-table">
            <tr>
                <td class="td-label">Harga Harian</td>
                <td class="td-val">Rp {{ number_format($booking->car->harga_harian ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="td-label">Estimasi Biaya Sewa</td>
                <td class="td-val">Rp {{ number_format($booking->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
            </tr>
            @if (($booking->invoice->pickup_dropOff ?? 0) > 0)
                <tr>
                    <td class="td-label">Biaya Antar / Jemput</td>
                    <td class="td-val">Rp {{ number_format($booking->invoice->pickup_dropOff, 0, ',', '.') }}</td>
                </tr>
            @endif
            @foreach ($booking->penalties as $penalty)
                <tr>
                    <td class="td-label">Klaim: {{ ucfirst($penalty->klaim) }}</td>
                    <td class="td-val" style="color:#b91c1c;">Rp {{ number_format($penalty->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="row-total">
                <td class="td-label">Total Tagihan</td>
                <td class="td-val" style="color:#1e3a5f;font-size:10px;">Rp
                    {{ number_format($booking->invoice->total_tagihan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="{{ $isLunas ? 'row-total' : 'row-danger' }}">
                <td class="td-label">Sisa Pembayaran</td>
                <td class="td-val" style="font-weight:bold;color:{{ $isLunas ? '#15803d' : '#b91c1c' }};">
                    Rp {{ number_format($booking->invoice->sisa_pembayaran ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="{{ $isLunas ? 'row-total' : 'row-danger' }}">
                <td class="td-label">Status Pembayaran</td>
                <td class="td-val">
                    <span class="badge {{ $isLunas ? 'badge-green' : 'badge-red' }}">
                        {{ $isLunas ? '✓ LUNAS' : '⏳ BELUM LUNAS' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════ FOTO DOKUMENTASI ═══════════════════ --}}
    @if (!empty($foto_bbm) || !empty($foto_dongkrak) || !empty($foto_ban_serep))
        <div class="section-title">Dokumentasi Foto Serah Terima</div>
        <table class="foto-table">
            <tr>
                @if (!empty($foto_bbm))
                    <td>
                        <div class="foto-label">Indikator BBM</div>
                        <div class="foto-frame"><img src="{{ $foto_bbm }}" alt="Foto BBM"></div>
                    </td>
                @endif
                @if (!empty($foto_dongkrak))
                    <td>
                        <div class="foto-label">Foto Serah Terima</div>
                        <div class="foto-frame"><img src="{{ $foto_dongkrak }}" alt="Foto Serah Terima"></div>
                    </td>
                @endif
                @if (!empty($foto_ban_serep))
                    <td>
                        <div class="foto-label">Ban Serep & Dongkrak</div>
                        <div class="foto-frame"><img src="{{ $foto_ban_serep }}" alt="Foto Ban Serep"></div>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    {{-- ═══════════════════ TANDA TANGAN ═══════════════════ --}}
    <div class="sig-section">

        <div class="sig-block">
            <div class="sig-role">Hormat Kami,</div>
            <div class="sig-img-wrap">
                @if ($stampData)
                    <img src="{{ $stampData }}" alt="Stempel">
                @endif
            </div>
            <div class="sig-name">ACHMAD MUZAMMIL</div>
            <div class="sig-title">Direktur</div>
        </div>

        <div class="sig-block sig-block-right">
            <div class="sig-role">Penyewa,</div>
            <div class="sig-img-wrap">
                @if ($booking->ttd)
                    <img src="{{ $booking->ttd }}" alt="TTD Penyewa">
                @else
                    <div class="sig-empty">TTD belum tersedia</div>
                @endif
            </div>
            <div class="sig-name">({{ $booking->customer?->nama ?? '—' }})</div>
            <div class="sig-title">Penyewa Kendaraan</div>
        </div>

        <div class="clear"></div>
    </div>

    {{-- ═══════════════════ FOOTER ═══════════════════ --}}
    <div class="footer">
        <strong>Semeton Pesiar Trans</strong> &nbsp;·&nbsp;
        Jl. Batu Ringgit No.218, Kota Mataram, NTB &nbsp;·&nbsp;
        Telp: 0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com<br>
        Dokumen ini diterbitkan secara digital dan sah tanpa tanda tangan basah.
        Dicetak pada {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WITA.
    </div>

</body>

</html>
