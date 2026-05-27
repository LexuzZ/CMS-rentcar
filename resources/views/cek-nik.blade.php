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

                {{-- Alerts --}}
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
                                inputmode="numeric" autocomplete="off" required>
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
        .ck-blob {
            position: fixed; border-radius: 50%; pointer-events: none; z-index: 0;
        }
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
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
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
        .ck-divider {
            display: flex; align-items: center; gap: 12px;
        }
        .ck-divider::before, .ck-divider::after {
            content: ''; flex: 1; height: 1px; background: #f1f5f9;
        }
        .ck-divider span { font-size: 12px; color: #94a3b8; white-space: nowrap; font-weight: 500; }

        /* Register */
        .ck-register {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px;
            border: 1.5px solid #e2e8f0; border-radius: 11px;
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
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
            const n = input.value.length;
            counter.textContent = n + ' / 16';
            counter.classList.toggle('full', n === 16);
        });
    </script>

</body>
</html>
