<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kedaluwarsa | Semeton Pesiar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            overflow: hidden;
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .blob--tl {
            width: 500px; height: 500px;
            top: -160px; left: -120px;
            background: radial-gradient(circle, rgba(245,158,11,.2) 0%, transparent 70%);
        }
        .blob--br {
            width: 420px; height: 420px;
            bottom: -120px; right: -80px;
            background: radial-gradient(circle, rgba(99,102,241,.18) 0%, transparent 70%);
        }

        /* Card */
        .card {
            position: relative; z-index: 1;
            background: #ffffff;
            border-radius: 20px;
            padding: 44px 36px 36px;
            max-width: 460px;
            width: 100%;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.06),
                0 24px 60px rgba(0,0,0,.45),
                0 2px 8px rgba(0,0,0,.2);
            text-align: center;
            animation: card-rise .44s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes card-rise {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-strip {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            border-radius: 20px 20px 0 0;
            background: linear-gradient(90deg, #f59e0b, #f97316, #6366f1);
        }

        /* Icon */
        .icon-wrap {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1.5px solid #fde68a;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 16px rgba(245,158,11,.2);
        }

        /* Clock animation */
        .clock-hand {
            transform-origin: center bottom;
            animation: tick 2s steps(12, end) infinite;
        }
        @keyframes tick {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* Error code */
        .error-code {
            font-size: 72px;
            font-weight: 800;
            letter-spacing: -4px;
            line-height: 1;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .error-desc {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        /* Info box */
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 22px;
            text-align: left;
        }
        .info-box-icon {
            width: 28px; height: 28px; flex-shrink: 0;
            border-radius: 7px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            display: flex; align-items: center; justify-content: center;
            color: #d97706;
            margin-top: 1px;
        }
        .info-box-text {
            font-size: 12px;
            color: #92400e;
            line-height: 1.6;
        }
        .info-box-text strong { font-weight: 700; }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin-bottom: 22px;
        }

        /* Actions */
        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .18s, box-shadow .18s, filter .18s;
            cursor: pointer;
            font-family: inherit;
            border: none;
            width: 100%;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }

        .btn--primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            box-shadow: 0 6px 20px rgba(245,158,11,.4);
        }
        .btn--primary:hover {
            box-shadow: 0 10px 28px rgba(245,158,11,.45);
            filter: brightness(1.06);
        }

        .btn--ghost {
            background: #f8fafc;
            color: #374151;
            border: 1.5px solid #e2e8f0;
        }
        .btn--ghost:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 22px;
        }
        .brand-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #e2e8f0;
        }
        .brand-text {
            font-size: 11.5px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="blob blob--tl"></div>
    <div class="blob blob--br"></div>

    <div class="card">
        <div class="card-strip"></div>

        {{-- Animated clock icon --}}
        <div class="icon-wrap">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14" class="clock-hand"/>
            </svg>
        </div>

        {{-- Code --}}
        <div class="error-code">419</div>

        {{-- Title --}}
        <h1 class="error-title">Sesi Halaman Kedaluwarsa</h1>

        {{-- Description --}}
        <p class="error-desc">
            Halaman ini sudah tidak aktif karena sesi Anda habis atau terlalu lama tidak ada aktivitas.
        </p>

        {{-- Info box --}}
        <div class="info-box">
            <div class="info-box-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <p class="info-box-text">
                <strong>Mengapa ini terjadi?</strong><br>
                Token keamanan (CSRF) sudah tidak valid. Hal ini terjadi saat halaman dibuka terlalu lama, atau setelah Anda logout lalu kembali ke halaman yang sama.
            </p>
        </div>

        <div class="divider"></div>

        {{-- Actions --}}
        <div class="actions">
            <button onclick="window.location.reload()" class="btn btn--primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                </svg>
                Muat Ulang Halaman
            </button>

            @if(auth()->check())
                <a href="{{ url()->previous() }}" class="btn btn--ghost">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Kembali ke Halaman Sebelumnya
                </a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}" class="btn btn--ghost">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Masuk ke Akun
                </a>
            @endif
        </div>

        <div class="brand">
            <div class="brand-dot"></div>
            <span class="brand-text">Semeton Pesiar Lombok</span>
            <div class="brand-dot"></div>
        </div>
    </div>

</body>
</html>
