<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Sewa mobil Lombok di Semeton Pesiar. Pilihan rental mobil lepas kunci dan dengan sopir, berbagai armada, harga terjangkau, dan booking online untuk perjalanan di Lombok.">
    <title>Semeton Pesiar – Sewa Kendaraan Lombok</title>
    <meta property="og:type" content="website">
    <meta property="og:title" content="Sewa Mobil Lombok – Lepas Kunci & Dengan Sopir">
    <meta property="og:description"
        content="Rental mobil Lombok dengan pilihan lepas kunci dan dengan sopir. Lihat armada, harga, dan booking online di Semeton Pesiar.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('/public/sptLOGO.png') }}">
    <meta property="og:site_name" content="Semeton Pesiar Lombok">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sewa Mobil Lombok | Semeton Pesiar">
    <meta name="twitter:description" content="Rental mobil Lombok lepas kunci dan dengan sopir.">
    <meta name="twitter:image" content="{{ asset('/public/sptLOGO.png') }}">
    <link rel="icon" type="image/x-icon" href="/public/sptLOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .hp-body {
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', system-ui, sans-serif;
            color: #0f172a;
            font-size: 14px;
        }

        /* ── Navbar ── */
        .hp-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .hp-nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hp-logo {
            display: flex;
            align-items: center;
            gap: 9px;
            text-decoration: none;
        }

        .hp-logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #f0eeed, #efebe9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .hp-logo-text {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ── Search bar ── */
        .hp-searchbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }

        .hp-searchbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px 24px;
        }

        /* Toggle tipe */
        .hp-toggle-row {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }

        .hp-tipe-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: #374151;
            background: #fff;
            cursor: pointer;
            transition: all .15s;
        }

        .hp-tipe-btn:hover {
            border-color: #f97316;
            color: #f97316;
        }

        .hp-tipe-btn.active {
            background: #fff7ed;
            border-color: #f97316;
            color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .1);
        }

        .hp-tipe-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
            flex-shrink: 0;
        }

        .hp-tipe-btn.active .hp-tipe-icon {
            background: #ffedd5;
        }

        /* Date row */
        .hp-date-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
        }

        .hp-date-top {
            display: flex;
            align-items: stretch;
            gap: 8px;
        }

        .hp-date-loc {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: #f97316;
        }

        .hp-date-micro {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .hp-date-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .hp-date-fields {
            display: flex;
            align-items: stretch;
            gap: 8px;
        }

        .hp-date-field {
            flex: 1;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            min-width: 0;
        }

        .hp-date-field:focus-within {
            border-color: #f97316;
        }

        .hp-date-input {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            font-family: inherit;
        }

        @media (max-width: 380px) {
            .hp-date-input {
                font-size: 11px;
            }

            .hp-date-field {
                padding: 9px 10px;
            }
        }

        .hp-date-arrow {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            color: #cbd5e1;
        }

        @media (max-width: 360px) {
            .hp-date-arrow {
                display: none;
            }
        }

        @media (min-width: 768px) {
            .hp-date-row {
                flex-direction: row;
                align-items: stretch;
                gap: 10px;
            }

            .hp-date-top {
                flex: 0 0 auto;
                gap: 10px;
            }

            .hp-date-top .hp-ubah-btn {
                display: none;
            }

            .hp-date-fields {
                flex: 1;
                gap: 10px;
            }
        }

        .hp-ubah-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            background: #fbbf24;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .hp-ubah-btn:hover {
            background: #f59e0b;
        }

        .hp-ubah-btn-desktop {
            display: none;
        }

        @media (min-width: 768px) {
            .hp-ubah-btn-desktop {
                display: flex;
            }
        }

        /* Search row */
        .hp-search-row {
            display: flex;
            gap: 8px;
        }

        .hp-search-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: border-color .15s;
            color: #94a3b8;
        }

        .hp-search-wrap:focus-within {
            border-color: #f97316;
        }

        .hp-search-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            color: #0f172a;
            font-family: inherit;
        }

        .hp-search-input::placeholder {
            color: #94a3b8;
        }

        .hp-search-btn {
            padding: 10px 22px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s;
        }

        .hp-search-btn:hover {
            background: #ea580c;
        }

        /* ── Filter bar ── */
        .hp-filter-bar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }

        .hp-filter-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hp-filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hp-filter-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .hp-chip {
            border: 1.5px solid #e2e8f0;
            border-radius: 100px;
            padding: 5px 14px;
            font-size: 12.5px;
            font-weight: 500;
            color: #374151;
            background: #fff;
            cursor: pointer;
            transition: all .15s;
        }

        .hp-chip:hover {
            border-color: #f97316;
            color: #f97316;
        }

        .hp-chip.active {
            background: #fff7ed;
            border-color: #f97316;
            color: #f97316;
            font-weight: 700;
        }

        .hp-sort-select {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            background: #fff;
            outline: none;
            cursor: pointer;
            font-family: inherit;
        }

        .hp-sort-select:focus {
            border-color: #f97316;
        }

        /* ── Grid ── */
        .hp-grid-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 24px 60px;
        }

        .hp-grid-header {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 18px;
        }

        .hp-grid-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
        }

        .hp-grid-count {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .hp-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        @media (min-width: 768px) {
            .hp-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .hp-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Card */
        .hp-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .hp-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 36px rgba(0, 0, 0, .09);
        }

        .hp-card-img-wrap {
            position: relative;
            background: #f8fafc;
            aspect-ratio: 4/3;
            overflow: hidden;
        }

        .hp-card-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 14px;
        }

        .hp-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
        }

        .badge-orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-gray {
            background: #f1f5f9;
            color: #475569;
        }

        .hp-badge-tr {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, .92);
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 100px;
            color: #475569;
            backdrop-filter: blur(4px);
        }

        .hp-badge-bl {
            position: absolute;
            bottom: 10px;
            left: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 255, 255, .92);
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 100px;
            color: #475569;
            backdrop-filter: blur(4px);
        }

        .hp-card-body {
            padding: 14px;
        }

        .hp-card-brand {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .hp-card-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .hp-card-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 10px;
        }

        .hp-chip-tipe {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .hp-chip-tipe--kunci {
            background: #fff7ed;
            color: #f97316;
            border: 1px solid #fed7aa;
        }

        .hp-chip-tipe--sopir {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .hp-card-price-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .hp-card-price {
            font-size: 17px;
            font-weight: 800;
            color: #f97316;
        }

        .hp-card-per {
            font-size: 12px;
            color: #94a3b8;
        }

        .hp-card-total {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 12px;
        }

        .hp-card-total strong {
            color: #0f172a;
            font-weight: 700;
        }

        .hp-card-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s;
        }

        .hp-card-btn:hover {
            background: #ea580c;
        }

        /* Empty */
        .hp-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
            text-align: center;
        }

        .hp-empty-icon {
            width: 72px;
            height: 72px;
            background: #fff7ed;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .hp-empty-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .hp-empty-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .hp-empty-reset {
            padding: 10px 22px;
            background: #f97316;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s;
        }

        .hp-empty-reset:hover {
            background: #ea580c;
        }

        /* ── Pagination ── */
        .hp-pagination {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        .hp-pagination nav {
            display: flex;
            justify-content: center;
        }

        .hp-pagination nav>div:first-child {
            display: none;
        }

        .hp-pagination .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hp-pagination .page-item {
            line-height: 1;
        }

        .hp-pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            background: #fff;
            text-decoration: none;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all .15s;
            cursor: pointer;
            line-height: 1;
        }

        .hp-pagination .page-link:hover {
            border-color: #f97316;
            color: #f97316;
            background: #fff7ed;
        }

        .hp-pagination .page-item.active .page-link {
            background: #f97316;
            border-color: #f97316;
            color: #fff;
            box-shadow: 0 2px 8px rgba(249, 115, 22, .35);
            cursor: default;
        }

        .hp-pagination .page-item.active .page-link:hover {
            background: #ea580c;
            border-color: #ea580c;
        }

        .hp-pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            border-color: #f1f5f9;
            background: #f8fafc;
            cursor: not-allowed;
            pointer-events: none;
        }

        .hp-pagination .page-item .page-link[aria-disabled="true"],
        .hp-pagination .page-item span.page-link:not([href]) {
            color: #94a3b8;
            border-color: transparent;
            background: transparent;
            cursor: default;
            pointer-events: none;
        }

        .hp-pagination .page-item:first-child .page-link,
        .hp-pagination .page-item:last-child .page-link {
            padding: 0 14px;
        }

        @media (max-width: 480px) {
            .hp-pagination .page-link {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
                padding: 0 8px;
            }
        }

        /* ── Modal ── */
        .hp-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .hp-modal-backdrop.show {
            display: flex;
        }

        .hp-modal {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .25);
            animation: modal-in .22s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(.95) translateY(12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .hp-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 22px;
            background: #f97316;
            color: #fff;
        }

        .hp-modal-step {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, .75);
            margin-bottom: 3px;
        }

        .hp-modal-title {
            font-size: 17px;
            font-weight: 800;
        }

        .hp-modal-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }

        .hp-modal-close:hover {
            background: rgba(255, 255, 255, .25);
        }

        .hp-modal-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .hp-modal-car {
            padding: 12px 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
        }

        .hp-modal-car-label {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 3px;
        }

        .hp-modal-car-name {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
        }

        .hp-modal-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .hp-modal-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .hp-modal-input-wrap {
            position: relative;
        }

        .hp-modal-input {
            width: 100%;
            padding: 11px 60px 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            letter-spacing: .05em;
        }

        .hp-modal-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
            letter-spacing: 0;
        }

        .hp-modal-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
        }

        .hp-modal-counter {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            pointer-events: none;
        }

        .hp-modal-hint {
            font-size: 12px;
            color: #94a3b8;
        }

        .hp-modal-alert {
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.5;
        }

        .hp-modal-alert--error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .hp-modal-alert--success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .hp-modal-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            width: 100%;
            transition: background .15s;
        }

        .hp-modal-submit:hover {
            background: #ea580c;
        }

        .hp-modal-submit:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        /* ══════════════════════════════════════
   JUMBOTRON
   ══════════════════════════════════════ */

        .hp-hero {
            position: relative;
            min-height: 430px;
            display: flex;
            align-items: center;
            overflow: hidden;

            background:
                linear-gradient(90deg,
                    rgba(15, 23, 42, 0.88) 0%,
                    rgba(15, 23, 42, 0.72) 42%,
                    rgba(15, 23, 42, 0.35) 100%),
                url('{{ asset('images/jumbotron/slider-mobil.png') }}') center center / cover no-repeat;
        }

        .hp-hero-overlay {
            position: absolute;
            inset: 0;

            background:
                linear-gradient(90deg,
                    rgba(15, 23, 42, 0.88) 0%,
                    rgba(15, 23, 42, 0.72) 42%,
                    rgba(15, 23, 42, 0.35) 100%);

            z-index: 1;
        }

        .hp-hero-inner {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 1200px;
            margin: 0 auto;

            padding: 72px 24px;
        }

        .hp-hero-badge {
            display: inline-flex;
            align-items: center;

            padding: 7px 13px;
            margin-bottom: 18px;

            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;

            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);

            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .hp-hero-title {
            max-width: 720px;

            margin: 0 0 18px;

            color: #fff;

            font-size: 44px;
            line-height: 1.13;
            font-weight: 800;

            letter-spacing: -0.035em;
        }

        .hp-hero-title span {
            display: block;
            color: #fed7aa;
        }

        .hp-hero-text {
            max-width: 650px;

            margin: 0 0 28px;

            color: rgba(255, 255, 255, 0.88);

            font-size: 15px;
            line-height: 1.8;
        }

        .hp-hero-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hp-hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-height: 44px;
            padding: 0 20px;

            border-radius: 9px;

            font-family: inherit;
            font-size: 13px;
            font-weight: 700;

            text-decoration: none;

            transition:
                transform .18s ease,
                background .18s ease,
                border-color .18s ease;
        }

        .hp-hero-btn:hover {
            transform: translateY(-2px);
        }

        .hp-hero-btn-primary {
            background: #f97316;
            color: #fff;
            box-shadow: 0 8px 24px rgba(249, 115, 22, .25);
        }

        .hp-hero-btn-primary:hover {
            background: #ea580c;
        }

        .hp-hero-btn-secondary {
            border: 1px solid rgba(255, 255, 255, .35);

            background: rgba(255, 255, 255, .1);
            color: #fff;

            backdrop-filter: blur(8px);
        }

        .hp-hero-btn-secondary:hover {
            background: rgba(255, 255, 255, .18);
            border-color: rgba(255, 255, 255, .55);
        }


        /* ══════════════════════════════════════
   SEO CONTENT
   ══════════════════════════════════════ */

        .hp-seo-content {
            background: #fff;
            border-top: 1px solid #e2e8f0;
        }

        .hp-seo-content-inner {
            max-width: 1200px;
            margin: 0 auto;

            padding: 54px 24px 60px;

            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: 50px;
        }

        .hp-seo-block {
            min-width: 0;
        }

        .hp-seo-label {
            display: inline-block;

            margin-bottom: 9px;

            color: #f97316;

            font-size: 11px;
            font-weight: 800;

            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hp-seo-title {
            margin: 0 0 14px;

            color: #0f172a;

            font-size: 23px;
            line-height: 1.35;
            font-weight: 800;

            letter-spacing: -0.02em;
        }

        .hp-seo-text {
            margin: 0 0 13px;

            color: #64748b;

            font-size: 14px;
            line-height: 1.8;
        }

        .hp-seo-text:last-child {
            margin-bottom: 0;
        }

        .hp-seo-text a {
            color: #ea580c;
            font-weight: 700;
            text-decoration: none;
        }

        .hp-seo-text a:hover {
            text-decoration: underline;
        }

        .hp-seo-text strong {
            color: #334155;
            font-weight: 700;
        }


        /* ══════════════════════════════════════
   TABLET
   ══════════════════════════════════════ */

        @media (max-width: 767px) {

            .hp-hero {
                min-height: 480px;

                background-position: center;
            }

            .hp-hero-overlay {
                background:
                    linear-gradient(180deg,
                        rgba(15, 23, 42, 0.55),
                        rgba(15, 23, 42, 0.88));
            }

            .hp-hero-inner {
                padding: 60px 20px;
            }

            .hp-hero-title {
                font-size: 34px;
                line-height: 1.18;
            }

            .hp-hero-text {
                font-size: 14px;
                line-height: 1.75;
            }

            .hp-seo-content-inner {
                grid-template-columns: 1fr;
                gap: 36px;

                padding: 42px 20px 46px;
            }

            .hp-seo-title {
                font-size: 21px;
            }
        }


        /* ══════════════════════════════════════
   MOBILE
   ══════════════════════════════════════ */

        @media (max-width: 480px) {

            .hp-hero {
                min-height: 500px;
            }

            .hp-hero-inner {
                padding: 48px 16px;
            }

            .hp-hero-badge {
                font-size: 11px;
                padding: 6px 11px;
                margin-bottom: 14px;
            }

            .hp-hero-title {
                font-size: 29px;
                line-height: 1.18;
                letter-spacing: -0.025em;
            }

            .hp-hero-text {
                font-size: 13px;
                line-height: 1.7;
                margin-bottom: 22px;
            }

            .hp-hero-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .hp-hero-btn {
                width: 100%;
            }

            .hp-seo-content-inner {
                padding: 36px 16px 42px;
                gap: 30px;
            }

            .hp-seo-title {
                font-size: 20px;
            }

            .hp-seo-text {
                font-size: 13px;
                line-height: 1.75;
            }
        }

        /* ── Google Maps Section ── */
        .hp-maps-section {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .hp-maps-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px 48px;
        }

        .hp-maps-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }

        .hp-maps-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .hp-maps-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .hp-maps-subtitle {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        .hp-maps-frame-wrap {
            position: relative;
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
            /* Aspek rasio 16:7 di desktop, 4:3 di mobile */
            aspect-ratio: 16 / 7;
        }

        .hp-maps-frame-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
        }

        .hp-maps-cta {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hp-maps-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: #f97316;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s;
        }

        .hp-maps-link:hover {
            background: #ea580c;
        }

        .hp-maps-address {
            font-size: 12.5px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 767px) {
            .hp-maps-inner {
                padding: 32px 20px 40px;
            }

            .hp-maps-title {
                font-size: 16px;
            }

            .hp-maps-frame-wrap {
                aspect-ratio: 4 / 3;
            }
        }

        @media (max-width: 480px) {
            .hp-maps-inner {
                padding: 28px 16px 36px;
            }

            .hp-maps-header {
                gap: 10px;
            }

            .hp-maps-icon {
                width: 36px;
                height: 36px;
                border-radius: 9px;
            }

            .hp-maps-frame-wrap {
                aspect-ratio: 3 / 4;
            }
        }

        /* ── Footer ── */
        .hp-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
        }

        .hp-footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hp-footer-brand {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .hp-footer-copy {
            font-size: 13px;
            color: #94a3b8;
        }

        .hp-footer-contact {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #94a3b8;
        }

        input[type=date]::-webkit-calendar-picker-indicator {
            opacity: .6;
            cursor: pointer;
        }
    </style>
</head>

<body class="hp-body">

    {{-- ══ NAVBAR ══ --}}
    <nav class="hp-nav">
        <div class="hp-nav-inner">
            <a href="{{ route('home') }}" class="hp-logo">
                <div class="hp-logo-mark">
                    <img src="/public/sptLOGO.png" alt="logo" width="20" height="20">
                </div>
                <span class="hp-logo-text">Semeton Pesiar Trans</span>
            </a>
        </div>
    </nav>
    {{-- ══ JUMBOTRON ══ --}}
    <section class="hp-hero">

        <div class="hp-hero-overlay"></div>

        <div class="hp-hero-inner">
            <span class="hp-hero-badge">
                Rental Mobil Lombok
            </span>

            <h1 class="hp-hero-title">
                Sewa Kendaraan di Lombok
                <span>Harga Murah, Armada Lengkap</span>
            </h1>

            <p class="hp-hero-text">
                Nikmati perjalanan di Lombok dengan armada terawat,
                harga transparan, dan pilihan layanan lepas kunci maupun
                dengan driver berpengalaman.
            </p>

            <div class="hp-hero-actions">
                <a href="#kendaraan" class="hp-hero-btn hp-hero-btn-primary">
                    Lihat Armada
                </a>

                <a href="https://wa.me/6281128948884" target="_blank" rel="noopener noreferrer"
                    class="hp-hero-btn hp-hero-btn-secondary">
                    Hubungi Kami
                </a>
            </div>
        </div>

    </section>

    {{-- ══ SEARCH / FILTER BAR ══ --}}
    <div class="hp-searchbar">
        <div class="hp-searchbar-inner">
            @php $tipeSewa = request('tipe_sewa', 'semua'); @endphp

            <form method="GET" action="{{ route('home') }}" id="search-form">

                {{-- Hidden inputs — satu per parameter, tidak ada duplikat --}}
                <input type="hidden" name="tipe_sewa" id="hidden-tipe-sewa" value="{{ $tipeSewa }}">
                <input type="hidden" name="transmisi" id="hidden-transmisi" value="{{ request('transmisi', 'semua') }}">
                <input type="hidden" name="sort" id="hidden-sort" value="{{ request('sort', 'termurah') }}">

                {{-- Toggle Lepas Kunci / Dengan Sopir --}}
                <div class="hp-toggle-row">
                    <button type="button" onclick="setFilter('tipe_sewa', 'lepas_kunci')"
                        class="hp-tipe-btn {{ $tipeSewa === 'lepas_kunci' ? 'active' : '' }}">
                        <span class="hp-tipe-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 9.9-1" />
                            </svg>
                        </span>
                        Lepas kunci
                    </button>
                    <button type="button" onclick="setFilter('tipe_sewa', 'dengan_sopir')"
                        class="hp-tipe-btn {{ $tipeSewa === 'dengan_sopir' ? 'active' : '' }}">
                        <span class="hp-tipe-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        Dengan sopir
                    </button>
                </div>

                {{-- Tanggal + Ubah --}}
                <div class="hp-date-row">

                    <div class="hp-date-top">
                        <div class="hp-date-loc">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <div>
                                <p class="hp-date-micro">Lokasi</p>
                                <p class="hp-date-val">Lombok</p>
                            </div>
                        </div>
                        {{-- Tombol Ubah mobile (disembunyikan di ≥768px via CSS) --}}
                        <button type="submit" class="hp-ubah-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Ubah
                        </button>
                    </div>

                    <div class="hp-date-fields">
                        <div class="hp-date-field">
                            <p class="hp-date-micro">Tanggal Keluar</p>
                            <input type="date" name="tanggal_keluar" id="tgl_keluar"
                                value="{{ request('tanggal_keluar', now()->format('Y-m-d')) }}"
                                min="{{ now()->format('Y-m-d') }}" class="hp-date-input" onchange="updateMinReturn()">
                        </div>

                        <div class="hp-date-arrow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </div>

                        <div class="hp-date-field">
                            <p class="hp-date-micro">Tanggal Kembali</p>
                            <input type="date" name="tanggal_kembali" id="tgl_kembali"
                                value="{{ request('tanggal_kembali', now()->addDay()->format('Y-m-d')) }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}" class="hp-date-input">
                        </div>
                    </div>

                    {{-- Tombol Ubah desktop (disembunyikan di <768px via CSS) --}} <button type="submit"
                        class="hp-ubah-btn hp-ubah-btn-desktop">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Ubah
                        </button>

                </div>

                {{-- Search nama --}}
                <div class="hp-search-row">
                    <div class="hp-search-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input type="text" name="search" placeholder="Cari nama kendaraan, mis. Fortuner"
                            value="{{ request('search') }}" class="hp-search-input">
                    </div>
                    <button type="submit" class="hp-search-btn">Cari</button>
                </div>

            </form>
        </div>
    </div>

    {{-- ══ FILTER TRANSMISI + SORT ══ --}}
    <div class="hp-filter-bar">
        <div class="hp-filter-inner">
            <div class="hp-filter-group">
                <span class="hp-filter-label">Transmisi</span>
                @foreach(['semua' => 'Semua', 'AT' => 'Matic (AT)', 'MT' => 'Manual (MT)'] as $val => $label)
                    <button type="button" onclick="setFilter('transmisi', '{{ $val }}')"
                        class="hp-chip {{ request('transmisi', 'semua') === $val ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="hp-filter-group">
                <span class="hp-filter-label">Urutkan</span>
                <select onchange="setFilter('sort', this.value)" class="hp-sort-select">
                    <option value="termurah" {{ request('sort', 'termurah') === 'termurah' ? 'selected' : '' }}>Harga
                        termurah</option>
                    <option value="termahal" {{ request('sort', 'termurah') === 'termahal' ? 'selected' : '' }}>Harga
                        termahal</option>
                    <option value="terbaru" {{ request('sort', 'termurah') === 'terbaru' ? 'selected' : '' }}>Terbaru
                    </option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══ GRID KARTU ══ --}}
    <div class="hp-grid-wrap" id="kendaraan">

        <div class="hp-grid-header">
            <h2 class="hp-grid-title">Kendaraan tersedia</h2>
            <span class="hp-grid-count">
                {{ $cars->total() }} kendaraan
                @if(request('tipe_sewa') === 'lepas_kunci') · Lepas Kunci
                @elseif(request('tipe_sewa') === 'dengan_sopir') · Dengan Sopir
                @endif
            </span>
        </div>

        @if($cars->isEmpty())
            <div class="hp-empty">
                <div class="hp-empty-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13" rx="2" />
                        <path d="m16 8 5 1 2 4v3h-2" />
                        <circle cx="5.5" cy="18.5" r="2.5" />
                        <circle cx="18.5" cy="18.5" r="2.5" />
                    </svg>
                </div>
                <h3 class="hp-empty-title">Tidak ada kendaraan ditemukan</h3>
                <p class="hp-empty-sub">Coba ubah filter pencarian Anda.</p>
                <a href="{{ route('home') }}" class="hp-empty-reset">Reset Filter</a>
            </div>

        @else
            <div class="hp-grid">
                @foreach($cars as $car)
                    @php
                        $harga = $car['harga_aktif'] ?? 0;
                        $totalHarga = $harga * $totalHari;
                        $transmisi = strtoupper($car['transmisi']);
                        $badgeStyles = [
                            'orange' => 'badge-orange',
                            'green' => 'badge-green',
                            'blue' => 'badge-blue',
                            'purple' => 'badge-purple',
                            'gray' => 'badge-gray',
                        ];
                        $badgeClass = $badgeStyles[$car['badge_color'] ?? 'gray'] ?? 'badge-gray';
                        $bisaLepasKunci = in_array('lepas_kunci', $car['tipe_sewa'] ?? []);
                        $bisaDenganSopir = in_array('dengan_sopir', $car['tipe_sewa'] ?? []);
                    @endphp

                    <div class="hp-card">

                        {{-- Gambar --}}
                        <div class="hp-card-img-wrap">
                            <img src="{{ $car['foto'] }}" alt="{{ $car['brand'] }} {{ $car['nama'] }}" class="hp-card-img"
                                onerror="this.src='https://placehold.co/400x300/f3f4f6/9ca3af?text=No+Image'">

                            @if(!empty($car['badge']))
                                <span class="hp-badge {{ $badgeClass }}">{{ $car['badge'] }}</span>
                            @endif

                            <span class="hp-badge-tr">{{ $transmisi }}</span>

                            <span class="hp-badge-bl">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                {{ $car['kapasitas'] }} orang
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="hp-card-body">
                            <p class="hp-card-brand">{{ $car['brand'] }}</p>
                            <h3 class="hp-card-name">{{ strtoupper($car['nama']) }}</h3>

                            <div class="hp-card-chips">
                                @if($bisaLepasKunci)
                                    <span class="hp-chip-tipe hp-chip-tipe--kunci">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5">
                                            <rect x="3" y="11" width="18" height="11" rx="2" />
                                            <path d="M7 11V7a5 5 0 0 1 9.9-1" />
                                        </svg>
                                        Lepas kunci
                                    </span>
                                @endif
                                @if($bisaDenganSopir)
                                    <span class="hp-chip-tipe hp-chip-tipe--sopir">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        Dengan sopir
                                    </span>
                                @endif
                            </div>

                            <div class="hp-card-price-row">
                                <span class="hp-card-price">Rp {{ number_format($harga, 0, ',', '.') }}</span>
                                <span class="hp-card-per">/hari</span>
                            </div>
                            <div class="hp-card-total">
                                {{ $totalHari }} hari ·
                                <strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
                            </div>

                            <button type="button"
                                onclick="openNikModal({{ $car['id'] }}, '{{ addslashes($car['brand'] . ' ' . $car['nama']) }}')"
                                class="hp-card-btn">
                                Pilih kendaraan ini
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ══ PAGINATION ══ --}}
            @if($cars->hasPages())
                <div class="hp-pagination">
                    {{ $cars->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif

    </div>

    <section class="hp-seo-content">

        <div class="hp-seo-content-inner">

            <div class="hp-seo-block">
                <span class="hp-seo-label">
                    SEMETON PESIAR TRANS
                </span>

                <h2 class="hp-seo-title">
                    Rental Mobil Lombok untuk Perjalanan Nyaman
                </h2>

                <p class="hp-seo-text">
                    <a href="https://adminsemetonpesiarlombok.id/">
                        Semeton Pesiar Trans
                    </a>
                    hadir sebagai solusi perjalanan yang mengutamakan
                    kenyamanan dan pelayanan prima. Sebagai penyedia jasa
                    sewa mobil dan paket Tour & Travel di Lombok, kami
                    menyediakan berbagai pilihan kendaraan dengan kondisi
                    terawat dan harga yang kompetitif.
                </p>

                <p class="hp-seo-text">
                    Kami melayani kebutuhan perjalanan di berbagai wilayah
                    Lombok, termasuk Mataram, Senggigi, Kuta Lombok dan
                    berbagai destinasi wisata lainnya.
                </p>
            </div>


            <div class="hp-seo-block">

                <span class="hp-seo-label">
                    PILIHAN LAYANAN
                </span>

                <h2 class="hp-seo-title">
                    Sewa Mobil Lepas Kunci atau Dengan Driver
                </h2>

                <p class="hp-seo-text">
                    Bagi Anda yang menginginkan privasi dan kebebasan dalam
                    menjelajahi Lombok, tersedia layanan sewa mobil
                    <strong>lepas kunci</strong>.
                </p>

                <p class="hp-seo-text">
                    Sementara bagi Anda yang ingin menikmati perjalanan tanpa
                    perlu memikirkan rute dan perjalanan, tersedia layanan
                    <strong>dengan driver berpengalaman</strong>.
                </p>

            </div>

        </div>

    </section>

    {{-- ══ GOOGLE MAPS ══ --}}
    <section class="hp-maps-section">
        <div class="hp-maps-inner">

            <div class="hp-maps-header">
                <div class="hp-maps-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </div>
                <div>
                    <p class="hp-maps-title">Lokasi Kami</p>
                    <p class="hp-maps-subtitle">
                        Semeton Pesiar Trans — Sewa Mobil Lombok Terpercaya
                    </p>
                </div>
            </div>

            <div class="hp-maps-frame-wrap">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.964101425894!2d116.08530180000001!3d-8.599445099999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf0264047f73%3A0x5f1a0bbef364f13!2sSemeton%20Pesiar%20-%20Sewa%20Mobil%20Lombok%20Terpercaya!5e0!3m2!1sid!2sid!4v1789729563043!5m2!1sid!2sid"
                    allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                    title="Lokasi Semeton Pesiar Trans di Google Maps">
                </iframe>
            </div>

            <div class="hp-maps-cta">
                <a href="https://maps.app.goo.gl/b4rduAQFoAvEmCBu5" target="_blank" rel="noopener noreferrer"
                    class="hp-maps-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Buka di Google Maps
                </a>
                <span class="hp-maps-address">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Lombok, Nusa Tenggara Barat
                </span>
            </div>

        </div>
    </section>

    {{-- ══ MODAL CEK NIK ══ --}}
    <div id="nikModal" class="hp-modal-backdrop">
        <div class="hp-modal" id="nikModalBox">

            <div class="hp-modal-header">
                <div>
                    <p class="hp-modal-step">Langkah berikutnya</p>
                    <h3 class="hp-modal-title">Cek Data Penyewa</h3>
                </div>
                <button type="button" onclick="closeNikModal()" class="hp-modal-close" aria-label="Tutup modal">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="hp-modal-body">

                <div class="hp-modal-car">
                    <p class="hp-modal-car-label">Kendaraan yang dipilih</p>
                    <p id="selectedCarName" class="hp-modal-car-name">-</p>
                </div>

                <div class="hp-modal-field">
                    <label for="modalNik" class="hp-modal-label">Nomor Induk Kependudukan (NIK)</label>
                    <div class="hp-modal-input-wrap">
                        <input type="text" id="modalNik" maxlength="16" inputmode="numeric" autocomplete="off"
                            placeholder="Masukkan 16 digit NIK" class="hp-modal-input">
                        <span id="modalNikCounter" class="hp-modal-counter">0 / 16</span>
                    </div>
                    <p class="hp-modal-hint">NIK digunakan untuk mengecek status data penyewa sebelum booking.</p>
                </div>

                <div id="nikError" class="hp-modal-alert hp-modal-alert--error" style="display:none"></div>
                <div id="nikSuccess" class="hp-modal-alert hp-modal-alert--success" style="display:none"></div>

                <button type="button" id="btnCheckNik" onclick="checkNik()" class="hp-modal-submit">
                    <svg id="checkNikIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <span id="checkNikText">Cek NIK</span>
                </button>

            </div>
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <footer class="hp-footer">
        <div class="hp-footer-inner">
            <div class="hp-footer-brand">
                <div class="hp-logo-mark">
                    <img src="/public/sptLOGO.png" alt="logo" width="20" height="20">
                </div>
                <span class="hp-footer-copy">© {{ date('Y') }} Semeton Pesiar Trans</span>
            </div>
            <div class="hp-footer-contact">
                <span>📞 +6281128948884</span>
                <span>🌐 www.semetonpesiar.com</span>
            </div>
        </div>
    </footer>

    <script>
        // ── Tanggal ──────────────────────────────────────────────────
        function updateMinReturn() {
            const keluar = document.getElementById('tgl_keluar');
            const kembali = document.getElementById('tgl_kembali');
            if (!keluar.value) return;

            const next = new Date(keluar.value);
            next.setDate(next.getDate() + 1);
            const minVal = next.toISOString().split('T')[0];

            kembali.min = minVal;
            if (kembali.value <= keluar.value) kembali.value = minVal;
        }

        // ── Filter ───────────────────────────────────────────────────
        const hiddenInputIds = {
            tipe_sewa: 'hidden-tipe-sewa',
            transmisi: 'hidden-transmisi',
            sort: 'hidden-sort',
        };

        function setFilter(name, value) {
            const id = hiddenInputIds[name];
            if (id) document.getElementById(id).value = value;
            document.getElementById('search-form').submit();
        }

        updateMinReturn();

        // ── Modal NIK ────────────────────────────────────────────────
        let selectedCarId = null;

        function openNikModal(carId, carName) {
            selectedCarId = carId;
            document.getElementById('selectedCarName').textContent = carName;
            document.getElementById('modalNik').value = '';
            document.getElementById('modalNikCounter').textContent = '0 / 16';
            document.getElementById('nikError').style.display = 'none';
            document.getElementById('nikSuccess').style.display = 'none';
            document.getElementById('nikModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('modalNik').focus(), 100);
        }

        function closeNikModal() {
            document.getElementById('nikModal').classList.remove('show');
            document.body.style.overflow = '';
            selectedCarId = null;
        }

        document.getElementById('nikModal').addEventListener('click', function (e) {
            if (e.target === this) closeNikModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeNikModal();
        });

        document.getElementById('modalNik').addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            document.getElementById('modalNikCounter').textContent = `${this.value.length} / 16`;
        });

        document.getElementById('modalNik').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') checkNik();
        });

        // ── Cek NIK ──────────────────────────────────────────────────
        async function checkNik() {
            const nik = document.getElementById('modalNik').value.trim();
            const errorBox = document.getElementById('nikError');
            const successBox = document.getElementById('nikSuccess');
            const button = document.getElementById('btnCheckNik');
            const buttonText = document.getElementById('checkNikText');

            errorBox.style.display = 'none';
            successBox.style.display = 'none';

            if (nik.length !== 16) {
                errorBox.textContent = 'NIK harus terdiri dari 16 digit.';
                errorBox.style.display = 'block';
                return;
            }

            if (!selectedCarId) {
                errorBox.textContent = 'Kendaraan belum dipilih.';
                errorBox.style.display = 'block';
                return;
            }

            button.disabled = true;
            buttonText.textContent = 'Memeriksa…';

            try {
                const response = await fetch("{{ route('cek.nik.ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        nik,
                        car_id: selectedCarId,
                        tanggal_keluar: document.getElementById('tgl_keluar').value,
                        tanggal_kembali: document.getElementById('tgl_kembali').value,
                        tipe_sewa: document.getElementById('hidden-tipe-sewa').value,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    errorBox.textContent = data.message ?? 'NIK tidak dapat digunakan.';
                    errorBox.style.display = 'block';
                    return;
                }

                successBox.textContent = data.message ?? 'NIK berhasil diverifikasi.';
                successBox.style.display = 'block';

                setTimeout(() => {
                    const base = data.registered
                        ? "{{ route('booking.form') }}"
                        : "{{ route('data.penyewa') }}";

                    const params = new URLSearchParams(
                        data.registered
                            ? {
                                customer_id: data.customer_id,
                                car_id: selectedCarId,
                                tanggal_keluar: document.getElementById('tgl_keluar').value,
                                tanggal_kembali: document.getElementById('tgl_kembali').value,
                                tipe_sewa: document.getElementById('hidden-tipe-sewa').value,
                            }
                            : {
                                ktp: nik,
                                car_id: selectedCarId,
                                tanggal_keluar: document.getElementById('tgl_keluar').value,
                                tanggal_kembali: document.getElementById('tgl_kembali').value,
                                tipe_sewa: document.getElementById('hidden-tipe-sewa').value,
                            }
                    );

                    window.location.href = base + '?' + params.toString();
                }, 700);

            } catch (err) {
                console.error(err);
                errorBox.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                errorBox.style.display = 'block';
            } finally {
                button.disabled = false;
                buttonText.textContent = 'Cek NIK';
            }
        }
    </script>

</body>

</html>
