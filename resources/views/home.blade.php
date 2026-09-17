<!DOCTYPE html>
<html lang="id">

<head>
    @php
        $isSeoLanding = ! request()->hasAny([
            'search', 'transmisi', 'sort', 'tipe_sewa',
            'tanggal_keluar', 'tanggal_kembali', 'page'
        ]);

        $canonicalUrl = url('/');
        $seoTitle = 'Sewa Mobil Lombok – Lepas Kunci & Dengan Sopir | Semeton Pesiar';
        $seoDescription = 'Sewa mobil Lombok di Semeton Pesiar. Pilihan rental mobil lepas kunci dan dengan sopir, berbagai armada, harga transparan, serta booking online untuk perjalanan di Lombok.';
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $seoDescription }}">
    @if($isSeoLanding)
        <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    @else
        <meta name="robots" content="noindex,follow">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <title>{{ $seoTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset('sptLOGO.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Semeton Pesiar Lombok">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('sptLOGO.png') }}">
    <meta property="og:image:alt" content="Semeton Pesiar Lombok – Rental Mobil Lombok">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ asset('sptLOGO.png') }}">
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
            background: linear-gradient(135deg, #f97316, #ea580c);
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

            /* Sembunyikan tombol Ubah mobile saat desktop */
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

        /* Tombol ubah versi desktop */
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

        .badge-orange { background: #ffedd5; color: #c2410c; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }
        .badge-gray   { background: #f1f5f9; color: #475569; }

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

        .hp-pagination nav > div:first-child {
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
            color: #fff;
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

        /* BUG FIX: pakai .show bukan .hidden untuk konsistensi — JS toggle .show */
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
            from { opacity: 0; transform: scale(.95) translateY(12px); }
            to   { opacity: 1; transform: scale(1)   translateY(0);    }
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
        input[type=date]::-webkit-calendar-picker-indicator {
            opacity: .6;
            cursor: pointer;
        }

        /* ── SEO LANDING CONTENT ── */
        .seo-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 56px;
        }

        .seo-hero {
            padding: 44px 0 22px;
        }

        .seo-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .seo-hero h1 {
            max-width: 860px;
            font-size: clamp(30px, 5vw, 48px);
            line-height: 1.08;
            letter-spacing: -.035em;
            color: #0f172a;
            font-weight: 800;
        }

        .seo-hero-lead {
            max-width: 820px;
            margin-top: 16px;
            font-size: 16px;
            line-height: 1.75;
            color: #475569;
        }

        .seo-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .seo-btn-primary, .seo-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 17px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .seo-btn-primary {
            background: #f97316;
            color: #fff;
        }

        .seo-btn-secondary {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }

        .seo-section {
            padding: 28px 0 0;
        }

        .seo-section-head {
            max-width: 800px;
            margin-bottom: 16px;
        }

        .seo-section h2 {
            font-size: clamp(23px, 3vw, 30px);
            line-height: 1.2;
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .seo-section h3 {
            font-size: 16px;
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .seo-section p {
            color: #64748b;
            line-height: 1.75;
            font-size: 14px;
        }

        .seo-grid-2, .seo-grid-3 {
            display: grid;
            gap: 14px;
        }

        .seo-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .seo-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .seo-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
        }

        .seo-card-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-bottom: 12px;
            background: #fff7ed;
            color: #ea580c;
        }

        .seo-list {
            display: grid;
            gap: 8px;
            margin-top: 12px;
            padding-left: 18px;
        }

        .seo-list li {
            color: #475569;
            line-height: 1.7;
            font-size: 14px;
        }

        .seo-location-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .seo-location-links span {
            color: #334155;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .seo-steps {
            counter-reset: seo-step;
        }

        .seo-step {
            position: relative;
            padding-left: 50px;
        }

        .seo-step::before {
            counter-increment: seo-step;
            content: counter(seo-step);
            position: absolute;
            left: 0;
            top: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f97316;
            color: #fff;
            font-weight: 800;
            font-size: 13px;
        }

        .seo-faq {
            border-top: 1px solid #e2e8f0;
        }

        .seo-faq details {
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 0;
        }

        .seo-faq summary {
            cursor: pointer;
            list-style: none;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            padding-right: 24px;
            position: relative;
        }

        .seo-faq summary::-webkit-details-marker {
            display: none;
        }

        .seo-faq summary::after {
            content: '+';
            position: absolute;
            right: 0;
            top: -1px;
            color: #f97316;
            font-size: 20px;
            line-height: 1;
        }

        .seo-faq details[open] summary::after {
            content: '−';
        }

        .seo-faq details p {
            margin-top: 9px;
            max-width: 900px;
        }

        .seo-cta {
            margin-top: 28px;
            padding: 24px;
            border-radius: 16px;
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border: 1px solid #fed7aa;
        }

        @media (max-width: 767px) {
            .seo-wrap { padding-left: 16px; padding-right: 16px; }
            .seo-hero { padding-top: 30px; }
            .seo-grid-2, .seo-grid-3 { grid-template-columns: 1fr; }
            .seo-hero-actions { flex-direction: column; }
            .seo-btn-primary, .seo-btn-secondary { width: 100%; }
        }
    </style>

    @if($isSeoLanding)
    {{-- Structured data: Organization / LocalBusiness --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "{{ $canonicalUrl }}#business",
        "name": "Semeton Pesiar Lombok",
        "url": "{{ $canonicalUrl }}",
        "telephone": "+6281128948884",
        "description": @json($seoDescription),
        "image": "{{ asset('sptLOGO.png') }}",
        "priceRange": "Rp",
        "areaServed": [
            {"@type": "AdministrativeArea", "name": "Lombok"},
            {"@type": "City", "name": "Mataram"},
            {"@type": "Place", "name": "Bandara Internasional Lombok"},
            {"@type": "Place", "name": "Kuta Mandalika"},
            {"@type": "Place", "name": "Senggigi"}
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "@id": "{{ $canonicalUrl }}#website",
        "url": "{{ $canonicalUrl }}",
        "name": "Semeton Pesiar Lombok",
        "inLanguage": "id-ID",
        "publisher": {"@id": "{{ $canonicalUrl }}#business"}
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "{{ $canonicalUrl }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Sewa Mobil Lombok",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "Berapa harga sewa mobil di Lombok?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Harga sewa mobil Lombok menyesuaikan jenis armada, durasi sewa, dan paket yang dipilih. Lihat harga pada daftar kendaraan di halaman ini dan pilih tanggal perjalanan untuk menghitung totalnya."
                }
            },
            {
                "@type": "Question",
                "name": "Apakah tersedia sewa mobil Lombok lepas kunci?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Semeton Pesiar menyediakan pilihan kendaraan dengan paket lepas kunci untuk armada yang memiliki keterangan tersebut. Pilihan tersedia dapat dilihat melalui filter Lepas Kunci."
                }
            },
            {
                "@type": "Question",
                "name": "Apakah bisa sewa mobil dengan sopir di Lombok?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Tersedia pilihan dengan sopir pada kendaraan yang menampilkan label Dengan Sopir. Gunakan filter Dengan Sopir untuk melihat armada yang tersedia."
                }
            },
            {
                "@type": "Question",
                "name": "Apakah bisa booking rental mobil untuk beberapa hari?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Bisa. Pilih tanggal keluar dan tanggal kembali pada form pencarian, kemudian sistem akan menghitung total durasi dan harga berdasarkan kendaraan yang dipilih."
                }
            },
            {
                "@type": "Question",
                "name": "Bagaimana cara booking rental mobil di Semeton Pesiar?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Pilih tanggal, filter kebutuhan sewa, pilih kendaraan, lalu lakukan pengecekan NIK untuk melanjutkan ke form booking."
                }
            }
        ]
    }
    </script>
    @endif

</head>

<body class="hp-body">

    {{-- ══ NAVBAR ══ --}}
    <nav class="hp-nav">
        <div class="hp-nav-inner">
            {{-- BUG FIX: href="" → href="{{ route('home') }}" --}}
            <a href="{{ route('home') }}" class="hp-logo">
                <div class="hp-logo-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z" />
                        <circle cx="7.5" cy="17.5" r="1.5" />
                        <circle cx="16.5" cy="17.5" r="1.5" />
                    </svg>
                </div>
                <span class="hp-logo-text">Semeton Pesiar Lombok</span>
            </a>
        </div>
    </nav>

    @if($isSeoLanding)
    {{-- ══ SEO LANDING HERO ══ --}}
    <section class="seo-wrap seo-hero" aria-labelledby="seo-main-title">
        <span class="seo-eyebrow">Rental Mobil Lombok</span>
        <h1 id="seo-main-title">Sewa Mobil Lombok – Lepas Kunci &amp; Dengan Sopir</h1>
        <p class="seo-hero-lead">
            Semeton Pesiar menyediakan rental mobil di Lombok untuk liburan, perjalanan keluarga,
            perjalanan bisnis, dan kebutuhan transportasi selama berada di Pulau Lombok.
            Pilih kendaraan, tentukan tanggal sewa, lalu lanjutkan booking secara online.
        </p>
        <div class="seo-hero-actions">
            <a href="#armada" class="seo-btn-primary">Lihat Armada &amp; Harga</a>
            <a href="#cara-booking" class="seo-btn-secondary">Cara Booking</a>
        </div>
    </section>
    @endif

    {{-- ══ SEARCH / FILTER BAR ══ --}}
    <div class="hp-searchbar">
        <div class="hp-searchbar-inner">
            {{--
                BUG FIX: Hapus hidden input tipe_sewa duplikat di bagian bawah form.
                Nilai tipe_sewa dikelola satu tempat saja via setFilter() yang update
                hidden input ini, sehingga tidak ada konflik nilai ganda saat submit.
            --}}
            @php $tipeSewa = request('tipe_sewa', 'semua'); @endphp

            <form method="GET" action="{{ route('home') }}" id="search-form">

                {{-- Hidden inputs — satu per parameter, tidak ada duplikat --}}
                <input type="hidden" name="tipe_sewa"  id="hidden-tipe-sewa"  value="{{ $tipeSewa }}">
                <input type="hidden" name="transmisi"  id="hidden-transmisi"  value="{{ request('transmisi', 'semua') }}">
                <input type="hidden" name="sort"       id="hidden-sort"       value="{{ request('sort', 'termurah') }}">

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
                                min="{{ now()->format('Y-m-d') }}"
                                class="hp-date-input"
                                onchange="updateMinReturn()">
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
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="hp-date-input">
                        </div>
                    </div>

                    {{-- Tombol Ubah desktop (disembunyikan di <768px via CSS) --}}
                    <button type="submit" class="hp-ubah-btn hp-ubah-btn-desktop">
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
                    <button onclick="setFilter('transmisi', '{{ $val }}')"
                        class="hp-chip {{ request('transmisi', 'semua') === $val ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="hp-filter-group">
                <span class="hp-filter-label">Urutkan</span>
                <select onchange="setFilter('sort', this.value)" class="hp-sort-select">
                    <option value="termurah" {{ request('sort', 'termurah') === 'termurah' ? 'selected' : '' }}>Harga termurah</option>
                    <option value="termahal" {{ request('sort', 'termurah') === 'termahal' ? 'selected' : '' }}>Harga termahal</option>
                    <option value="terbaru"  {{ request('sort', 'termurah') === 'terbaru'  ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══ GRID KARTU ══ --}}
    <div class="hp-grid-wrap" id="armada">

        @if($isSeoLanding)
        <div class="seo-section" style="padding-top: 0; padding-bottom: 18px;">
            <div class="seo-section-head">
                <h2>Rental Mobil Lombok yang Bisa Anda Pilih</h2>
                <p>
                    Temukan berbagai pilihan mobil rental di Lombok dengan informasi harga per hari, transmisi,
                    kapasitas penumpang, serta pilihan lepas kunci atau dengan sopir. Gunakan filter dan tanggal
                    untuk menyesuaikan kendaraan dengan kebutuhan perjalanan Anda.
                </p>
            </div>
        </div>
        @endif

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
                        $harga      = $car['harga_aktif'] ?? 0;
                        $totalHarga = $harga * $totalHari;
                        $transmisi  = strtoupper($car['transmisi']);
                        $badgeStyles = [
                            'orange' => 'badge-orange',
                            'green'  => 'badge-green',
                            'blue'   => 'badge-blue',
                            'purple' => 'badge-purple',
                            'gray'   => 'badge-gray',
                        ];
                        $badgeClass      = $badgeStyles[$car['badge_color'] ?? 'gray'] ?? 'badge-gray';
                        $bisaLepasKunci  = in_array('lepas_kunci',   $car['tipe_sewa']);
                        $bisaDenganSopir = in_array('dengan_sopir',  $car['tipe_sewa']);
                    @endphp

                    <div class="hp-card">

                        {{-- Gambar --}}
                        <div class="hp-card-img-wrap">
                            <img src="{{ $car['foto'] }}"
                                 alt="Sewa {{ $car['brand'] }} {{ $car['nama'] }} di Lombok"
                                 class="hp-card-img"
                                 width="400"
                                 height="300"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.src='https://placehold.co/400x300/f3f4f6/9ca3af?text=No+Image'">

                            @if($car['badge'])
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
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <rect x="3" y="11" width="18" height="11" rx="2" />
                                            <path d="M7 11V7a5 5 0 0 1 9.9-1" />
                                        </svg>
                                        Lepas kunci
                                    </span>
                                @endif
                                @if($bisaDenganSopir)
                                    <span class="hp-chip-tipe hp-chip-tipe--sopir">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                                onclick="openNikModal({{ $car['id'] }}, '{{ $car['brand'] }} {{ $car['nama'] }}')"
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

    @if($isSeoLanding)
    {{-- ══ SEO CONTENT: LAYANAN ══ --}}
    <section class="seo-wrap" aria-labelledby="layanan-title">
        <div class="seo-section">
            <div class="seo-section-head">
                <h2 id="layanan-title">Pilihan Sewa Mobil di Lombok</h2>
                <p>Pilih jenis layanan berdasarkan kebutuhan perjalanan Anda.</p>
            </div>

            <div class="seo-grid-3">
                <article class="seo-card">
                    <div class="seo-card-icon">🚗</div>
                    <h3>Sewa Mobil Lepas Kunci</h3>
                    <p>
                        Cocok untuk Anda yang ingin lebih fleksibel menentukan rute dan waktu perjalanan sendiri.
                        Gunakan filter <strong>Lepas Kunci</strong> untuk melihat kendaraan yang tersedia.
                    </p>
                </article>

                <article class="seo-card">
                    <div class="seo-card-icon">👨‍✈️</div>
                    <h3>Rental Mobil Dengan Sopir</h3>
                    <p>
                        Pilihan untuk wisata, perjalanan keluarga, kebutuhan bisnis, dan perjalanan antarlokasi di Lombok
                        tanpa harus mengemudi sendiri.
                    </p>
                </article>

                <article class="seo-card">
                    <div class="seo-card-icon">🕐</div>
                    <h3>Sewa Berdasarkan Durasi</h3>
                    <p>
                        Tentukan tanggal keluar dan tanggal kembali pada pencarian untuk menyesuaikan durasi sewa
                        dengan itinerary perjalanan Anda.
                    </p>
                </article>
            </div>
        </div>

        {{-- ══ HARGA / NILAI ══ --}}
        <div class="seo-section">
            <div class="seo-grid-2">
                <article class="seo-card">
                    <h3>Harga Sewa Mobil Lombok</h3>
                    <p>
                        Harga yang tampil pada kartu kendaraan merupakan harga aktif per hari. Total perjalanan
                        dihitung berdasarkan jumlah hari yang Anda pilih. Karena harga dan ketersediaan dapat berubah,
                        gunakan tanggal perjalanan untuk melihat informasi yang sedang berlaku.
                    </p>
                    <ul class="seo-list">
                        <li>Pilih tanggal keluar dan tanggal kembali.</li>
                        <li>Gunakan filter transmisi Matic atau Manual.</li>
                        <li>Pilih paket Lepas Kunci atau Dengan Sopir.</li>
                        <li>Lihat estimasi total berdasarkan durasi sewa.</li>
                    </ul>
                </article>

                <article class="seo-card">
                    <h3>Kenapa Memilih Semeton Pesiar?</h3>
                    <p>
                        Proses pencarian dibuat sederhana agar pelanggan bisa memilih kendaraan terlebih dahulu
                        sebelum melanjutkan ke pemeriksaan data penyewa dan form booking.
                    </p>
                    <ul class="seo-list">
                        <li>Katalog kendaraan dengan harga per hari.</li>
                        <li>Filter berdasarkan kebutuhan sewa dan transmisi.</li>
                        <li>Booking online dengan tanggal perjalanan.</li>
                        <li>Pengecekan NIK sebelum melanjutkan pemesanan.</li>
                    </ul>
                </article>
            </div>
        </div>

        {{-- ══ AREA LAYANAN ══ --}}
        <div class="seo-section" aria-labelledby="lokasi-title">
            <div class="seo-card">
                <h2 id="lokasi-title">Layanan Rental Mobil di Lombok</h2>
                <p>
                    Semeton Pesiar melayani kebutuhan rental mobil untuk perjalanan di berbagai area populer di Lombok.
                    Saat menghubungi admin, sampaikan lokasi penjemputan, tujuan, dan tanggal perjalanan agar kebutuhan
                    transportasi dapat diproses sesuai layanan yang tersedia.
                </p>
                <div class="seo-location-links" aria-label="Area layanan Lombok">
                    <span>Mataram</span>
                    <span>Bandara Internasional Lombok</span>
                    <span>Kuta Mandalika</span>
                    <span>Senggigi</span>
                    <span>Lombok Barat</span>
                    <span>Lombok Tengah</span>
                    <span>Lombok Timur</span>
                    <span>Lombok Utara</span>
                </div>
            </div>
        </div>

        {{-- ══ CARA BOOKING ══ --}}
        <div class="seo-section" id="cara-booking" aria-labelledby="cara-title">
            <div class="seo-section-head">
                <h2 id="cara-title">Cara Booking Rental Mobil Lombok</h2>
                <p>Proses dibuat bertahap agar data kendaraan dan tanggal perjalanan dapat diperiksa sebelum booking dilanjutkan.</p>
            </div>

            <div class="seo-grid-3 seo-steps">
                <article class="seo-card seo-step">
                    <h3>Pilih Tanggal</h3>
                    <p>Tentukan tanggal keluar dan tanggal kembali sesuai rencana perjalanan.</p>
                </article>
                <article class="seo-card seo-step">
                    <h3>Pilih Kendaraan</h3>
                    <p>Gunakan filter dan daftar kendaraan untuk memilih armada yang sesuai.</p>
                </article>
                <article class="seo-card seo-step">
                    <h3>Cek NIK &amp; Booking</h3>
                    <p>Masukkan NIK untuk pengecekan data penyewa lalu lanjutkan ke form booking.</p>
                </article>
            </div>
        </div>

        {{-- ══ TIPS MEMILIH MOBIL ══ --}}
        <div class="seo-section" aria-labelledby="tips-title">
            <div class="seo-grid-2">
                <article class="seo-card">
                    <h3 id="tips-title">Pilih Mobil Sesuai Kebutuhan Perjalanan</h3>
                    <ul class="seo-list">
                        <li>Perjalanan berdua atau kelompok kecil dapat mempertimbangkan mobil berukuran kompak.</li>
                        <li>Untuk keluarga, prioritaskan kapasitas penumpang dan ruang bagasi.</li>
                        <li>Untuk perjalanan yang membutuhkan kenyamanan lebih, perhatikan kelas dan ukuran kendaraan.</li>
                        <li>Periksa transmisi sesuai preferensi Anda sebelum booking.</li>
                    </ul>
                </article>
                <article class="seo-card">
                    <h3>Siapkan Informasi Perjalanan</h3>
                    <p>
                        Sebelum booking, siapkan tanggal perjalanan, lokasi penjemputan atau pengantaran,
                        jumlah penumpang, dan pilihan layanan. Informasi tersebut membantu proses pemesanan berjalan lebih cepat.
                    </p>
                </article>
            </div>
        </div>

        {{-- ══ FAQ ══ --}}
        <div class="seo-section seo-faq" aria-labelledby="faq-title">
            <div class="seo-section-head">
                <h2 id="faq-title">Pertanyaan Umum Sewa Mobil Lombok</h2>
                <p>Beberapa pertanyaan yang sering muncul sebelum melakukan booking.</p>
            </div>

            <details>
                <summary>Berapa harga sewa mobil di Lombok?</summary>
                <p>Harga mengikuti kendaraan, paket, durasi, dan ketersediaan. Harga per hari dapat dilihat pada kartu kendaraan setelah Anda menentukan tanggal perjalanan.</p>
            </details>

            <details>
                <summary>Apakah tersedia sewa mobil Lombok lepas kunci?</summary>
                <p>Ya, tersedia untuk kendaraan yang memiliki label Lepas Kunci. Gunakan filter Lepas Kunci pada bagian pencarian untuk menampilkan armada yang sesuai.</p>
            </details>

            <details>
                <summary>Apakah tersedia rental mobil Lombok dengan sopir?</summary>
                <p>Tersedia untuk kendaraan yang memiliki label Dengan Sopir. Anda dapat menggunakan filter Dengan Sopir untuk melihat armada yang tersedia.</p>
            </details>

            <details>
                <summary>Apakah bisa menyewa mobil untuk beberapa hari?</summary>
                <p>Bisa. Tentukan tanggal keluar dan tanggal kembali pada form pencarian. Total durasi dan estimasi harga akan mengikuti tanggal yang Anda pilih.</p>
            </details>

            <details>
                <summary>Bagaimana cara booking rental mobil di Semeton Pesiar?</summary>
                <p>Pilih tanggal, tentukan jenis layanan, pilih kendaraan, kemudian lakukan pengecekan NIK untuk melanjutkan ke form booking.</p>
            </details>
        </div>

        {{-- ══ CTA ══ --}}
        <div class="seo-cta">
            <h2>Siap Menyewa Mobil di Lombok?</h2>
            <p style="margin-top:8px;">Tentukan tanggal perjalanan dan pilih kendaraan yang sesuai dari daftar armada di atas.</p>
            <div class="seo-hero-actions">
                <a href="#armada" class="seo-btn-primary">Pilih Kendaraan</a>
                <a href="https://wa.me/6281128948884" target="_blank" rel="noopener noreferrer" class="seo-btn-secondary">Hubungi WhatsApp</a>
            </div>
        </div>
    </section>
    @endif

    {{-- ══ MODAL CEK NIK ══ --}}
    {{-- BUG FIX: hapus class "hidden" — modal dikontrol murni lewat .show via JS --}}
    <div id="nikModal" class="hp-modal-backdrop">
        <div class="hp-modal" id="nikModalBox">

            <div class="hp-modal-header">
                <div>
                    <p class="hp-modal-step">Langkah berikutnya</p>
                    <h3 class="hp-modal-title">Cek Data Penyewa</h3>
                </div>
                <button type="button" onclick="closeNikModal()" class="hp-modal-close" aria-label="Tutup modal">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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

                <div id="nikError"   class="hp-modal-alert hp-modal-alert--error"   style="display:none"></div>
                <div id="nikSuccess" class="hp-modal-alert hp-modal-alert--success" style="display:none"></div>

                <button type="button" id="btnCheckNik" onclick="checkNik()" class="hp-modal-submit">
                    <svg id="checkNikIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z" />
                        <circle cx="7.5" cy="17.5" r="1.5" />
                        <circle cx="16.5" cy="17.5" r="1.5" />
                    </svg>
                </div>
                <span class="hp-footer-copy">© {{ date('Y') }} Semeton Pesiar Lombok</span>
            </div>
            <div class="hp-footer-contact">
                <a href="tel:+6281128948884" style="color:inherit;text-decoration:none;">📞 +62 811-2894-8884</a>
                <a href="https://wa.me/6281128948884" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none;">WhatsApp</a>
                <a href="{{ $canonicalUrl }}" style="color:inherit;text-decoration:none;">🌐 semetonpesiar.com</a>
            </div>
        </div>
    </footer>

    <script>
        // ── Tanggal ──────────────────────────────────────────────
        function updateMinReturn() {
            const keluar  = document.getElementById('tgl_keluar');
            const kembali = document.getElementById('tgl_kembali');
            if (!keluar.value) return;

            const next = new Date(keluar.value);
            next.setDate(next.getDate() + 1);
            const minVal = next.toISOString().split('T')[0];

            kembali.min = minVal;
            if (kembali.value <= keluar.value) kembali.value = minVal;
        }

        // ── Filter (update hidden input lalu submit) ─────────────
        // BUG FIX: querySelectorAll sebelumnya bisa mengenai beberapa input dengan
        // name yang sama (duplikat). Sekarang tiap param punya 1 hidden input dengan
        // id unik — langsung diupdate via getElementById.
        const hiddenInputIds = {
            tipe_sewa : 'hidden-tipe-sewa',
            transmisi : 'hidden-transmisi',
            sort      : 'hidden-sort',
        };

        function setFilter(name, value) {
            const id = hiddenInputIds[name];
            if (id) {
                document.getElementById(id).value = value;
            }
            document.getElementById('search-form').submit();
        }

        updateMinReturn();

        // ── Modal NIK ────────────────────────────────────────────
        let selectedCarId = null;

        function openNikModal(carId, carName) {
            selectedCarId = carId;
            document.getElementById('selectedCarName').textContent = carName;
            document.getElementById('modalNik').value              = '';
            document.getElementById('modalNikCounter').textContent = '0 / 16';
            document.getElementById('nikError').style.display      = 'none';
            document.getElementById('nikSuccess').style.display    = 'none';
            document.getElementById('nikModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('modalNik').focus(), 100);
        }

        function closeNikModal() {
            document.getElementById('nikModal').classList.remove('show');
            document.body.style.overflow = '';
            selectedCarId = null;
        }

        // Tutup saat klik backdrop
        document.getElementById('nikModal').addEventListener('click', function (e) {
            if (e.target === this) closeNikModal();
        });

        // Tutup saat tekan Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeNikModal();
        });

        // Counter karakter NIK
        document.getElementById('modalNik').addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            document.getElementById('modalNikCounter').textContent = `${this.value.length} / 16`;
        });

        // Submit dengan Enter
        document.getElementById('modalNik').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') checkNik();
        });

        // ── Cek NIK (fetch) ──────────────────────────────────────
        async function checkNik() {
            const nik        = document.getElementById('modalNik').value.trim();
            const errorBox   = document.getElementById('nikError');
            const successBox = document.getElementById('nikSuccess');
            const button     = document.getElementById('btnCheckNik');
            const buttonText = document.getElementById('checkNikText');

            errorBox.style.display   = 'none';
            successBox.style.display = 'none';

            if (nik.length !== 16) {
                errorBox.textContent    = 'NIK harus terdiri dari 16 digit.';
                errorBox.style.display  = 'block';
                return;
            }

            if (!selectedCarId) {
                errorBox.textContent   = 'Kendaraan belum dipilih.';
                errorBox.style.display = 'block';
                return;
            }

            button.disabled      = true;
            buttonText.textContent = 'Memeriksa…';

            try {
                const response = await fetch("{{ route('cek.nik.ajax') }}", {
                    method  : 'POST',
                    headers : {
                        'Content-Type' : 'application/json',
                        'Accept'       : 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        nik,
                        car_id          : selectedCarId,
                        tanggal_keluar  : document.getElementById('tgl_keluar').value,
                        tanggal_kembali : document.getElementById('tgl_kembali').value,
                        // BUG FIX: ambil dari hidden input yang sudah unik, bukan
                        // querySelector lama yang bisa membaca input duplikat pertama
                        tipe_sewa       : document.getElementById('hidden-tipe-sewa').value,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    errorBox.textContent   = data.message ?? 'NIK tidak dapat digunakan.';
                    errorBox.style.display = 'block';
                    return;
                }

                successBox.textContent   = data.message ?? 'NIK berhasil diverifikasi.';
                successBox.style.display = 'block';

                setTimeout(() => {
                    const base = data.registered
                        ? "{{ route('booking.form') }}"
                        : "{{ route('data.penyewa') }}";

                    const params = new URLSearchParams(
                        data.registered
                            ? {
                                customer_id     : data.customer_id,
                                car_id          : selectedCarId,
                                tanggal_keluar  : document.getElementById('tgl_keluar').value,
                                tanggal_kembali : document.getElementById('tgl_kembali').value,
                                tipe_sewa       : document.getElementById('hidden-tipe-sewa').value,
                            }
                            : {
                                ktp             : nik,
                                car_id          : selectedCarId,
                                tanggal_keluar  : document.getElementById('tgl_keluar').value,
                                tanggal_kembali : document.getElementById('tgl_kembali').value,
                                tipe_sewa       : document.getElementById('hidden-tipe-sewa').value,
                            }
                    );

                    window.location.href = base + '?' + params.toString();
                }, 700);

            } catch (err) {
                console.error(err);
                errorBox.textContent   = 'Terjadi kesalahan. Silakan coba lagi.';
                errorBox.style.display = 'block';
            } finally {
                button.disabled        = false;
                buttonText.textContent = 'Cek NIK';
            }
        }
    </script>

</body>
</html>
