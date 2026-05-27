<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking Kendaraan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
</head>
<body class="fb-body">

    <div class="fb-blob fb-blob--tl"></div>
    <div class="fb-blob fb-blob--br"></div>

    <div class="fb-wrapper">

        {{-- Brand --}}
        <div class="fb-brand">
            <div class="fb-brand-logo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                    <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                </svg>
            </div>
            <span class="fb-brand-name">Semeton Pesiar Lombok</span>
        </div>

        {{-- Card --}}
        <div class="fb-card">
            <div class="fb-card-strip"></div>

            <div class="fb-card-body">

                {{-- Heading --}}
                <div class="fb-heading">
                    <div>
                        <h1 class="fb-title">Form Booking Rental</h1>
                        <p class="fb-subtitle">Lengkapi detail perjalanan Anda</p>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="fb-customer">
                    <div class="fb-customer-avatar">
                        {{ mb_strtoupper(mb_substr($customer->nama, 0, 1)) }}
                    </div>
                    <div class="fb-customer-info">
                        <p class="fb-customer-greeting">Halo, <strong>{{ $customer->nama }}</strong> 👋</p>
                        <p class="fb-customer-nik">NIK: {{ $customer->ktp }}</p>
                    </div>
                    <span class="fb-customer-badge">Terverifikasi</span>
                </div>

                {{-- Form --}}
                <form id="bookingForm" class="fb-form">
                    @csrf

                    {{-- Section: Kendaraan --}}
                    <p class="fb-section-label">Pilihan Kendaraan</p>

                    <div class="fb-field">
                        <label class="fb-label" for="mobil">Pilih Mobil</label>
                        <div class="fb-select-wrap">
                            <span class="fb-select-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                                    <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                                </svg>
                            </span>
                            <select id="mobil" class="fb-select" required>
                                <option value="">Pilih jenis mobil…</option>
                                @foreach ($carModels as $model)
                                    <option value="{{ $model->name }}">{{ $model->brand->name }} — {{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="fb-field">
                        <label class="fb-label" for="paket">Paket Sewa</label>
                        <div class="fb-select-wrap">
                            <span class="fb-select-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/>
                                    <path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                                </svg>
                            </span>
                            <select id="paket" class="fb-select" required>
                                <option value="">Pilih paket…</option>
                                <option value="Lepas Kunci">Lepas Kunci</option>
                                <option value="Dengan Driver">Dengan Driver</option>
                                <option value="12 Jam Lepas Kunci">12 Jam (Lepas Kunci)</option>
                                <option value="12 Jam Dengan Driver">12 Jam (Dengan Driver)</option>
                                <option value="Paket Tour">Paket Tour</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section: Jadwal --}}
                    <p class="fb-section-label" style="margin-top:6px">Jadwal</p>

                    <div class="fb-grid-2">
                        <div class="fb-field">
                            <label class="fb-label" for="tanggal_keluar">Tanggal Keluar</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </span>
                                <input type="date" id="tanggal_keluar" class="fb-input" required>
                            </div>
                        </div>
                        <div class="fb-field">
                            <label class="fb-label" for="tanggal_kembali">Tanggal Kembali</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </span>
                                <input type="date" id="tanggal_kembali" class="fb-input" required>
                            </div>
                        </div>
                    </div>

                    <div class="fb-grid-2">
                        <div class="fb-field">
                            <label class="fb-label" for="jam_keluar">Jam Pengantaran</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </span>
                                <input type="time" id="jam_keluar" class="fb-input" required>
                            </div>
                        </div>
                        <div class="fb-field">
                            <label class="fb-label" for="jam_kembali">Jam Pengembalian</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </span>
                                <input type="time" id="jam_kembali" class="fb-input" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Lokasi --}}
                    <p class="fb-section-label" style="margin-top:6px">Lokasi</p>

                    <div class="fb-field">
                        <label class="fb-label" for="lokasi_pengantaran">Lokasi Pengantaran</label>
                        <div class="fb-input-wrap">
                            <span class="fb-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
                                </svg>
                            </span>
                            <input type="text" id="lokasi_pengantaran" class="fb-input" placeholder="Hotel / Bandara / Alamat…" required>
                        </div>
                    </div>

                    <div class="fb-field">
                        <label class="fb-label" for="lokasi_pengembalian">Lokasi Pengembalian</label>
                        <div class="fb-input-wrap">
                            <span class="fb-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
                                </svg>
                            </span>
                            <input type="text" id="lokasi_pengembalian" class="fb-input" placeholder="Hotel / Bandara / Alamat…" required>
                        </div>
                    </div>

                    {{-- Section: Sosmed (opsional) --}}
                    <p class="fb-section-label" style="margin-top:6px">
                        Media Sosial
                        <span class="fb-optional">opsional</span>
                    </p>

                    <div class="fb-grid-2">
                        <div class="fb-field">
                            <label class="fb-label" for="facebook">Facebook</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon" style="color:#1877f2;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M22.675 0H1.325A1.326 1.326 0 000 1.325v21.351A1.326 1.326 0 001.325 24h11.497v-9.294H9.692V11.01h3.13V8.41c0-3.1 1.894-4.788 4.66-4.788 1.325 0 2.463.099 2.794.143v3.24h-1.917c-1.505 0-1.797.716-1.797 1.767v2.317h3.59l-.467 3.697h-3.123V24h6.116A1.326 1.326 0 0024 22.676V1.325A1.326 1.326 0 0022.675 0z"/>
                                    </svg>
                                </span>
                                <input type="text" id="facebook" class="fb-input" placeholder="Username / link…">
                            </div>
                        </div>
                        <div class="fb-field">
                            <label class="fb-label" for="instagram">Instagram</label>
                            <div class="fb-input-wrap">
                                <span class="fb-input-icon" style="color:#e1306c;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2.2c3.2 0 3.584.012 4.85.07 1.17.056 1.96.24 2.418.404a4.9 4.9 0 011.772 1.153 4.9 4.9 0 011.153 1.772c.164.458.348 1.248.404 2.418.058 1.266.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.056 1.17-.24 1.96-.404 2.418a4.9 4.9 0 01-1.153 1.772 4.9 4.9 0 01-1.772 1.153c-.458.164-1.248.348-2.418.404-1.266.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.056-1.96-.24-2.418-.404a4.9 4.9 0 01-1.772-1.153 4.9 4.9 0 01-1.153-1.772c-.164-.458-.348-1.248-.404-2.418C2.212 15.584 2.2 15.2 2.2 12s.012-3.584.07-4.85c.056-1.17.24-1.96.404-2.418a4.9 4.9 0 011.153-1.772A4.9 4.9 0 015.6 1.78c.458-.164 1.248-.348 2.418-.404C9.284 1.312 9.668 1.3 12 1.3zm0 1.8c-3.17 0-3.548.012-4.797.07-1.03.048-1.59.22-1.96.366-.493.191-.845.42-1.215.79a3.1 3.1 0 00-.79 1.215c-.146.37-.318.93-.366 1.96-.058 1.25-.07 1.627-.07 4.797s.012 3.548.07 4.797c.048 1.03.22 1.59.366 1.96.191.493.42.845.79 1.215.37.37.722.599 1.215.79.37.146.93.318 1.96.366 1.25.058 1.627.07 4.797.07s3.548-.012 4.797-.07c1.03-.048 1.59-.22 1.96-.366.493-.191.845-.42 1.215-.79.37-.37.599-.722.79-1.215.146-.37.318-.93.366-1.96.058-1.25.07-1.627.07-4.797s-.012-3.548-.07-4.797c-.048-1.03-.22-1.59-.366-1.96a3.1 3.1 0 00-.79-1.215 3.1 3.1 0 00-1.215-.79c-.37-.146-.93-.318-1.96-.366C15.548 4.012 15.17 4 12 4zM12 7.2a4.8 4.8 0 110 9.6 4.8 4.8 0 010-9.6zm0 1.8a3 3 0 100 6 3 3 0 000-6zm5.85-1.95a1.12 1.12 0 110 2.24 1.12 1.12 0 010-2.24z"/>
                                    </svg>
                                </span>
                                <input type="text" id="instagram" class="fb-input" placeholder="@username…">
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="fb-field">
                        <label class="fb-label" for="catatan">
                            Catatan Tambahan
                            <span class="fb-optional">opsional</span>
                        </label>
                        <textarea id="catatan" rows="3" class="fb-textarea"
                            placeholder="Contoh: Minta baby seat, jemput di bandara Terminal 2…"></textarea>
                    </div>

                    {{-- Lampiran info box --}}
                    <div class="fb-infobox">
                        <div class="fb-infobox-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                            </svg>
                        </div>
                        <div>
                            <p class="fb-infobox-title">Lampiran yang Diperlukan</p>
                            <p class="fb-infobox-text">Mohon sertakan via WhatsApp:</p>
                            <div class="fb-infobox-items">
                                <span class="fb-infobox-item">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Foto tiket pesawat / kapal
                                </span>
                                <span class="fb-infobox-item">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Foto voucher hotel
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="fb-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Kirim Booking via WhatsApp
                    </button>
                </form>

            </div>
        </div>
    </div>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .fb-body {
            min-height: 100vh; background: #0f172a;
            padding: 40px 16px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .fb-blob {
            position: fixed; border-radius: 50%; pointer-events: none; z-index: 0;
        }
        .fb-blob--tl { width:500px;height:500px;top:-160px;left:-120px;
            background:radial-gradient(circle,rgba(99,102,241,.28) 0%,transparent 70%); }
        .fb-blob--br { width:420px;height:420px;bottom:-120px;right:-80px;
            background:radial-gradient(circle,rgba(16,185,129,.2) 0%,transparent 70%); }

        .fb-wrapper {
            width:100%;max-width:540px;margin:0 auto;
            position:relative;z-index:1;
            animation:fb-rise .42s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fb-rise {
            from{opacity:0;transform:translateY(28px)}
            to{opacity:1;transform:translateY(0)}
        }

        /* Brand */
        .fb-brand { display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:22px; }
        .fb-brand-logo {
            width:40px;height:40px;border-radius:11px;
            background:linear-gradient(135deg,#6366f1,#4f46e5);
            display:flex;align-items:center;justify-content:center;
            color:#fff;box-shadow:0 4px 14px rgba(99,102,241,.4);
        }
        .fb-brand-name { font-size:14px;font-weight:700;color:#e2e8f0; }

        /* Card */
        .fb-card {
            background:#fff;border-radius:20px;overflow:hidden;
            box-shadow:0 0 0 1px rgba(255,255,255,.06),0 20px 50px rgba(0,0,0,.45),0 2px 8px rgba(0,0,0,.2);
        }
        .fb-card-strip { height:4px;background:linear-gradient(90deg,#6366f1,#8b5cf6,#06b6d4); }
        .fb-card-body { padding:28px 28px 24px;display:flex;flex-direction:column;gap:16px; }
        @media(max-width:480px){ .fb-card-body{padding:20px 18px 22px;} }

        /* Heading */
        .fb-heading { display:flex;align-items:center;justify-content:space-between; }
        .fb-title { font-size:20px;font-weight:800;color:#0f172a;letter-spacing:-.02em; }
        .fb-subtitle { font-size:13px;color:#64748b;margin-top:3px; }

        /* Customer */
        .fb-customer {
            display:flex;align-items:center;gap:12px;
            padding:13px 16px;border-radius:12px;
            background:linear-gradient(135deg,#eff6ff,#dbeafe);
            border:1px solid #bfdbfe;
        }
        .fb-customer-avatar {
            width:40px;height:40px;border-radius:10px;flex-shrink:0;
            background:linear-gradient(135deg,#6366f1,#4f46e5);
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:16px;font-weight:800;
            box-shadow:0 3px 10px rgba(99,102,241,.3);
        }
        .fb-customer-info { flex:1;min-width:0; }
        .fb-customer-greeting { font-size:13.5px;color:#1e3a5f; }
        .fb-customer-greeting strong { font-weight:700; }
        .fb-customer-nik { font-size:11px;color:#3b82f6;margin-top:2px;font-family:ui-monospace,monospace;letter-spacing:.04em; }
        .fb-customer-badge {
            font-size:10px;font-weight:700;padding:3px 9px;border-radius:100px;
            background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;white-space:nowrap;
        }

        /* Form */
        .fb-form { display:flex;flex-direction:column;gap:12px; }

        .fb-section-label {
            font-size:11px;font-weight:700;letter-spacing:.07em;
            color:#94a3b8;text-transform:uppercase;
            display:flex;align-items:center;gap:8px;
            margin-bottom:-4px;
        }
        .fb-optional {
            font-size:10px;font-weight:600;letter-spacing:.03em;
            text-transform:none;color:#64748b;
            background:#f1f5f9;border-radius:100px;padding:1px 8px;
        }

        .fb-field { display:flex;flex-direction:column;gap:5px; }
        .fb-label { font-size:13px;font-weight:600;color:#374151; }
        .fb-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
        @media(max-width:420px){ .fb-grid-2{grid-template-columns:1fr;} }

        /* Input */
        .fb-input-wrap { position:relative; }
        .fb-input-icon {
            position:absolute;left:11px;top:50%;transform:translateY(-50%);
            color:#94a3b8;display:flex;pointer-events:none;
        }
        .fb-input, .fb-textarea, .fb-select {
            width:100%;padding:11px 12px 11px 36px;
            border:1.5px solid #e2e8f0;border-radius:10px;
            font-size:13.5px;color:#0f172a;background:#f8fafc;
            font-family:inherit;
            transition:border-color .18s,box-shadow .18s,background .18s;
            outline:none;-webkit-appearance:none;
        }
        .fb-input:hover,.fb-textarea:hover { border-color:#cbd5e1;background:#fff; }
        .fb-input:focus,.fb-textarea:focus {
            border-color:#6366f1;background:#fff;
            box-shadow:0 0 0 3px rgba(99,102,241,.12);
        }
        .fb-input::placeholder,.fb-textarea::placeholder { color:#94a3b8; }
        .fb-textarea { padding-left:12px;resize:none;padding-top:10px; }

        /* Select */
        .fb-select-wrap { position:relative; }
        .fb-select-icon {
            position:absolute;left:11px;top:50%;transform:translateY(-50%);
            color:#94a3b8;display:flex;pointer-events:none;z-index:1;
        }
        .fb-select { cursor:pointer; }
        .fb-select:hover { border-color:#cbd5e1;background:#fff; }
        .fb-select:focus { border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.12); }

        /* Info box */
        .fb-infobox {
            display:flex;gap:12px;
            padding:14px 16px;border-radius:12px;
            background:linear-gradient(135deg,#fffbeb,#fef3c7);
            border:1px solid #fde68a;
        }
        .fb-infobox-icon {
            width:32px;height:32px;border-radius:8px;flex-shrink:0;
            background:#fff;border:1px solid #fde68a;
            display:flex;align-items:center;justify-content:center;
            color:#d97706;margin-top:1px;
        }
        .fb-infobox-title { font-size:13px;font-weight:700;color:#92400e;margin-bottom:4px; }
        .fb-infobox-text { font-size:12px;color:#b45309;margin-bottom:6px; }
        .fb-infobox-items { display:flex;flex-direction:column;gap:4px; }
        .fb-infobox-item {
            display:flex;align-items:center;gap:6px;
            font-size:12px;font-weight:600;color:#92400e;
        }
        .fb-infobox-item svg { color:#d97706;flex-shrink:0; }

        /* Submit */
        .fb-submit {
            display:flex;align-items:center;justify-content:center;gap:10px;
            padding:14px 24px;margin-top:4px;
            background:linear-gradient(135deg,#25d366,#128c7e);
            color:#fff;font-size:15px;font-weight:700;
            border:none;border-radius:12px;cursor:pointer;
            font-family:inherit;width:100%;
            box-shadow:0 6px 20px rgba(37,211,102,.35);
            transition:transform .18s,box-shadow .18s,filter .18s;
        }
        .fb-submit:hover { transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,211,102,.4);filter:brightness(1.05); }
        .fb-submit:active { transform:translateY(0); }
    </style>

    <script>
        new TomSelect('#mobil', { create:false, sortField:{field:'text',direction:'asc'} });

        document.getElementById('bookingForm').onsubmit = function(e) {
            e.preventDefault();

            const nama    = "{{ $customer->nama }}";
            const no_telp = "{{ $customer->no_telp }}";
            const ktp     = "{{ $customer->ktp }}";

            const get = id => document.getElementById(id)?.value?.trim() ?? '';

            const mobil              = get('mobil');
            const tanggal_keluar     = get('tanggal_keluar');
            const tanggal_kembali    = get('tanggal_kembali');
            const jam_keluar         = get('jam_keluar');
            const jam_kembali        = get('jam_kembali');
            const paket              = get('paket');
            const lokasi_pengantaran = get('lokasi_pengantaran');
            const lokasi_pengembalian= get('lokasi_pengembalian');
            const facebook           = get('facebook');
            const instagram          = get('instagram');
            const catatan            = get('catatan');

            if (!mobil||!tanggal_keluar||!tanggal_kembali||!jam_keluar||!jam_kembali||!lokasi_pengantaran||!lokasi_pengembalian||!paket) {
                alert('Mohon lengkapi semua field wajib!'); return;
            }

            const fmt = d => new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});

            const msg = `*--- 🚗 BOOKING RENTAL MOBIL 🚗 ---*\n\n*Nama:* ${nama}\n*NIK:* ${ktp}\n*WhatsApp:* ${no_telp}\n*Facebook:* ${facebook||'-'}\n*Instagram:* ${instagram||'-'}\n\n*DETAIL BOOKING:*\n*Mobil:* ${mobil}\n*Tanggal Keluar:* ${fmt(tanggal_keluar)}\n*Tanggal Kembali:* ${fmt(tanggal_kembali)}\n*Jam Antar:* ${jam_keluar} WITA\n*Jam Kembali:* ${jam_kembali} WITA\n*Paket Sewa:* ${paket}\n*Lokasi Antar:* ${lokasi_pengantaran}\n*Lokasi Pengembalian:* ${lokasi_pengembalian}\n\n*Catatan:*\n${catatan||'-'}\n\n*LAMPIRAN YANG DIPERLUKAN:*\n• Foto tiket pesawat/kapal\n• Foto voucher hotel`;

            window.open('https://wa.me/6281128948884?text=' + encodeURIComponent(msg));
        };
    </script>
</body>
</html>
