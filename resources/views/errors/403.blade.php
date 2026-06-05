<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak | Semeton Pesiar</title>
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

        /* Blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .blob--tl {
            width: 500px; height: 500px;
            top: -160px; left: -120px;
            background: radial-gradient(circle, rgba(239,68,68,.22) 0%, transparent 70%);
        }
        .blob--br {
            width: 420px; height: 420px;
            bottom: -120px; right: -80px;
            background: radial-gradient(circle, rgba(99,102,241,.2) 0%, transparent 70%);
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

        /* Strip top */
        .card-strip {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            border-radius: 20px 20px 0 0;
            background: linear-gradient(90deg, #ef4444, #f97316, #6366f1);
        }

        /* Icon */
        .icon-wrap {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1.5px solid #fecaca;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 16px rgba(239,68,68,.2);
        }

        /* Error code */
        .error-code {
            font-size: 72px;
            font-weight: 800;
            letter-spacing: -4px;
            line-height: 1;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
            margin-bottom: 28px;
        }

        /* Divider */
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
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }

        .btn--primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
        }
        .btn--primary:hover {
            box-shadow: 0 10px 28px rgba(99,102,241,.45);
            filter: brightness(1.06);
        }

        .btn--ghost {
            background: #f8fafc;
            color: #374151;
            border: 1.5px solid #e2e8f0;
        }
        .btn--ghost:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* Brand footer */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 24px;
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

        {{-- Icon --}}
        <div class="icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                <line x1="12" y1="15" x2="12" y2="17"/>
            </svg>
        </div>

        {{-- Code --}}
        <div class="error-code">403</div>

        {{-- Title --}}
        <h1 class="error-title">Akses Ditolak</h1>

        {{-- Description --}}
        <p class="error-desc">
            Anda tidak memiliki izin untuk membuka halaman ini.<br>
            Hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>

        <div class="divider"></div>

        {{-- Actions --}}
        <div class="actions">
            @if(auth()->check())
                <a href="{{ url()->previous() }}" class="btn btn--primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Kembali ke Halaman Sebelumnya
                </a>
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn--ghost">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Ke Dashboard
                </a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}" class="btn btn--primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
