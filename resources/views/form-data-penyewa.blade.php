<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penyewa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="fp-body">

    {{-- Background decoration --}}
    <div class="fp-bg-top"></div>
    <div class="fp-bg-bottom"></div>

    <div class="fp-wrapper">

        {{-- Card --}}
        <div class="fp-card">

            {{-- Header --}}
            <div class="fp-header">
                <div class="fp-header-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="fp-title">Form Data Penyewa</h2>
                    <p class="fp-subtitle">Lengkapi data pelanggan sebelum melanjutkan booking.</p>
                </div>
            </div>

            {{-- Divider --}}
            <div class="fp-divider"></div>

            {{-- Flash Info --}}
            @if (session('info'))
            <div class="fp-alert fp-alert--info">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
            <div class="fp-alert fp-alert--error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
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

            {{-- Form --}}
            <form action="{{ route('data.penyewa.post') }}" method="POST" enctype="multipart/form-data" class="fp-form">
                @csrf

                {{-- Section: Data Pribadi --}}
                <p class="fp-section-label">Data Pribadi</p>

                {{-- KTP --}}
                <div class="fp-field">
                    <label class="fp-label" for="ktp">Nomor KTP</label>
                    <div class="fp-input-wrap">
                        <span class="fp-input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <circle cx="8" cy="12" r="2.5"/>
                                <path d="M14 10h4M14 14h4"/>
                            </svg>
                        </span>
                        <input type="text" id="ktp" name="ktp" maxlength="16"
                            class="fp-input" placeholder="16 digit nomor E-KTP" required>
                    </div>
                </div>

                {{-- Nama --}}
                <div class="fp-field">
                    <label class="fp-label" for="nama">Nama Lengkap</label>
                    <div class="fp-input-wrap">
                        <span class="fp-input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="nama" name="nama"
                            class="fp-input" placeholder="Sesuai identitas" required>
                    </div>
                </div>

                {{-- No Telp --}}
                <div class="fp-field">
                    <label class="fp-label" for="no_telp">Nomor WhatsApp</label>
                    <div class="fp-input-wrap">
                        <span class="fp-input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </span>
                        <input type="tel" id="no_telp" name="no_telp"
                            class="fp-input" placeholder="0812xxxxxxxx" required>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="fp-field">
                    <label class="fp-label" for="alamat">Alamat Tinggal</label>
                    <div class="fp-input-wrap fp-input-wrap--textarea">
                        <span class="fp-input-icon fp-input-icon--top">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                        </span>
                        <textarea id="alamat" name="alamat" rows="3"
                            class="fp-input fp-textarea" placeholder="Jalan, kelurahan, kota…" required></textarea>
                    </div>
                </div>

                {{-- Section: Dokumen --}}
                <p class="fp-section-label" style="margin-top:8px">Dokumen <span class="fp-optional-badge">opsional</span></p>

                {{-- SIM --}}
                <div class="fp-field">
                    <label class="fp-label" for="lisence">Nomor SIM</label>
                    <div class="fp-input-wrap">
                        <span class="fp-input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 3h2a2 2 0 0 1 2 2v2M8 3H6a2 2 0 0 0-2 2v2"/>
                                <circle cx="9" cy="14" r="2"/>
                                <path d="M13 13h4M13 16h4"/>
                            </svg>
                        </span>
                        <input type="text" id="lisence" name="lisence"
                            class="fp-input" placeholder="Nomor SIM A / B / C">
                    </div>
                </div>

                {{-- Upload row --}}
                <div class="fp-upload-row">
                    {{-- Foto KTP --}}
                    <div class="fp-field">
                        <label class="fp-label" for="identity_file">Foto KTP</label>
                        <label for="identity_file" class="fp-upload">
                            <span class="fp-upload-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </span>
                            <span class="fp-upload-text" id="ktp-label">Pilih foto KTP</span>
                            <input type="file" accept="image/*" name="identity_file" id="identity_file"
                                class="fp-upload-input" onchange="updateLabel(this,'ktp-label')">
                        </label>
                    </div>

                    {{-- Foto SIM --}}
                    <div class="fp-field">
                        <label class="fp-label" for="lisence_file">Foto SIM</label>
                        <label for="lisence_file" class="fp-upload">
                            <span class="fp-upload-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </span>
                            <span class="fp-upload-text" id="sim-label">Pilih foto SIM</span>
                            <input type="file" accept="image/*" name="lisence_file" id="lisence_file"
                                class="fp-upload-input" onchange="updateLabel(this,'sim-label')">
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="fp-submit">
                    <span>Simpan & Lanjut Booking</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .fp-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            padding: 24px 16px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs */
        .fp-bg-top {
            position: fixed; top: -160px; left: -100px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.35) 0%, transparent 70%);
            pointer-events: none;
        }
        .fp-bg-bottom {
            position: fixed; bottom: -120px; right: -80px;
            width: 420px; height: 420px; border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        .fp-wrapper {
            width: 100%; max-width: 520px;
            position: relative; z-index: 1;
            animation: fp-rise 0.45s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fp-rise {
            from { opacity:0; transform: translateY(28px); }
            to   { opacity:1; transform: translateY(0); }
        }

        /* Card */
        .fp-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px 32px 28px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.35), 0 2px 6px rgba(0,0,0,0.15);
        }
        @media (max-width: 480px) { .fp-card { padding: 24px 20px 22px; } }

        /* Header */
        .fp-header {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 20px;
        }
        .fp-header-icon {
            width: 50px; height: 50px; border-radius: 14px; flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            box-shadow: 0 6px 16px rgba(99,102,241,0.35);
        }
        .fp-title { font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1.2; }
        .fp-subtitle { font-size: 13px; color: #64748b; margin-top: 3px; line-height: 1.5; }

        .fp-divider { height: 1px; background: #f1f5f9; margin-bottom: 20px; }

        /* Alerts */
        .fp-alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px; border-radius: 10px; font-size: 13px;
            margin-bottom: 18px; line-height: 1.5;
        }
        .fp-alert--info  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .fp-alert--error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .fp-alert svg { flex-shrink: 0; margin-top: 1px; }

        /* Form */
        .fp-form { display: flex; flex-direction: column; gap: 14px; }

        .fp-section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 0.07em;
            color: #94a3b8; text-transform: uppercase;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: -4px;
        }
        .fp-optional-badge {
            font-size: 10px; font-weight: 600; letter-spacing: 0.03em;
            text-transform: none; color: #64748b;
            background: #f1f5f9; border-radius: 100px; padding: 1px 8px;
        }

        .fp-field { display: flex; flex-direction: column; gap: 5px; }

        .fp-label { font-size: 13px; font-weight: 600; color: #374151; }

        /* Input wrapper */
        .fp-input-wrap {
            position: relative;
        }
        .fp-input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; display: flex; pointer-events: none;
        }
        .fp-input-icon--top {
            top: 14px; transform: none;
        }

        .fp-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            font-family: inherit;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            outline: none;
            -webkit-appearance: none;
        }
        .fp-input:hover { border-color: #cbd5e1; background: #fff; }
        .fp-input:focus {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .fp-input::placeholder { color: #94a3b8; }
        .fp-textarea { resize: none; padding-top: 12px; }

        /* Upload */
        .fp-upload-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .fp-upload {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 8px; padding: 18px 12px;
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: border-color 0.18s, background 0.18s;
            text-align: center;
        }
        .fp-upload:hover { border-color: #6366f1; background: #eef2ff; }
        .fp-upload-icon { color: #94a3b8; transition: color 0.18s; }
        .fp-upload:hover .fp-upload-icon { color: #6366f1; }
        .fp-upload-text { font-size: 12px; color: #64748b; font-weight: 500; word-break: break-all; }
        .fp-upload-input { display: none; }

        /* Submit */
        .fp-submit {
            margin-top: 6px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 14px 24px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            font-size: 15px; font-weight: 700;
            border: none; border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(99,102,241,0.4);
            transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
            font-family: inherit;
        }
        .fp-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(99,102,241,0.45);
            filter: brightness(1.06);
        }
        .fp-submit:active { transform: translateY(0); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
    </style>

    <script>
        function updateLabel(input, labelId) {
            const el = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                el.textContent = input.files[0].name;
                el.style.color = '#4f46e5';
                el.style.fontWeight = '600';
            }
        }
    </script>

</body>
</html>
