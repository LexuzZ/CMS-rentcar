<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semeton Pesiar – Sewa Kendaraan Lombok</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .brand-orange { color: #c2410c; }
        .bg-brand { background: #c2410c; }
        .bg-brand-hover:hover { background: #9a3412; }
        .chip-active { background: #fff7ed; border-color: #c2410c; color: #c2410c; font-weight: 700; }
        .chip {
            border: 1.5px solid #e5e7eb; border-radius: 9999px;
            padding: 5px 16px; font-size: 13px; font-weight: 500;
            color: #374151; cursor: pointer; background: #fff; transition: all .15s;
        }
        .chip:hover { border-color: #c2410c; color: #c2410c; }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.1); }
        input[type=date]::-webkit-calendar-picker-indicator { opacity: .6; cursor: pointer; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- ══ NAVBAR ══ --}}
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('spt.png') }}" alt="Semeton Pesiar" class="h-9 w-auto">
        </a>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="text-sm font-semibold text-gray-600 hover:text-orange-700 transition">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('filament.admin.auth.login') }}"
                   class="flex items-center gap-2 bg-brand bg-brand-hover text-white text-sm font-bold px-5 py-2 rounded-xl transition shadow-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Masuk
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ══ SEARCH BAR ══ --}}
<div class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <form method="GET" action="{{ route('home') }}" id="search-form">

            <div class="flex flex-col md:flex-row items-stretch gap-3 mb-3">

                {{-- Lokasi statis --}}
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 min-w-[160px]">
                    <svg class="text-orange-600 flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Lokasi</p>
                        <p class="text-sm font-bold text-gray-800">Lombok</p>
                    </div>
                </div>

                {{-- Tanggal Keluar --}}
                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 font-medium mb-0.5">Tanggal Keluar</p>
                    <input type="date" name="tgl_keluar" id="tgl_keluar"
                           value="{{ request('tgl_keluar', now()->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full bg-transparent text-sm font-semibold text-gray-800 outline-none"
                           onchange="updateMinReturn()">
                </div>

                {{-- Panah --}}
                <div class="hidden md:flex items-center justify-center text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </div>

                {{-- Tanggal Kembali --}}
                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 font-medium mb-0.5">Tanggal Kembali</p>
                    <input type="date" name="tgl_kembali" id="tgl_kembali"
                           value="{{ request('tgl_kembali', now()->addDay()->format('Y-m-d')) }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full bg-transparent text-sm font-semibold text-gray-800 outline-none">
                </div>

                {{-- Tombol Ubah --}}
                <button type="submit"
                        class="flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-white font-bold text-sm px-6 py-3 rounded-xl transition shadow-sm whitespace-nowrap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Ubah
                </button>
            </div>

            {{-- Search nama --}}
            <div class="flex gap-2">
                <div class="flex-1 flex items-center gap-3 border border-gray-200 bg-white rounded-xl px-4 py-3 focus-within:border-orange-400 transition">
                    <svg class="text-gray-400 flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search"
                           placeholder="Cari nama kendaraan, mis. Fortuner"
                           value="{{ request('search') }}"
                           class="w-full text-sm text-gray-700 outline-none placeholder-gray-400">
                </div>
                <button type="submit" class="bg-brand bg-brand-hover text-white font-bold text-sm px-6 rounded-xl transition shadow-sm">
                    Cari
                </button>
            </div>

            {{-- Hidden: pertahankan state filter saat submit --}}
            <input type="hidden" name="transmisi" value="{{ request('transmisi', 'semua') }}">
            <input type="hidden" name="sort"      value="{{ request('sort', 'termurah') }}">

        </form>
    </div>
</div>

{{-- ══ FILTER CHIPS + SORT ══ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-3">

        {{-- Filter Transmisi (jenis dihapus, tidak ada di db) --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Transmisi</span>
            @foreach(['semua' => 'Semua', 'AT' => 'Matic (AT)', 'MT' => 'Manual (MT)'] as $val => $label)
                <button onclick="setFilter('transmisi', '{{ $val }}')"
                        class="chip {{ request('transmisi', 'semua') === $val ? 'chip-active' : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Sort --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Urutkan</span>
            <select onchange="setFilter('sort', this.value)"
                    class="text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl px-3 py-2 bg-white outline-none focus:border-orange-400 cursor-pointer">
                <option value="termurah" {{ request('sort', 'termurah') === 'termurah' ? 'selected' : '' }}>Harga termurah</option>
                <option value="termahal" {{ request('sort', 'termurah') === 'termahal' ? 'selected' : '' }}>Harga termahal</option>
                <option value="terbaru"  {{ request('sort', 'termurah') === 'terbaru'  ? 'selected' : '' }}>Terbaru</option>
            </select>
        </div>

    </div>
</div>

{{-- ══ GRID KARTU ══ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

    {{-- Info jumlah hasil --}}
    <div class="flex items-baseline gap-2 mb-5">
        <h2 class="text-lg font-extrabold text-gray-900">Kendaraan tersedia</h2>
        <span class="text-sm text-gray-400 font-medium">
            {{ $cars->total() }} kendaraan tersedia untuk tanggal Anda
        </span>
    </div>

    @if($cars->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 bg-orange-50 rounded-2xl flex items-center justify-center mb-4">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c2410c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"/>
                    <path d="m16 8 5 1 2 4v3h-2"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-1">Tidak ada kendaraan tersedia</h3>
            <p class="text-sm text-gray-400 mb-5">Coba ubah tanggal atau filter pencarian Anda.</p>
            <a href="{{ route('home') }}"
               class="bg-brand bg-brand-hover text-white font-bold text-sm px-6 py-3 rounded-xl transition">
                Reset Filter
            </a>
        </div>

    @else
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
            @foreach($cars as $car)
                @php
                    $brand       = $car->carModel?->brand?->name ?? '';
                    $model       = $car->carModel?->name ?? '';
                    $namaLengkap = strtoupper(trim("{$brand} {$model}"));

                    // ✅ Kolom yang sudah disesuaikan
                    $harga      = $car->harga_harian ?? 0;
                    $totalHarga = $harga * $totalHari;
                    $transmisi  = strtoupper($car->transmisi ?? 'AT');
                    $foto       = $car->photo
                                    ? Storage::url($car->photo)
                                    : asset('images/car-placeholder.png');

                    // Badge ketersediaan — pakai warna default karena tidak ada kolom unit
                    $badgeColor = 'bg-green-100 text-green-700';
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-hover shadow-sm">

                    {{-- Gambar --}}
                    <div class="relative bg-gray-50 aspect-[4/3] overflow-hidden">
                        <img src="{{ $foto }}"
                             alt="{{ $namaLengkap }}"
                             class="w-full h-full object-contain p-3"
                             onerror="this.src='{{ asset('images/car-placeholder.png') }}'">

                        {{-- Badge tersedia --}}
                        <span class="absolute top-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full {{ $badgeColor }}">
                            Tersedia
                        </span>

                        {{-- Logo brand --}}
                        <div class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full border border-gray-100 shadow-sm flex items-center justify-center overflow-hidden">
                            @if($car->carModel?->brand?->logo)
                                <img src="{{ Storage::url($car->carModel->brand->logo) }}"
                                     alt="{{ $brand }}" class="w-6 h-6 object-contain">
                            @else
                                <span class="text-xs font-extrabold text-orange-700">
                                    {{ mb_substr($brand, 0, 1) }}
                                </span>
                            @endif
                        </div>

                        {{-- Chip transmisi --}}
                        <span class="absolute bottom-3 right-3 text-xs font-bold bg-white/90 backdrop-blur px-2 py-0.5 rounded-full border border-gray-200 text-gray-600">
                            {{ $transmisi }}
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="font-extrabold text-gray-900 text-sm leading-tight mb-2">
                            {{ $namaLengkap }}
                        </h3>

                        <div class="mb-1">
                            <span class="text-lg font-extrabold brand-orange">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium"> /hari</span>
                        </div>

                        <div class="text-xs text-gray-500 font-medium mb-4">
                            {{ $totalHari }} hari ·
                            <span class="font-bold text-gray-700">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Tombol Pilih --}}
                        <a href="{{ route('booking.create', [
                                'car_id'      => $car->id,
                                'tgl_keluar'  => request('tgl_keluar'),
                                'tgl_kembali' => request('tgl_kembali'),
                           ]) }}"
                           class="block w-full text-center bg-brand bg-brand-hover text-white font-bold text-sm py-2.5 rounded-xl transition">
                            Pilih kendaraan ini
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($cars->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $cars->links() }}
            </div>
        @endif
    @endif

</div>

{{-- ══ FOOTER ══ --}}
<footer class="bg-white border-t border-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <img src="{{ asset('spt.png') }}" alt="Semeton Pesiar" class="h-8 w-auto">
            <span class="text-sm text-gray-400">© {{ date('Y') }} Semeton Pesiar</span>
        </div>
        <div class="flex items-center gap-5 text-sm text-gray-400">
            <span>📞 +6281128948884</span>
            <span>🌐 www.semetonpesiar.com</span>
        </div>
    </div>
</footer>

<script>
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

    function setFilter(name, value) {
        const form = document.getElementById('search-form');
        form.querySelectorAll(`input[name="${name}"]`).forEach(i => i.value = value);
        form.querySelectorAll(`select[name="${name}"]`).forEach(s => s.value = value);
        form.submit();
    }

    updateMinReturn();
</script>

</body>
</html>
