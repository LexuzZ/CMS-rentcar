<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penyewa — Semeton Pesiar Lombok</title>
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
            <a href="#" class="sp-nav-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
                Masuk
            </a>
        </div>
    </header>

    <main class="sp-main">
        <div class="sp-page-header">
            <h1 class="sp-page-title">Data Penyewa</h1>
            <p class="sp-page-sub">Lengkapi data pelanggan sebelum melanjutkan booking.</p>
        </div>

        <div class="sp-center">

            {{-- Flash Info --}}
            @if (session('info'))
                <div class="sp-alert sp-alert--info">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="sp-alert sp-alert--error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <div>
                        <p style="font-weight:600;margin-bottom:6px">Terdapat kesalahan pada input Anda:</p>
                        <ul style="padding-left:16px;list-style:disc">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('data.penyewa.post') }}" method="POST" enctype="multipart/form-data" class="sp-form">
                @csrf

                {{-- Blok: Data Pribadi --}}
                <div class="sp-block">
                    <h2 class="sp-block-title">Data Pribadi</h2>

                    <div class="sp-field">
                        <label class="sp-label" for="ktp">Nomor KTP</label>
                        <input type="text" id="ktp" name="ktp" maxlength="16" class="sp-input"
                            placeholder="16 digit nomor E-KTP" value="{{ old('ktp') }}" required>
                    </div>

                    <div class="sp-field">
                        <label class="sp-label" for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="sp-input" placeholder="Sesuai identitas"
                            value="{{ old('nama') }}" required>
                    </div>

                    <div class="sp-field">
                        <label class="sp-label" for="no_telp">Nomor WhatsApp</label>
                        <input type="tel" id="no_telp" name="no_telp" class="sp-input" placeholder="0812xxxxxxxx"
                            value="{{ old('no_telp') }}" required>
                    </div>

                    <div class="sp-field">
                        <label class="sp-label" for="alamat">Alamat Tinggal</label>
                        <textarea id="alamat" name="alamat" rows="3" class="sp-input sp-textarea"
                            placeholder="Jalan, kelurahan, kota…" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>

                {{-- Blok: Dokumen --}}
                <div class="sp-block">
                    <h2 class="sp-block-title">
                        Dokumen
                        <span class="sp-opt">opsional</span>
                    </h2>

                    <div class="sp-field">
                        <label class="sp-label" for="lisence">Nomor SIM</label>
                        <input type="text" id="lisence" name="lisence" class="sp-input"
                            placeholder="Nomor SIM A / B / C" value="{{ old('lisence') }}">
                    </div>

                    <div class="sp-upload-row">
                        {{-- Foto KTP --}}
                        <div class="sp-field">
                            <label class="sp-label" for="identity_file">Foto KTP</label>
                            <label for="identity_file" class="sp-upload" id="ktp-box">
                                <span class="sp-upload-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                </span>
                                <span class="sp-upload-text" id="ktp-label">Pilih foto KTP</span>
                                <span class="sp-upload-hint">JPG / PNG</span>
                                <input type="file" accept="image/*" name="identity_file" id="identity_file"
                                    class="sp-upload-input" onchange="updateLabel(this,'ktp-label','ktp-box')">
                            </label>
                        </div>

                        {{-- Foto SIM --}}
                        <div class="sp-field">
                            <label class="sp-label" for="lisence_file">Foto SIM</label>
                            <label for="lisence_file" class="sp-upload" id="sim-box">
                                <span class="sp-upload-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2" />
                                        <path d="M16 3h2a2 2 0 0 1 2 2v2M8 3H6a2 2 0 0 0-2 2v2" />
                                        <circle cx="9" cy="14" r="2" />
                                        <path d="M13 13h4M13 16h4" />
                                    </svg>
                                </span>
                                <span class="sp-upload-text" id="sim-label">Pilih foto SIM</span>
                                <span class="sp-upload-hint">JPG / PNG</span>
                                <input type="file" accept="image/*" name="lisence_file" id="lisence_file"
                                    class="sp-upload-input" onchange="updateLabel(this,'sim-label','sim-box')">
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="sp-submit">
                    <span>Simpan &amp; Lanjut Booking</span>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>
            </form>
        </div>
    </main>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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

        .sp-logo {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .sp-logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .sp-logo-text {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .sp-nav-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 8px;
            background: #f97316;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }

        .sp-nav-btn:hover {
            background: #ea580c;
        }

        /* ── Page header ── */
        .sp-main {
            max-width: 640px;
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

        .sp-center {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* ── Alerts ── */
        .sp-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 15px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.5;
        }

        .sp-alert--info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .sp-alert--error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* ── Form ── */
        .sp-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

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

        /* ── Fields ── */
        .sp-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

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

        .sp-input::placeholder {
            color: #94a3b8;
        }

        .sp-input:hover {
            border-color: #cbd5e1;
        }

        .sp-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
        }

        .sp-textarea {
            resize: vertical;
        }

        /* ── Upload ── */
        .sp-upload-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 420px) {
            .sp-upload-row {
                grid-template-columns: 1fr;
            }
        }

        .sp-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 22px 12px;
            border: 1.5px dashed #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            cursor: pointer;
            text-align: center;
            transition: border-color .15s, background .15s;
        }

        .sp-upload:hover {
            border-color: #f97316;
            background: #fff7ed;
        }

        .sp-upload.is-filled {
            border-color: #16a34a;
            background: #f0fdf4;
        }

        .sp-upload-icon {
            color: #cbd5e1;
            transition: color .15s;
        }

        .sp-upload:hover .sp-upload-icon {
            color: #f97316;
        }

        .sp-upload.is-filled .sp-upload-icon {
            color: #16a34a;
        }

        .sp-upload-text {
            font-size: 12.5px;
            font-weight: 600;
            color: #64748b;
            word-break: break-all;
            line-height: 1.3;
        }

        .sp-upload.is-filled .sp-upload-text {
            color: #15803d;
        }

        .sp-upload-hint {
            font-size: 11px;
            color: #94a3b8;
        }

        .sp-upload-input {
            display: none;
        }

        /* ── Submit ── */
        .sp-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 14px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            width: 100%;
            transition: background .15s, transform .15s;
            box-shadow: 0 4px 14px rgba(249, 115, 22, .3);
        }

        .sp-submit:hover {
            background: #ea580c;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, .35);
        }

        .sp-submit:active {
            transform: translateY(0);
        }
    </style>

    <script>
        function updateLabel(input, labelId, boxId) {
            const label = document.getElementById(labelId);
            const box = document.getElementById(boxId);
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
                box.classList.add('is-filled');
            } else {
                label.textContent = labelId === 'ktp-label' ? 'Pilih foto KTP' : 'Pilih foto SIM';
                box.classList.remove('is-filled');
            }
        }
    </script>

</body>

</html>
