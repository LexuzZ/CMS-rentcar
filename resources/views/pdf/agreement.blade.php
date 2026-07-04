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
            color: #1c1917;
            background: #fff;
            padding: 30px 34px;
            line-height: 1.55;
        }

        .clear {
            clear: both;
        }

        /* ══ TOP ACCENT BAR ══ */
        .accent-bar {
            height: 4px;
            background: #1e3a5f;
            border-radius: 3px;
            margin-bottom: 18px;
        }

        .accent-bar-inner {
            height: 4px;
            width: 60px;
            background: #f59e0b;
            border-radius: 3px;
        }

        /* ══ HEADER ══ */
        .header {
            overflow: hidden;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e7e5e4;
        }

        .header-left {
            float: left;
        }

        .header-right {
            float: right;
            text-align: right;
        }

        .logo {
            width: 110px;
            height: auto;
            display: block;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: .04em;
            margin-bottom: 2px;
        }

        .company-sub {
            font-size: 8.5px;
            color: #a8a29e;
            line-height: 1.6;
        }

        .doc-badge {
            display: inline-block;
            background: #1e3a5f;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .doc-sub {
            font-size: 9px;
            color: #78716c;
            margin-bottom: 8px;
        }

        /* ══ SECTION TITLE ══ */
        .section-title {
            display: table;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #fff;
            background: #1e3a5f;
            padding: 4px 12px;
            border-radius: 4px;
            margin: 18px 0 10px;
        }

        /* ══ RULES ══ */
        .rules {
            border: 1px solid #e7e5e4;
            border-left: 3px solid #1e3a5f;
            border-radius: 0 6px 6px 0;
            padding: 12px 14px 12px 16px;
            background: #faf9f7;
            margin-bottom: 4px;
        }

        .rules ol {
            padding-left: 16px;
        }

        .rules li {
            margin-bottom: 4px;
            font-size: 8.5px;
            color: #44403c;
            line-height: 1.6;
        }

        .rules li::marker {
            color: #1e3a5f;
            font-weight: bold;
        }

        /* ══ TWO-COL DATA LAYOUT ══ */
        .two-col {
            overflow: hidden;
            margin-bottom: 14px;
        }

        .col-half {
            float: left;
            width: 48%;
        }

        .col-half-right {
            float: right;
            width: 48%;
        }

        /* ══ DATA TABLE ══ */
        .data-wrap {
            border: 1px solid #e7e5e4;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table tr:nth-child(even) td {
            background: #faf9f7;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table td {
            padding: 6.5px 11px;
            border-bottom: 1px solid #f0eeed;
            vertical-align: top;
        }

        .td-label {
            width: 36%;
            color: #78716c;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .td-val {
            color: #1c1917;
            font-size: 9.5px;
        }

        .td-sep {
            width: 8px;
            color: #d6d3d1;
            font-size: 9px;
            padding-left: 0;
            padding-right: 0;
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

        /* ══ BADGES ══ */
        .badge {
            padding: 1.5px 7px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid;
        }

        .badge-green {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .badge-red {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .badge-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .badge-amber {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .badge-dark {
            background: #1e3a5f;
            color: #fff;
            border-color: #1e3a5f;
        }

        /* ══ INFO STRIP (meta dokumen) ══ */
        .info-strip {
            overflow: hidden;
            background: #faf9f7;
            border: 1px solid #e7e5e4;
            border-radius: 7px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }

        .info-strip-item {
            float: left;
            margin-right: 24px;
        }

        .info-strip-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #a8a29e;
            display: block;
        }

        .info-strip-val {
            font-size: 9.5px;
            font-weight: bold;
            color: #1c1917;
        }

        /* ══ FOTO ══ */
        .foto-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .foto-table td {
            width: 33%;
            text-align: center;
            padding: 6px;
            vertical-align: top;
        }

        .foto-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #78716c;
            margin-bottom: 5px;
        }

        .foto-frame {
            border: 1px solid #e7e5e4;
            border-radius: 6px;
            overflow: hidden;
            background: #faf9f7;
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
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .sig-block {
            float: left;
            width: 46%;
            text-align: center;
        }

        .sig-block-right {
            float: right;
            width: 46%;
            text-align: center;
        }

        .sig-role {
            font-size: 8.5px;
            color: #78716c;
            margin-bottom: 6px;
        }

        .sig-img-wrap {
            height: 90px;
            border-bottom: 1px solid #374151;
            margin-bottom: 6px;
            display: table;
            width: 100%;
            position: relative;
        }

        .sig-img-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .sig-img-wrap img {
            max-height: 85px;
            width: auto;
            opacity: .85;
        }

        .sig-empty {
            font-size: 8.5px;
            color: #c4bfbb;
            font-style: italic;
        }

        .sig-name {
            font-weight: bold;
            font-size: 9.5px;
            color: #1c1917;
        }

        .sig-title {
            font-size: 8.5px;
            color: #78716c;
            margin-top: 1px;
        }

        /* ══ FOOTER ══ */
        .footer-wrap {
            border-top: 1px solid #e7e5e4;
            margin-top: 24px;
            padding-top: 10px;
            overflow: hidden;
        }

        .footer-left {
            float: left;
            font-size: 8px;
            color: #a8a29e;
            line-height: 1.7;
        }

        .footer-right {
            float: right;
            font-size: 8px;
            color: #a8a29e;
            text-align: right;
            line-height: 1.7;
        }

        .footer-brand {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 8.5px;
        }

        /* divider */
        .divider {
            border: none;
            border-top: 1px solid #f0eeed;
            margin: 10px 0;
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

        $noDoc = 'SPT/' . \Carbon\Carbon::parse($booking->tanggal_keluar)->format('Y/m') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
    @endphp

    {{-- ── Accent bar ── --}}
    <div class="accent-bar">
        <div class="accent-bar-inner"></div>
    </div>

    {{-- ══ HEADER ══ --}}
    <div class="header">
        <div class="header-left">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="logo" alt="Logo">
            @endif
            <div class="company-name">Semeton Pesiar Trans</div>
            <div class="company-sub">
                Jl. Batu Ringgit No.218, Kota Mataram, NTB<br>
                0811-2894-8884 &nbsp;·&nbsp; www.semetonpesiar.com
            </div>
        </div>
        <div class="header-right">
            <div class="doc-badge">Perjanjian Sewa</div>
            <div class="doc-sub">Kendaraan &nbsp;·&nbsp; No. {{ $noDoc }}</div>
            <span class="badge {{ $isLunas ? 'badge-green' : 'badge-red' }}">
                {{ $isLunas ? 'LUNAS' : 'BELUM LUNAS' }}
            </span>
        </div>
        <div class="clear"></div>
    </div>

    {{-- ── Info strip ── --}}
    <div class="info-strip">
        <div class="info-strip-item">
            <span class="info-strip-label">Penyewa</span>
            <span class="info-strip-val">{{ $booking->customer?->nama ?? '—' }}</span>
        </div>
        <div class="info-strip-item">
            <span class="info-strip-label">Kendaraan</span>
            <span class="info-strip-val">
                {{ $booking->car?->carModel?->name }}
                <span class="badge badge-amber">{{ $booking->car?->nopol }}</span>
            </span>
        </div>
        <div class="info-strip-item">
            <span class="info-strip-label">Durasi</span>
            <span class="info-strip-val">
                {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}
                → {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}
                <span class="badge badge-blue">{{ $booking->total_hari }} hari</span>
            </span>
        </div>
        <div class="info-strip-item">
            <span class="info-strip-label">Total</span>
            <span class="info-strip-val">Rp
                {{ number_format($booking->invoice->total_tagihan ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="clear"></div>
    </div>

    {{-- ══ SYARAT & KETENTUAN ══ --}}
    <div class="section-title">Syarat &amp; Ketentuan Sewa</div>
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

    {{-- ══ DATA PENYEWA & RINCIAN — dua kolom ══ --}}
    <div class="section-title">Data Penyewa &amp; Rincian Sewa</div>
    <div class="two-col">

        {{-- Kiri: Data Penyewa --}}
        <div class="col-half">
            <div class="data-wrap">
                <table class="data-table">
                    <tr>
                        <td class="td-label">Nama</td>
                        <td class="td-sep">:</td>
                        <td class="td-val" style="font-weight:bold;">{{ $booking->customer?->nama ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">No. KTP</td>
                        <td class="td-sep">:</td>
                        <td class="td-val" style="font-family:monospace;letter-spacing:.04em;">
                            {{ $booking->customer?->ktp ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Telepon</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">{{ $booking->customer?->no_telp ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Jaminan</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">☑ Motor &nbsp; ☑ STNK</td>
                    </tr>
                    <tr>
                        <td class="td-label">Metode Bayar</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">{{ $methods }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Lokasi Antar</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">{{ $booking->lokasi_pengantaran ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Lokasi Ambil</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">{{ $booking->lokasi_pengembalian ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Kanan: Rincian Biaya --}}
        <div class="col-half-right">
            <div class="data-wrap">
                <table class="data-table">
                    <tr>
                        <td class="td-label">Harga Harian</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">Rp {{ number_format($booking->car->harga_harian ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Estimasi Sewa</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">Rp {{ number_format($booking->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if(($booking->invoice->pickup_dropOff ?? 0) > 0)
                        <tr>
                            <td class="td-label">Antar / Jemput</td>
                            <td class="td-sep">:</td>
                            <td class="td-val">Rp {{ number_format($booking->invoice->pickup_dropOff, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @foreach($booking->penalties as $penalty)
                        <tr>
                            <td class="td-label">{{ ucfirst($penalty->klaim) }}</td>
                            <td class="td-sep">:</td>
                            <td class="td-val" style="color:#991b1b;">Rp {{ number_format($penalty->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="row-total">
                        <td class="td-label">Total Tagihan</td>
                        <td class="td-sep">:</td>
                        <td class="td-val" style="color:#1e3a5f;font-size:10px;">
                            Rp {{ number_format($booking->invoice->total_tagihan ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="{{ $isLunas ? 'row-total' : 'row-danger' }}">
                        <td class="td-label">Sisa Bayar</td>
                        <td class="td-sep">:</td>
                        <td class="td-val" style="font-weight:bold;color:{{ $isLunas ? '#166534' : '#991b1b' }};">
                            Rp {{ number_format($booking->invoice->sisa_pembayaran ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="{{ $isLunas ? 'row-total' : 'row-danger' }}">
                        <td class="td-label">Status</td>
                        <td class="td-sep">:</td>
                        <td class="td-val">
                            <span class="badge {{ $isLunas ? 'badge-green' : 'badge-red' }}">
                                {{ $isLunas ? 'LUNAS' : ' BELUM LUNAS' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="clear"></div>
    </div>

    {{-- ══ FOTO DOKUMENTASI ══ --}}
    @if(!empty($foto_bbm) || !empty($foto_dongkrak) || !empty($foto_ban_serep))
        <div class="section-title">Dokumentasi Serah Terima</div>
        <table class="foto-table">
            <tr>
                @if(!empty($foto_bbm))
                    <td>
                        <div class="foto-label">Indikator BBM</div>
                        <div class="foto-frame"><img src="{{ $foto_bbm }}" alt="Foto BBM"></div>
                    </td>
                @endif
                @if(!empty($foto_dongkrak))
                    <td>
                        <div class="foto-label">Foto Serah Terima</div>
                        <div class="foto-frame"><img src="{{ $foto_dongkrak }}" alt="Foto Serah Terima"></div>
                    </td>
                @endif
                @if(!empty($foto_ban_serep))
                    <td>
                        <div class="foto-label">Ban Serep &amp; Dongkrak</div>
                        <div class="foto-frame"><img src="{{ $foto_ban_serep }}" alt="Foto Ban Serep"></div>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    {{-- ══ TANDA TANGAN ══ --}}
    <div class="section-title">Persetujuan &amp; Tanda Tangan</div>
    <div style="font-size:8.5px;color:#78716c;margin-bottom:12px;line-height:1.6;">
        Dengan menandatangani dokumen ini, penyewa menyatakan telah membaca, memahami,<br>
        dan menyetujui seluruh syarat dan ketentuan yang berlaku.
    </div>

    <div class="sig-section">
        <div class="sig-block">
            <div class="sig-role">Hormat Kami,</div>
            <div class="sig-img-wrap">
                <div class="sig-img-inner">
                    @if($stampData)
                        <img src="{{ $stampData }}" alt="Stempel">
                    @endif
                </div>
            </div>
            <div class="sig-name">ACHMAD MUZAMMIL</div>
            <div class="sig-title">Direktur — PT. Semeton Pesiar Trans</div>
        </div>

        <div class="sig-block sig-block-right">
            <div class="sig-role">Penyewa,</div>
            <div class="sig-img-wrap">
                <div class="sig-img-inner">
                    @if($booking->ttd)
                        <img src="{{ $booking->ttd }}" alt="TTD Penyewa">
                    @else
                        <div class="sig-empty">Tanda tangan belum tersedia</div>
                    @endif
                </div>
            </div>
            <div class="sig-name">({{ $booking->customer?->nama ?? '—' }})</div>
            <div class="sig-title">Penyewa Kendaraan</div>
        </div>

        <div class="clear"></div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="footer-wrap">
        <div class="footer-left">
            <span class="footer-brand">Semeton Pesiar Trans</span><br>
            Jl. Batu Ringgit No.218, Kota Mataram, NTB &nbsp;·&nbsp; 0811-2894-8884
        </div>
        <div class="footer-right">
            No. Dokumen: {{ $noDoc }}<br>
            Diterbitkan: {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WITA
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>
