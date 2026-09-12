<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pemesanan — Semeton Pesiar Lombok</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="sp-body">

    {{-- Navbar --}}
    <header class="sp-nav">
        <div class="sp-nav-inner">
            <div class="sp-logo">
                <div class="sp-logo-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z" />
                        <circle cx="7.5" cy="17.5" r="1.5" />
                        <circle cx="16.5" cy="17.5" r="1.5" />
                    </svg>
                </div>
                <span class="sp-logo-text">Semeton Pesiar Lombok</span>
            </div>
        </div>
    </header>

    <main class="sp-main">
        <div class="sp-page-header">
            <h1 class="sp-page-title">Selesaikan pemesanan</h1>
            <p class="sp-page-sub">Satu langkah lagi — isi data di bawah, kami konfirmasi lewat WhatsApp.</p>
        </div>

        <div class="sp-layout">

            {{-- ───── Kolom Kiri: Form ───── --}}
            <div class="sp-col-form">

                {{-- Autofill banner --}}
                <div class="sp-autofill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span><strong>Pernah pesan di sini?</strong> Isi nomor HP dulu, data Anda terisi sendiri.</span>
                </div>

                <form id="bookingForm" class="sp-form">
                    @csrf

                    {{-- Blok: Data Anda --}}
                    <div class="sp-block">
                        <h2 class="sp-block-title">Data Anda</h2>
                        <p class="sp-block-hint">Nomor luar Indonesia sertakan kode negara.</p>

                        <div class="sp-grid-2">
                            <div class="sp-field">
                                <label class="sp-label" for="no_hp">Nomor WhatsApp</label>
                                <input type="tel" id="no_hp" class="sp-input" placeholder="0812… atau 62812…"
                                    value="{{ $customer->no_telp ?? '' }}" required>
                            </div>
                            <div class="sp-field">
                                <label class="sp-label" for="nama">Nama lengkap</label>
                                <input type="text" id="nama" class="sp-input" placeholder="Sesuai identitas"
                                    value="{{ $customer->nama ?? '' }}" required>
                            </div>
                        </div>

                        <div class="sp-field">
                            <label class="sp-label" for="email">
                                Email
                                <span class="sp-opt">opsional</span>
                            </label>
                            <input type="email" id="email" class="sp-input" placeholder="Untuk kontak cadangan"
                                value="{{ $customer->email ?? '' }}">
                        </div>
                    </div>



                    {{-- Blok: Paket Sewa --}}
                    <div class="sp-block">
                        <h2 class="sp-block-title">Paket Sewa</h2>

                        <div class="sp-field">
                            <label class="sp-label" for="paket">Pilih Paket</label>
                            @php
                                $paketDefault = match($tipeSewa ?? '') {
                                    'lepas_kunci'  => 'Lepas Kunci',
                                    'dengan_sopir' => 'Dengan Driver',
                                    default        => '',
                                };
                                // Hanya tampilkan paket yang relevan dengan tipe_sewa
                                $bisaLepas = in_array('lepas_kunci', $car['tipe_sewa'] ?? []);
                                $bisaSopir = in_array('dengan_sopir', $car['tipe_sewa'] ?? []);
                            @endphp
                            <select id="paket" class="sp-input sp-select" required>
                                <option value="">Pilih paket…</option>
                                @if($bisaLepas)
                                <option value="Lepas Kunci"        {{ $paketDefault === 'Lepas Kunci'        ? 'selected' : '' }}>Lepas Kunci</option>
                                <option value="12 Jam Lepas Kunci" {{ $paketDefault === '12 Jam Lepas Kunci' ? 'selected' : '' }}>12 Jam (Lepas Kunci)</option>
                                @endif
                                @if($bisaSopir)
                                <option value="Dengan Driver"        {{ $paketDefault === 'Dengan Driver'        ? 'selected' : '' }}>Dengan Driver</option>
                                <option value="12 Jam Dengan Driver" {{ $paketDefault === '12 Jam Dengan Driver' ? 'selected' : '' }}>12 Jam (Dengan Driver)</option>
                                @endif
                                <option value="Paket Tour">Paket Tour</option>
                            </select>
                        </div>
                    </div>

                    {{-- Blok: Jadwal --}}
                    <div class="sp-block">
                        <h2 class="sp-block-title">Jadwal</h2>

                        <div class="sp-grid-2">
                            <div class="sp-field">
                                <label class="sp-label" for="tanggal_keluar">Tanggal ambil</label>
                                <input type="date" id="tanggal_keluar" class="sp-input"
                                    value="{{ $tanggalKeluar ?? '' }}" required>
                            </div>
                            <div class="sp-field">
                                <label class="sp-label" for="tanggal_kembali">Tanggal kembali</label>
                                <input type="date" id="tanggal_kembali" class="sp-input"
                                    value="{{ $tanggalKembali ?? '' }}" required>
                            </div>
                        </div>
                        <div class="sp-grid-2">
                            <div class="sp-field">
                                <label class="sp-label" for="jam_keluar">Jam pengantaran</label>
                                <input type="time" id="jam_keluar" class="sp-input" required>
                            </div>
                            <div class="sp-field">
                                <label class="sp-label" for="jam_kembali">Jam pengembalian</label>
                                <input type="time" id="jam_kembali" class="sp-input" required>
                            </div>
                        </div>
                    </div>

                    {{-- Blok: Lokasi --}}
                    <div class="sp-block">
                        <h2 class="sp-block-title">Lokasi</h2>

                        <div class="sp-field">
                            <label class="sp-label" for="lokasi_pengantaran">Lokasi Pengantaran</label>
                            <input type="text" id="lokasi_pengantaran" class="sp-input"
                                placeholder="Hotel / Bandara / Alamat…" required>
                        </div>
                        <div class="sp-field">
                            <label class="sp-label" for="lokasi_pengembalian">Lokasi Pengembalian</label>
                            <input type="text" id="lokasi_pengembalian" class="sp-input"
                                placeholder="Hotel / Bandara / Alamat…" required>
                        </div>
                    </div>

                    {{-- Blok: Sosmed & Catatan --}}
                    <div class="sp-block">
                        <h2 class="sp-block-title">
                            Media Sosial & Catatan
                            <span class="sp-opt">opsional</span>
                        </h2>

                        <div class="sp-grid-2">
                            <div class="sp-field">
                                <label class="sp-label" for="facebook">Facebook</label>
                                <input type="text" id="facebook" class="sp-input" placeholder="Username / link…">
                            </div>
                            <div class="sp-field">
                                <label class="sp-label" for="instagram">Instagram</label>
                                <input type="text" id="instagram" class="sp-input" placeholder="@username…">
                            </div>
                        </div>

                        <div class="sp-field">
                            <label class="sp-label" for="catatan">Catatan Tambahan</label>
                            <textarea id="catatan" rows="3" class="sp-input sp-textarea"
                                placeholder="Contoh: Minta baby seat, jemput di bandara Terminal 2…"></textarea>
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    <div class="sp-attachment">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                        </svg>
                        <div>
                            <p class="sp-att-title">Lampiran via WhatsApp</p>
                            <p class="sp-att-list">Foto tiket pesawat / kapal &nbsp;·&nbsp; Foto voucher hotel</p>
                        </div>
                    </div>

                    <button type="submit" class="sp-submit">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        Kirim Booking via WhatsApp
                    </button>
                </form>
            </div>

            {{-- ───── Kolom Kanan: Ringkasan ───── --}}
            <aside class="sp-col-summary">
                <div class="sp-summary-card">
                    @php
                        $fotoRaw  = $car['foto'] ?? '';
                        // foto bisa berupa URL atau base64
                        $carFoto  = $fotoRaw ?: 'https://placehold.co/320x180/f1f5f9/94a3b8?text=Foto+Kendaraan';
                        $carNama  = ($car['brand'] ?? '') . ' ' . ($car['nama'] ?? '—');
                    @endphp
                    <div class="sp-summary-car-img">
                        <img src="{{ $carFoto }}" alt="{{ $carNama }}" class="sp-car-img"
                            onerror="this.src='https://placehold.co/320x180/f1f5f9/94a3b8?text=Foto+Kendaraan'">
                        <div class="sp-summary-brand-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z" />
                                <circle cx="7.5" cy="17.5" r="1.5" />
                                <circle cx="16.5" cy="17.5" r="1.5" />
                            </svg>
                        </div>
                    </div>

                    <div class="sp-summary-body">
                        <p class="sp-summary-car-name">{{ $carNama }}</p>
                        <p class="sp-summary-location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                <circle cx="12" cy="9" r="2.5" />
                            </svg>
                            Lombok
                        </p>
                        <div class="sp-summary-chips">
                            <span class="sp-summary-chip">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                {{ $car['kapasitas'] ?? '-' }} orang
                            </span>
                            <span class="sp-summary-chip">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                                </svg>
                                {{ strtoupper($car['transmisi'] ?? '-') }}
                            </span>
                        </div>

                        <div class="sp-summary-divider"></div>

                        <div class="sp-summary-rows">
                            <div class="sp-summary-row">
                                <span class="sp-row-label">Tanggal ambil</span>
                                <span class="sp-row-val" id="sum_keluar">—</span>
                            </div>
                            <div class="sp-summary-row">
                                <span class="sp-row-label">Tanggal kembali</span>
                                <span class="sp-row-val" id="sum_kembali">—</span>
                            </div>
                            <div class="sp-summary-row">
                                <span class="sp-row-label">Durasi</span>
                                <span class="sp-row-val" id="sum_durasi">—</span>
                            </div>
                            <div class="sp-summary-row">
                                <span class="sp-row-label">Jenis sewa</span>
                                <span class="sp-row-val" id="sum_paket">—</span>
                            </div>
                            <div class="sp-summary-row">
                                <span class="sp-row-label">Harga/hari</span>
                                <span class="sp-row-val" id="sum_harga_hari">—</span>
                            </div>
                        </div>

                        <p class="sp-summary-note">Dihitung per 24 jam dari jam ambil.</p>

                        <a href="{{ route('home') }}" class="sp-change-date">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                            Ganti kendaraan
                        </a>

                        <div class="sp-summary-divider"></div>

                        <div class="sp-price-row">
                            <span class="sp-price-label">Total estimasi</span>
                            <span class="sp-price-val" id="sum_total">Rp —</span>
                        </div>
                        <p class="sp-summary-note" style="text-align:right;margin-top:-6px">Belum termasuk biaya tambahan</p>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .sp-body {
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', system-ui, sans-serif;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Navbar ── */
        .sp-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .sp-nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sp-logo { display: flex; align-items: center; gap: 9px; }
        .sp-logo-mark {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
        }
        .sp-logo-text { font-size: 13.5px; font-weight: 700; color: #0f172a; }
        .sp-nav-btn {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 18px;
            border-radius: 8px;
            background: #f97316;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }
        .sp-nav-btn:hover { background: #ea580c; }

        /* ── Page header ── */
        .sp-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 24px 60px;
        }
        .sp-page-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        .sp-page-sub {
            font-size: 13.5px;
            color: #64748b;
            margin-top: 4px;
            margin-bottom: 28px;
        }

        /* ── Layout ── */
        .sp-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }
        @media (max-width: 860px) {
            .sp-layout { grid-template-columns: 1fr; }
            .sp-col-summary { order: -1; }
        }

        /* ── Autofill banner ── */
        .sp-autofill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 16px;
            background: #16a34a;
            color: #fff;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .sp-autofill svg { flex-shrink: 0; }

        /* ── Form blocks ── */
        .sp-form { display: flex; flex-direction: column; gap: 16px; }

        .sp-block {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px 22px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .sp-block-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sp-block-hint {
            font-size: 12.5px;
            color: #64748b;
            margin-top: -8px;
        }

        /* ── Fields ── */
        .sp-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 500px) { .sp-grid-2 { grid-template-columns: 1fr; } }

        .sp-field { display: flex; flex-direction: column; gap: 5px; }

        .sp-label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sp-opt {
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            background: #f1f5f9;
            border-radius: 100px;
            padding: 1px 8px;
        }

        .sp-input {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13.5px;
            color: #0f172a;
            background: #fff;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            -webkit-appearance: none;
        }
        .sp-input::placeholder { color: #94a3b8; }
        .sp-input:hover { border-color: #cbd5e1; }
        .sp-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
        }
        .sp-select { cursor: pointer; }
        .sp-textarea { resize: vertical; padding: 10px 13px; }

        /* ── Attachment notice ── */
        .sp-attachment {
            display: flex;
            gap: 10px;
            padding: 13px 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            font-size: 12.5px;
            color: #92400e;
        }
        .sp-attachment svg { flex-shrink: 0; margin-top: 2px; color: #d97706; }
        .sp-att-title { font-weight: 700; margin-bottom: 2px; }
        .sp-att-list { color: #b45309; }

        /* ── Submit ── */
        .sp-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 14px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            width: 100%;
            transition: background .15s, transform .15s;
        }
        .sp-submit:hover { background: #15803d; transform: translateY(-1px); }
        .sp-submit:active { transform: translateY(0); }

        /* ── Summary card ── */
        .sp-summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            position: sticky;
            top: 72px;
        }
        .sp-summary-car-img {
            position: relative;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .sp-car-img {
            width: 100%;
            max-width: 260px;
            height: 140px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .sp-summary-brand-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 30px; height: 30px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            color: #64748b;
        }

        .sp-summary-body { padding: 18px 18px 20px; display: flex; flex-direction: column; gap: 12px; }

        .sp-summary-car-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .sp-summary-location {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12.5px;
            color: #f97316;
            font-weight: 600;
            margin-top: -6px;
        }
        .sp-summary-chips {
            display: flex;
            gap: 6px;
            margin-top: -4px;
        }
        .sp-summary-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 100px;
            background: #f1f5f9;
            color: #475569;
        }

        .sp-summary-divider { height: 1px; background: #f1f5f9; }

        .sp-summary-rows { display: flex; flex-direction: column; gap: 8px; }
        .sp-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
            font-size: 13px;
        }
        .sp-row-label { color: #64748b; }
        .sp-row-val { font-weight: 600; color: #0f172a; text-align: right; }

        .sp-summary-note { font-size: 11.5px; color: #94a3b8; }

        .sp-change-date {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px;
            border-radius: 8px;
            border: 1.5px dashed #fed7aa;
            background: #fff7ed;
            color: #ea580c;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }
        .sp-change-date:hover { background: #ffedd5; }

        .sp-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sp-price-label { font-size: 13px; color: #64748b; }
        .sp-price-val {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
    </style>

    <script>
        // Data harga dari katalog JSON (server-side)
        @php
            $hargaLepas = $car['harga_lepas_kunci'] ?? 0;
            $hargaSopir = $car['harga_dengan_sopir'] ?? 0;
        @endphp
        const HARGA_LEPAS = {{ $hargaLepas }};
        const HARGA_SOPIR = {{ $hargaSopir }};

        function formatRp(n) {
            return 'Rp ' + n.toLocaleString('id-ID');
        }

        function fmtDate(val) {
            if (!val) return '—';
            return new Date(val).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
        }
        function fmtTime(val) { return val ? val + ' WITA' : ''; }

        function updateSummary() {
            const tKeluar  = document.getElementById('tanggal_keluar').value;
            const tKembali = document.getElementById('tanggal_kembali').value;
            const jKeluar  = document.getElementById('jam_keluar').value;
            const jKembali = document.getElementById('jam_kembali').value;
            const paket    = document.getElementById('paket').value;

            document.getElementById('sum_keluar').textContent =
                tKeluar  ? fmtDate(tKeluar)  + (jKeluar  ? ' · ' + fmtTime(jKeluar)  : '') : '—';
            document.getElementById('sum_kembali').textContent =
                tKembali ? fmtDate(tKembali) + (jKembali ? ' · ' + fmtTime(jKembali) : '') : '—';

            let hari = 0;
            if (tKeluar && tKembali) {
                hari = Math.round((new Date(tKembali) - new Date(tKeluar)) / 86400000);
                document.getElementById('sum_durasi').textContent = hari > 0 ? hari + ' hari' : '—';
            } else {
                document.getElementById('sum_durasi').textContent = '—';
            }

            document.getElementById('sum_paket').textContent = paket || '—';

            // Tentukan harga per hari berdasarkan paket
            let hargaPerHari = 0;
            if (paket.includes('Sopir') || paket.includes('Driver')) {
                hargaPerHari = HARGA_SOPIR;
            } else if (paket !== '') {
                hargaPerHari = HARGA_LEPAS;
            }

            // Harga 12 jam = setengah harga
            if (paket.includes('12 Jam')) hargaPerHari = Math.round(hargaPerHari / 2);

            document.getElementById('sum_harga_hari').textContent =
                hargaPerHari > 0 ? formatRp(hargaPerHari) : '—';

            document.getElementById('sum_total').textContent =
                (hargaPerHari > 0 && hari > 0) ? formatRp(hargaPerHari * hari) : 'Rp —';
        }

        ['tanggal_keluar','tanggal_kembali','jam_keluar','jam_kembali'].forEach(id =>
            document.getElementById(id).addEventListener('change', updateSummary)
        );
        document.getElementById('paket').addEventListener('change', updateSummary);

        // Langsung update saat load
        updateSummary();

        // Form submit → WhatsApp
        document.getElementById('bookingForm').onsubmit = function (e) {
            e.preventDefault();
            const get = id => document.getElementById(id)?.value?.trim() ?? '';
            const nama               = get('nama');
            const no_hp              = get('no_hp');
            const ktp                = "{{ $customer->ktp ?? '' }}";
            const mobilNama          = "{{ ($car['brand'] ?? '') . ' ' . ($car['nama'] ?? '') }}";
            const tanggal_keluar     = get('tanggal_keluar');
            const tanggal_kembali    = get('tanggal_kembali');
            const jam_keluar         = get('jam_keluar');
            const jam_kembali        = get('jam_kembali');
            const paket              = get('paket');
            const lokasi_pengantaran  = get('lokasi_pengantaran');
            const lokasi_pengembalian = get('lokasi_pengembalian');
            const facebook  = get('facebook');
            const instagram = get('instagram');
            const catatan   = get('catatan');

            if (!tanggal_keluar||!tanggal_kembali||!jam_keluar||!jam_kembali||!lokasi_pengantaran||!lokasi_pengembalian||!paket) {
                alert('Mohon lengkapi semua field wajib!'); return;
            }

            const totalEl = document.getElementById('sum_total').textContent;
            const fmt = d => new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});
            const msg = `*--- 🚗 BOOKING RENTAL MOBIL 🚗 ---*\n\n*Nama:* ${nama}\n*NIK:* ${ktp}\n*WhatsApp:* ${no_hp}\n*Facebook:* ${facebook||'-'}\n*Instagram:* ${instagram||'-'}\n\n*DETAIL BOOKING:*\n*Mobil:* ${mobilNama}\n*Tanggal Keluar:* ${fmt(tanggal_keluar)}\n*Tanggal Kembali:* ${fmt(tanggal_kembali)}\n*Jam Antar:* ${jam_keluar} WITA\n*Jam Kembali:* ${jam_kembali} WITA\n*Paket Sewa:* ${paket}\n*Estimasi Total:* ${totalEl}\n*Lokasi Antar:* ${lokasi_pengantaran}\n*Lokasi Pengembalian:* ${lokasi_pengembalian}\n\n*Catatan:*\n${catatan||'-'}\n\n*LAMPIRAN YANG DIPERLUKAN:*\n• Foto tiket pesawat/kapal\n• Foto voucher hotel`;
            window.open('https://wa.me/6281128948884?text=' + encodeURIComponent(msg));
        };
    </script>
</body>
</html>
