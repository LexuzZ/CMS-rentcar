<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Data Penyewa</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="ck-body">

    {{-- Decorative blobs --}}
    <div class="ck-blob ck-blob--tl"></div>
    <div class="ck-blob ck-blob--br"></div>

    <div class="ck-wrapper">

        {{-- Brand --}}
        <div class="ck-brand">
            <div class="ck-brand-logo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                    <circle cx="7.5" cy="17.5" r="1.5"/>
                    <circle cx="16.5" cy="17.5" r="1.5"/>
                </svg>
            </div>
            <span class="ck-brand-name">Semeton Pesiar Lombok</span>
        </div>

        {{-- Card --}}
        <div class="ck-card">

            {{-- Card header strip --}}
            <div class="ck-card-strip"></div>

            <div class="ck-card-body">

                {{-- Heading --}}
                <div class="ck-heading">
                    <div class="ck-heading-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <circle cx="8" cy="12" r="2.5"/>
                            <path d="M14 10h4M14 14h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="ck-title">Cek Data Penyewa</h1>
                        <p class="ck-subtitle">Masukkan NIK untuk melanjutkan booking</p>
                    </div>
                </div>

                {{-- ═══ ALERTS BIASA ═══ --}}
                @if (session('success'))
                <div class="ck-alert ck-alert--success">
                    <span class="ck-alert-dot ck-alert-dot--success"></span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if (session('info'))
                <div class="ck-alert ck-alert--info">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
                @endif

                @if (session('error'))
                <div class="ck-alert ck-alert--error">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                {{-- ═══ BLACKLIST ALERT ═══ --}}
                @if (session('error_blacklist'))
                @php
                    $bl = session('error_blacklist');
                    $alasanLabel = match($bl['alasan'] ?? '') {
                        'tidak_bayar'   => 'Tidak membayar tagihan sewa',
                        'merusak_mobil' => 'Merusak kendaraan',
                        'kabur'         => 'Melarikan diri / tidak mengembalikan kendaraan',
                        'penipuan'      => 'Penipuan atau identitas palsu',
                        'bermasalah'    => 'Penyewa bermasalah',
                        default         => 'Pelanggaran ketentuan sewa',
                    };
                @endphp
                <div class="ck-bl">
                    {{-- Header --}}
                    <div class="ck-bl-header">
                        <div class="ck-bl-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                            </svg>
                        </div>
                        <div>
                            <p class="ck-bl-title">NIK Tidak Dapat Digunakan</p>
                            <p class="ck-bl-subtitle">Nomor identitas ini terdaftar dalam daftar hitam sistem</p>
                        </div>
                    </div>

                    {{-- NIK box --}}
                    <div class="ck-bl-nik">
                        <span class="ck-bl-nik-tag">NIK</span>
                        <span class="ck-bl-nik-val">{{ $bl['nik'] }}</span>
                    </div>

                    {{-- Alasan --}}
                    <div class="ck-bl-reason">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <span><strong>Alasan:</strong> {{ $alasanLabel }}</span>
                    </div>

                    <div class="ck-bl-divider"></div>

                    <p class="ck-bl-help">
                        Jika Anda merasa ini kesalahan, hubungi kami untuk klarifikasi:
                    </p>

                    <a href="https://wa.me/6281128948884?text={{ urlencode('Halo, saya ingin klarifikasi terkait NIK ' . $bl['nik'] . ' yang terblacklist di sistem Semeton Pesiar.') }}"
                       target="_blank"
                       class="ck-bl-wa">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Hubungi Kami via WhatsApp
                    </a>
                </div>
                @endif
                {{-- ═══ END BLACKLIST ALERT ═══ --}}

                {{-- Form --}}
                <form action="{{ route('cek.nik.post') }}" method="POST" class="ck-form">
                    @csrf

                    <div class="ck-field">
                        <label class="ck-label" for="ktp">Nomor Induk Kependudukan (NIK)</label>
                        <div class="ck-input-wrap">
                            <span class="ck-input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                                    <circle cx="8" cy="12" r="2.5"/>
                                    <path d="M14 10h4M14 14h4"/>
                                </svg>
                            </span>
                            <input type="text" id="ktp" name="ktp" maxlength="16"
                                class="ck-input" placeholder="Masukkan 16 digit NIK…"
                                inputmode="numeric" autocomplete="off" required
                                value="{{ old('ktp') }}">
                            <span class="ck-counter" id="nik-counter">0 / 16</span>
                        </div>
                        <p class="ck-hint">Sesuai e-KTP yang masih berlaku</p>
                    </div>

                    <button type="submit" class="ck-submit">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Cek Data Saya
                    </button>
                </form>

                {{-- Divider --}}
                <div class="ck-divider">
                    <span>Belum punya akun?</span>
                </div>

                {{-- Register link --}}
                <a href="{{ route('data.penyewa') }}" class="ck-register">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    Daftar sebagai penyewa baru
                </a>

                {{-- Security note --}}
                <p class="ck-security">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Data Anda aman &amp; tidak akan disebarkan
                </p>

            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>{{-- /wrapper --}}

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .ck-body {
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        /* Blobs */
        .ck-blob { position: fixed; border-radius: 50%; pointer-events: none; z-index: 0; }
        .ck-blob--tl {
            width: 480px; height: 480px; top: -160px; left: -120px;
            background: radial-gradient(circle, rgba(99,102,241,.28) 0%, transparent 70%);
        }
        .ck-blob--br {
            width: 400px; height: 400px; bottom: -120px; right: -80px;
            background: radial-gradient(circle, rgba(16,185,129,.2) 0%, transparent 70%);
        }

        /* Wrapper */
        .ck-wrapper {
            width: 100%; max-width: 420px;
            position: relative; z-index: 1;
            animation: ck-rise .42s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes ck-rise {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Brand */
        .ck-brand {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 22px;
        }
        .ck-brand-logo {
            width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 4px 14px rgba(99,102,241,.4);
        }
        .ck-brand-name { font-size: 14px; font-weight: 700; color: #e2e8f0; letter-spacing: -.01em; }

        /* Card */
        .ck-card {
            background: #ffffff; border-radius: 20px; overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.06),
                0 20px 50px rgba(0,0,0,.45),
                0 2px 8px rgba(0,0,0,.2);
        }
        .ck-card-strip {
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4);
        }
        .ck-card-body { padding: 28px 28px 24px; display: flex; flex-direction: column; gap: 18px; }
        @media (max-width: 480px) { .ck-card-body { padding: 22px 20px 20px; } }

        /* Heading */
        .ck-heading { display: flex; align-items: center; gap: 14px; }
        .ck-heading-icon {
            width: 50px; height: 50px; border-radius: 14px; flex-shrink: 0;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1px solid #bfdbfe;
            display: flex; align-items: center; justify-content: center;
            color: #2563eb;
        }
        .ck-title { font-size: 19px; font-weight: 800; color: #0f172a; letter-spacing: -.02em; }
        .ck-subtitle { font-size: 13px; color: #64748b; margin-top: 3px; }

        /* Alerts */
        .ck-alert {
            display: flex; align-items: center; gap: 9px;
            padding: 11px 14px; border-radius: 10px;
            font-size: 13px; font-weight: 500; line-height: 1.4;
            position: relative; overflow: hidden;
        }
        .ck-alert--success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .ck-alert--info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .ck-alert--error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .ck-alert-dot {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            width: 7px; height: 7px; border-radius: 50%;
        }
        .ck-alert-dot--success { background: #16a34a; animation: ck-blink 1.8s infinite; }
        @keyframes ck-blink { 0%,100%{opacity:1} 50%{opacity:.3} }

        /* ═══ BLACKLIST ALERT ═══ */
        .ck-bl {
            border-radius: 14px;
            border: 2px solid #fca5a5;
            background: linear-gradient(135deg, #fff1f2, #fee2e2);
            overflow: hidden;
            animation: ck-shake .45s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes ck-shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        /* Header */
        .ck-bl-header {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 16px 16px 12px;
        }
        .ck-bl-icon {
            width: 42px; height: 42px; flex-shrink: 0; border-radius: 11px;
            background: #fee2e2; border: 1.5px solid #fca5a5;
            display: flex; align-items: center; justify-content: center;
            color: #dc2626;
            box-shadow: 0 3px 8px rgba(220,38,38,.18);
        }
        .ck-bl-title {
            font-size: 14px; font-weight: 800; color: #991b1b;
            margin: 0 0 3px; letter-spacing: -.01em;
        }
        .ck-bl-subtitle { font-size: 11.5px; color: #b91c1c; line-height: 1.45; }

        /* NIK box */
        .ck-bl-nik {
            display: flex; align-items: center; gap: 10px;
            margin: 0 16px 12px;
            background: #fff; border: 1px solid #fecaca;
            border-radius: 10px; padding: 9px 14px;
        }
        .ck-bl-nik-tag {
            font-size: 10px; font-weight: 800; letter-spacing: .08em;
            text-transform: uppercase; color: #dc2626;
            background: #fee2e2; border-radius: 5px; padding: 2px 8px;
            flex-shrink: 0;
        }
        .ck-bl-nik-val {
            font-size: 16px; font-weight: 800;
            font-family: ui-monospace, monospace;
            letter-spacing: .12em; color: #991b1b;
        }

        /* Alasan */
        .ck-bl-reason {
            display: flex; align-items: flex-start; gap: 7px;
            margin: 0 16px 12px;
            font-size: 12.5px; color: #b91c1c; line-height: 1.55;
        }

        .ck-bl-divider { height: 1px; background: #fecaca; margin: 0 0 12px; }

        .ck-bl-help {
            font-size: 11.5px; color: #9f1239; line-height: 1.5;
            padding: 0 16px; margin-bottom: 10px;
        }

        /* WA Button */
        .ck-bl-wa {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin: 0 16px 16px;
            padding: 10px 16px; border-radius: 10px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff; text-decoration: none;
            font-size: 13px; font-weight: 700;
            box-shadow: 0 3px 10px rgba(34,197,94,.3);
            transition: filter .15s, transform .15s;
        }
        .ck-bl-wa:hover { filter: brightness(.92); transform: translateY(-1px); }

        /* Form */
        .ck-form { display: flex; flex-direction: column; gap: 14px; }
        .ck-field { display: flex; flex-direction: column; gap: 5px; }
        .ck-label { font-size: 13px; font-weight: 600; color: #374151; }

        .ck-input-wrap { position: relative; }
        .ck-input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; display: flex; pointer-events: none;
        }
        .ck-input {
            width: 100%;
            padding: 13px 52px 13px 42px;
            border: 1.5px solid #e2e8f0; border-radius: 11px;
            font-size: 15px; font-weight: 600;
            color: #0f172a; background: #f8fafc;
            font-family: 'Plus Jakarta Sans', ui-monospace, monospace;
            letter-spacing: .06em;
            transition: border-color .18s, box-shadow .18s, background .18s;
            outline: none; -webkit-appearance: none;
        }
        .ck-input:hover { border-color: #cbd5e1; background: #fff; }
        .ck-input:focus {
            border-color: #6366f1; background: #fff;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }
        .ck-input::placeholder { color: #94a3b8; font-weight: 400; letter-spacing: 0; }

        .ck-counter {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 11px; font-weight: 600; color: #cbd5e1; pointer-events: none;
            transition: color .18s;
        }
        .ck-counter.full { color: #6366f1; }
        .ck-hint { font-size: 11.5px; color: #94a3b8; }

        /* Submit */
        .ck-submit {
            display: flex; align-items: center; justify-content: center; gap: 9px;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff; font-size: 15px; font-weight: 700;
            border: none; border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
            transition: transform .18s, box-shadow .18s, filter .18s;
            font-family: inherit; width: 100%;
        }
        .ck-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(99,102,241,.45); filter: brightness(1.06); }
        .ck-submit:active { transform: translateY(0); }

        /* Divider */
        .ck-divider { display: flex; align-items: center; gap: 12px; }
        .ck-divider::before, .ck-divider::after { content: ''; flex: 1; height: 1px; background: #f1f5f9; }
        .ck-divider span { font-size: 12px; color: #94a3b8; white-space: nowrap; font-weight: 500; }

        /* Register */
        .ck-register {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px; border: 1.5px solid #e2e8f0; border-radius: 11px;
            font-size: 13.5px; font-weight: 600; color: #374151;
            text-decoration: none; background: #f8fafc;
            transition: border-color .16s, background .16s, color .16s;
        }
        .ck-register:hover { border-color: #6366f1; color: #4f46e5; background: #eef2ff; }

        /* Security */
        .ck-security {
            display: flex; align-items: center; justify-content: center; gap: 5px;
            font-size: 11.5px; color: #94a3b8;
        }
    </style>

    <script>
        const input   = document.getElementById('ktp');
        const counter = document.getElementById('nik-counter');

        // Isi counter jika ada old value (mis. setelah redirect back)
        if (input.value.length) {
            counter.textContent = input.value.length + ' / 16';
            if (input.value.length === 16) counter.classList.add('full');
        }

        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
            const n = input.value.length;
            counter.textContent = n + ' / 16';
            counter.classList.toggle('full', n === 16);
        });
    </script>

</body>
</html>
