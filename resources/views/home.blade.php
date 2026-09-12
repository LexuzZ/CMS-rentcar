<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Semeton Pesiar – Sewa Kendaraan Lombok</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="hp-body">

    {{-- ══ NAVBAR ══ --}}
    <nav class="hp-nav">
        <div class="hp-nav-inner">
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
            <a href="#" class="hp-nav-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
                Masuk
            </a>
        </div>
    </nav>

    {{-- ══ SEARCH / FILTER BAR ══ --}}
    <div class="hp-searchbar">
        <div class="hp-searchbar-inner">
            <form method="GET" action="{{ route('home') }}" id="search-form">

                {{-- Toggle Lepas Kunci / Dengan Sopir --}}
                @php $tipeSewa = request('tipe_sewa', 'semua'); @endphp
                <div class="hp-toggle-row">
                    <button type="button" onclick="setFilter('tipe_sewa','lepas_kunci')"
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
                    <button type="button" onclick="setFilter('tipe_sewa','dengan_sopir')"
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

                    <div class="hp-date-field">
                        <p class="hp-date-micro">Tanggal Keluar</p>
                        <input type="date" name="tanggal_keluar" id="tgl_keluar"
                            value="{{ request('tanggal_keluar', now()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}"
                            class="hp-date-input" onchange="updateMinReturn()">
                    </div>

                    <div class="hp-date-arrow">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                    <button type="submit" class="hp-ubah-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Ubah
                    </button>
                </div>

                {{-- Search nama --}}
                <div class="hp-search-row">
                    <div class="hp-search-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input type="text" name="search" placeholder="Cari nama kendaraan, mis. Fortuner"
                            value="{{ request('search') }}" class="hp-search-input">
                    </div>
                    <button type="submit" class="hp-search-btn">Cari</button>
                </div>

                <input type="hidden" name="tipe_sewa" value="{{ request('tipe_sewa', 'semua') }}">
                <input type="hidden" name="transmisi" value="{{ request('transmisi', 'semua') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'termurah') }}">
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
                    <option value="termurah" {{ request('sort','termurah') === 'termurah' ? 'selected' : '' }}>Harga termurah</option>
                    <option value="termahal" {{ request('sort','termurah') === 'termahal' ? 'selected' : '' }}>Harga termahal</option>
                    <option value="terbaru"  {{ request('sort','termurah') === 'terbaru'  ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══ GRID KARTU ══ --}}
    <div class="hp-grid-wrap">

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
                            'green'  => 'badge-green',
                            'blue'   => 'badge-blue',
                            'purple' => 'badge-purple',
                            'gray'   => 'badge-gray',
                        ];
                        $badgeClass = $badgeStyles[$car['badge_color'] ?? 'gray'] ?? 'badge-gray';
                        $bisaLepasKunci  = in_array('lepas_kunci', $car['tipe_sewa']);
                        $bisaDenganSopir = in_array('dengan_sopir', $car['tipe_sewa']);
                    @endphp

                    <div class="hp-card">

                        {{-- Gambar --}}
                        <div class="hp-card-img-wrap">
                            <img src="{{ $car['foto'] }}" alt="{{ $car['brand'] }} {{ $car['nama'] }}"
                                class="hp-card-img"
                                onerror="this.src='https://placehold.co/400x300/f3f4f6/9ca3af?text=No+Image'">

                            @if($car['badge'])
                                <span class="hp-badge {{ $badgeClass }}">{{ $car['badge'] }}</span>
                            @endif

                            <span class="hp-badge-tr">{{ $transmisi }}</span>

                            <span class="hp-badge-bl">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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

                            {{-- Tipe sewa chips --}}
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

            @if($cars->hasPages())
                <div class="hp-pagination">{{ $cars->links() }}</div>
            @endif
        @endif

    </div>

    {{-- ══ MODAL CEK NIK ══ --}}
    <div id="nikModal" class="hp-modal-backdrop hidden">
        <div class="hp-modal" id="nikModalBox">

            {{-- Header --}}
            <div class="hp-modal-header">
                <div>
                    <p class="hp-modal-step">Langkah berikutnya</p>
                    <h3 class="hp-modal-title">Cek Data Penyewa</h3>
                </div>
                <button type="button" onclick="closeNikModal()" class="hp-modal-close">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="hp-modal-body">

                {{-- Kendaraan terpilih --}}
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
                <span>📞 +6281128948884</span>
                <span>🌐 www.semetonpesiar.com</span>
            </div>
        </div>
    </footer>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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
        .hp-logo { display: flex; align-items: center; gap: 9px; text-decoration: none; }
        .hp-logo-mark {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        .hp-logo-text { font-size: 13.5px; font-weight: 700; color: #0f172a; }
        .hp-nav-btn {
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
        .hp-nav-btn:hover { background: #ea580c; }

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
        .hp-tipe-btn:hover { border-color: #f97316; color: #f97316; }
        .hp-tipe-btn.active {
            background: #fff7ed;
            border-color: #f97316;
            color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.1);
        }
        .hp-tipe-icon {
            width: 30px; height: 30px;
            border-radius: 7px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
            flex-shrink: 0;
        }
        .hp-tipe-btn.active .hp-tipe-icon { background: #ffedd5; }

        /* Date row */
        .hp-date-row {
            display: flex;
            align-items: stretch;
            gap: 10px;
            margin-bottom: 10px;
        }
        .hp-date-loc {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            min-width: 140px;
            color: #f97316;
        }
        .hp-date-micro { font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 2px; }
        .hp-date-val { font-size: 13px; font-weight: 700; color: #0f172a; }
        .hp-date-field {
            flex: 1;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
        }
        .hp-date-field:focus-within { border-color: #f97316; }
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
        .hp-date-arrow {
            display: flex;
            align-items: center;
            color: #cbd5e1;
        }
        @media (max-width: 640px) { .hp-date-arrow { display: none; } }
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
        }
        .hp-ubah-btn:hover { background: #f59e0b; }

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
        .hp-search-wrap:focus-within { border-color: #f97316; }
        .hp-search-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            color: #0f172a;
            font-family: inherit;
        }
        .hp-search-input::placeholder { color: #94a3b8; }
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
        .hp-search-btn:hover { background: #ea580c; }

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
        .hp-filter-group { display: flex; align-items: center; gap: 8px; }
        .hp-filter-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
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
        .hp-chip:hover { border-color: #f97316; color: #f97316; }
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
        .hp-sort-select:focus { border-color: #f97316; }

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
        .hp-grid-title { font-size: 17px; font-weight: 800; color: #0f172a; }
        .hp-grid-count { font-size: 13px; color: #94a3b8; font-weight: 500; }

        .hp-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }
        @media (min-width: 640px)  { .hp-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 768px)  { .hp-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { .hp-grid { grid-template-columns: repeat(4, 1fr); } }

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
            box-shadow: 0 14px 36px rgba(0,0,0,.09);
        }
        .hp-card-img-wrap {
            position: relative;
            background: #f8fafc;
            aspect-ratio: 4/3;
            overflow: hidden;
        }
        .hp-card-img {
            width: 100%; height: 100%;
            object-fit: contain;
            padding: 14px;
        }
        .hp-badge {
            position: absolute;
            top: 10px; left: 10px;
            font-size: 11px; font-weight: 700;
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
            bottom: 10px; right: 10px;
            font-size: 11px; font-weight: 700;
            background: rgba(255,255,255,.92);
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 100px;
            color: #475569;
            backdrop-filter: blur(4px);
        }
        .hp-badge-bl {
            position: absolute;
            bottom: 10px; left: 10px;
            display: flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            background: rgba(255,255,255,.92);
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 100px;
            color: #475569;
            backdrop-filter: blur(4px);
        }
        .hp-card-body { padding: 14px; }
        .hp-card-brand { font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 2px; }
        .hp-card-name { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .hp-card-chips { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; }
        .hp-chip-tipe {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }
        .hp-chip-tipe--kunci { background: #fff7ed; color: #f97316; border: 1px solid #fed7aa; }
        .hp-chip-tipe--sopir { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .hp-card-price-row { display: flex; align-items: baseline; gap: 4px; }
        .hp-card-price { font-size: 17px; font-weight: 800; color: #f97316; }
        .hp-card-per { font-size: 12px; color: #94a3b8; }
        .hp-card-total { font-size: 12px; color: #64748b; margin: 4px 0 12px; }
        .hp-card-total strong { color: #0f172a; font-weight: 700; }
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
        .hp-card-btn:hover { background: #ea580c; }

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
            width: 72px; height: 72px;
            background: #fff7ed;
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .hp-empty-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 6px; }
        .hp-empty-sub { font-size: 13px; color: #94a3b8; margin-bottom: 20px; }
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
        .hp-empty-reset:hover { background: #ea580c; }

        .hp-pagination { margin-top: 36px; display: flex; justify-content: center; }

        /* ── Modal ── */
        .hp-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(15,23,42,.55);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .hp-modal-backdrop.show { display: flex; }
        .hp-modal {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,.25);
            animation: modal-in .22s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes modal-in {
            from { opacity: 0; transform: scale(.95) translateY(12px); }
            to   { opacity: 1; transform: scale(1)  translateY(0); }
        }
        .hp-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 22px;
            background: #f97316;
            color: #fff;
        }
        .hp-modal-step { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.75); margin-bottom: 3px; }
        .hp-modal-title { font-size: 17px; font-weight: 800; }
        .hp-modal-close {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            border: none;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }
        .hp-modal-close:hover { background: rgba(255,255,255,.25); }
        .hp-modal-body { padding: 22px; display: flex; flex-direction: column; gap: 16px; }

        .hp-modal-car {
            padding: 12px 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
        }
        .hp-modal-car-label { font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 3px; }
        .hp-modal-car-name { font-size: 13.5px; font-weight: 800; color: #0f172a; }

        .hp-modal-field { display: flex; flex-direction: column; gap: 6px; }
        .hp-modal-label { font-size: 13px; font-weight: 600; color: #374151; }
        .hp-modal-input-wrap { position: relative; }
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
        .hp-modal-input::placeholder { color: #94a3b8; font-weight: 400; letter-spacing: 0; }
        .hp-modal-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        }
        .hp-modal-counter {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            pointer-events: none;
        }
        .hp-modal-hint { font-size: 12px; color: #94a3b8; }

        .hp-modal-alert {
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.5;
        }
        .hp-modal-alert--error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .hp-modal-alert--success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

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
        .hp-modal-submit:hover { background: #ea580c; }
        .hp-modal-submit:disabled { opacity: .65; cursor: not-allowed; }

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
        .hp-footer-brand { display: flex; align-items: center; gap: 9px; }
        .hp-footer-copy { font-size: 13px; color: #94a3b8; }
        .hp-footer-contact { display: flex; gap: 20px; font-size: 13px; color: #94a3b8; }

        input[type=date]::-webkit-calendar-picker-indicator { opacity: .6; cursor: pointer; }
    </style>

    <script>
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

        function setFilter(name, value) {
            const form = document.getElementById('search-form');
            form.querySelectorAll(`input[name="${name}"]`).forEach(i => i.value = value);
            form.querySelectorAll(`select[name="${name}"]`).forEach(s => s.value = value);
            form.submit();
        }

        updateMinReturn();

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

        // Close on backdrop click
        document.getElementById('nikModal').addEventListener('click', function(e) {
            if (e.target === this) closeNikModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeNikModal();
        });

        document.getElementById('modalNik').addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            document.getElementById('modalNikCounter').textContent = `${this.value.length} / 16`;
        });

        // Enter to submit
        document.getElementById('modalNik').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') checkNik();
        });

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        nik,
                        car_id: selectedCarId,
                        tanggal_keluar:  document.getElementById('tgl_keluar').value,
                        tanggal_kembali: document.getElementById('tgl_kembali').value,
                        tipe_sewa: document.querySelector('input[name="tipe_sewa"]').value
                    })
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
                            ? { customer_id: data.customer_id, car_id: selectedCarId,
                                tanggal_keluar: document.getElementById('tgl_keluar').value,
                                tanggal_kembali: document.getElementById('tgl_kembali').value,
                                tipe_sewa: document.querySelector('input[name="tipe_sewa"]').value }
                            : { ktp: nik, car_id: selectedCarId,
                                tanggal_keluar: document.getElementById('tgl_keluar').value,
                                tanggal_kembali: document.getElementById('tgl_kembali').value,
                                tipe_sewa: document.querySelector('input[name="tipe_sewa"]').value }
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
