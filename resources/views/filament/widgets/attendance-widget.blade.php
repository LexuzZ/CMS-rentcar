<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:center;
                                width:38px;height:38px;border-radius:11px;
                                background:linear-gradient(135deg,#0ea5e9,#0284c7);
                                box-shadow:0 4px 12px rgba(14,165,233,.3);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--fi-color-gray-950,#111827);">Absensi Hari Ini</p>
                        <p style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-500,#6b7280);font-weight:400;" id="aw-clock">
                            {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>
                <div id="aw-live-time" style="font-size:18px;font-weight:800;color:var(--fi-color-gray-900,#111827);
                     font-variant-numeric:tabular-nums;letter-spacing:-.02em;">
                    {{ now()->format('H:i') }}
                </div>
            </div>
        </x-slot>

        <style>
            .aw-wrap { display:flex; flex-direction:column; align-items:center; padding:24px 16px; gap:20px; }

            /* Status card */
            .aw-status-card {
                width:100%; border-radius:14px; padding:20px 24px;
                display:flex; align-items:center; gap:16px;
                border:1px solid;
            }
            .aw-status-card.idle    { background:linear-gradient(135deg,#f0f9ff,#e0f2fe); border-color:#7dd3fc; }
            .aw-status-card.done    { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-color:#86efac; }
            .aw-status-card.late    { background:linear-gradient(135deg,#fffbeb,#fef3c7); border-color:#fcd34d; }
            .aw-status-card.error   { background:linear-gradient(135deg,#fff1f2,#fecdd3); border-color:#fca5a5; }

            .dark .aw-status-card.idle  { background:linear-gradient(135deg,#0c1a33,#0ea5e920); border-color:#0369a1; }
            .dark .aw-status-card.done  { background:linear-gradient(135deg,#052e16,#14532d); border-color:#166534; }
            .dark .aw-status-card.late  { background:linear-gradient(135deg,#1c1408,#3d2e00); border-color:#92400e; }
            .dark .aw-status-card.error { background:linear-gradient(135deg,#1a0505,#3f1d1d); border-color:#7f1d1d; }

            .aw-status-icon {
                width:52px; height:52px; border-radius:14px; flex-shrink:0;
                display:flex; align-items:center; justify-content:center;
            }
            .aw-status-card.idle  .aw-status-icon { background:rgba(14,165,233,.15); }
            .aw-status-card.done  .aw-status-icon { background:rgba(16,185,129,.15); }
            .aw-status-card.late  .aw-status-icon { background:rgba(245,158,11,.15); }
            .aw-status-card.error .aw-status-icon { background:rgba(239,68,68,.15); }

            .aw-status-title { font-size:15px; font-weight:700; margin:0 0 3px; }
            .aw-status-card.idle  .aw-status-title { color:#0c4a6e !important; }
            .aw-status-card.done  .aw-status-title { color:#14532d !important; }
            .aw-status-card.late  .aw-status-title { color:#78350f !important; }
            .aw-status-card.error .aw-status-title { color:#7f1d1d !important; }
            .dark .aw-status-card.idle  .aw-status-title { color:#7dd3fc !important; }
            .dark .aw-status-card.done  .aw-status-title { color:#4ade80 !important; }
            .dark .aw-status-card.late  .aw-status-title { color:#fcd34d !important; }
            .dark .aw-status-card.error .aw-status-title { color:#f87171 !important; }

            .aw-status-sub { font-size:12px; margin:0; color:#6b7280 !important; }
            .dark .aw-status-sub { color:#a8a29e !important; }

            /* Checkin info */
            .aw-info-row { display:flex; gap:10px; flex-wrap:wrap; width:100%; justify-content:center; }
            .aw-info-chip {
                display:inline-flex; align-items:center; gap:6px;
                padding:6px 14px; border-radius:100px;
                font-size:12px; font-weight:600;
                background:var(--fi-color-gray-100,#f3f4f6);
                border:1px solid var(--fi-color-gray-200,#e5e7eb);
                color:var(--fi-color-gray-700,#374151) !important;
            }
            .dark .aw-info-chip { background:#292524; border-color:#44403c; color:#d4d0cb !important; }

            /* Button */
            .aw-btn-wrap { width:100%; display:flex; justify-content:center; }
            .aw-btn {
                position:relative; overflow:hidden;
                padding:14px 40px; border-radius:14px; border:none;
                font-size:15px; font-weight:700; cursor:pointer;
                background:linear-gradient(135deg,#0ea5e9,#0284c7);
                color:#fff !important; letter-spacing:.01em;
                box-shadow:0 4px 16px rgba(14,165,233,.35);
                transition:all .15s; min-width:220px;
                display:flex; align-items:center; justify-content:center; gap:10px;
            }
            .aw-btn:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 8px 24px rgba(14,165,233,.45); }
            .aw-btn:active:not(:disabled) { transform:translateY(0); }
            .aw-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }
            .aw-btn .aw-spinner {
                width:18px; height:18px; border:2px solid rgba(255,255,255,.3);
                border-top-color:#fff; border-radius:50%;
                animation:aw-spin .7s linear infinite; display:none;
            }
            .aw-btn.loading .aw-spinner { display:block; }
            .aw-btn.loading .aw-btn-text { display:none; }
            @keyframes aw-spin { to { transform:rotate(360deg); } }

            /* GPS info */
            .aw-gps-note {
                font-size:11px; color:#9ca3af !important; text-align:center;
                display:flex; align-items:center; justify-content:center; gap:4px;
            }
            .dark .aw-gps-note { color:#78716c !important; }

            /* Error box */
            .aw-error {
                width:100%; padding:12px 16px; border-radius:10px;
                background:#fef2f2; border:1px solid #fca5a5;
                color:#991b1b !important; font-size:12px; font-weight:500;
                display:flex; align-items:flex-start; gap:8px;
            }
            .dark .aw-error { background:#3f1d1d; border-color:#7f1d1d; color:#f87171 !important; }

            /* Today recap (after check in) */
            .aw-recap {
                width:100%; display:grid; grid-template-columns:repeat(3,1fr); gap:10px;
            }
            .aw-recap-item {
                border-radius:10px; padding:12px 14px; text-align:center;
                background:var(--fi-color-gray-50,#f9fafb);
                border:1px solid var(--fi-color-gray-200,#e5e7eb);
            }
            .dark .aw-recap-item { background:#1c1917; border-color:#292524; }
            .aw-recap-val { font-size:18px; font-weight:800; color:var(--fi-color-gray-900,#111827) !important; }
            .dark .aw-recap-val { color:#f5f5f4 !important; }
            .aw-recap-label { font-size:10px; font-weight:600; color:#9ca3af !important; margin-top:3px; text-transform:uppercase; letter-spacing:.05em; }
            .dark .aw-recap-label { color:#57534e !important; }
        </style>

        @php
            $todayRecord = \App\Models\Attendance::where('user_id', auth()->id())
                ->where('date', today())->first();
            $hasCheckedIn = $todayRecord !== null;

            // Stats bulan ini
            $monthHadir    = \App\Models\Attendance::where('user_id', auth()->id())
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)
                ->where('status', 'hadir')->count();
            $monthTerlambat = \App\Models\Attendance::where('user_id', auth()->id())
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)
                ->where('status', 'terlambat')->count();
            $monthAlpha    = \App\Models\Attendance::where('user_id', auth()->id())
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)
                ->where('status', 'alpha')->count();
        @endphp

        <div class="aw-wrap">

            {{-- Status Card --}}
            @if($hasCheckedIn)
                <div class="aw-status-card {{ $todayRecord->status === 'terlambat' ? 'late' : 'done' }}">
                    <div class="aw-status-icon">
                        @if($todayRecord->status === 'terlambat')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        @else
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="aw-status-title">
                            {{ $todayRecord->status === 'terlambat' ? '⚠️ Anda Terlambat' : '✅ Sudah Absen' }}
                        </p>
                        <p class="aw-status-sub">
                            Tercatat masuk pukul <strong>{{ \Carbon\Carbon::parse($todayRecord->check_in_time)->format('H:i') }}</strong>
                            · Jarak {{ number_format($todayRecord->distance_meters, 0) }} m dari kantor
                        </p>
                    </div>
                </div>
            @else
                <div class="aw-status-card idle">
                    <div class="aw-status-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="aw-status-title">Belum Absen</p>
                        <p class="aw-status-sub">Klik tombol di bawah untuk melakukan absensi masuk hari ini.</p>
                    </div>
                </div>
            @endif

            {{-- Error message --}}
            <div id="aw-error" class="aw-error" style="display:none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;margin-top:1px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span id="aw-error-text"></span>
            </div>

            {{-- Tombol absen --}}
            @if(!$hasCheckedIn)
                <div class="aw-btn-wrap">
                    <button class="aw-btn" id="aw-checkin-btn" onclick="awStartCheckIn()">
                        <div class="aw-spinner"></div>
                        <span class="aw-btn-text" style="display:flex;align-items:center;gap:8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            Absen Sekarang
                        </span>
                    </button>
                </div>
                <p class="aw-gps-note">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    GPS akan diaktifkan untuk verifikasi lokasi (radius 50 m dari kantor)
                </p>
            @endif

            {{-- Rekap bulan ini --}}
            <div class="aw-recap">
                <div class="aw-recap-item">
                    <div class="aw-recap-val" style="color:#047857 !important;">{{ $monthHadir }}</div>
                    <div class="aw-recap-label">Hadir</div>
                </div>
                <div class="aw-recap-item">
                    <div class="aw-recap-val" style="color:#b45309 !important;">{{ $monthTerlambat }}</div>
                    <div class="aw-recap-label">Terlambat</div>
                </div>
                <div class="aw-recap-item">
                    <div class="aw-recap-val" style="color:#b91c1c !important;">{{ $monthAlpha }}</div>
                    <div class="aw-recap-label">Alpha</div>
                </div>
            </div>

        </div>

        <script>
            // Update jam real-time
            function awUpdateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2,'0');
                const m = String(now.getMinutes()).padStart(2,'0');
                const el = document.getElementById('aw-live-time');
                if (el) el.textContent = h + ':' + m;
            }
            setInterval(awUpdateClock, 1000);
            awUpdateClock();

            function awShowError(msg) {
                const box = document.getElementById('aw-error');
                const txt = document.getElementById('aw-error-text');
                if (box && txt) { txt.textContent = msg; box.style.display = 'flex'; }
                const btn = document.getElementById('aw-checkin-btn');
                if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
            }

            function awStartCheckIn() {
                const btn = document.getElementById('aw-checkin-btn');
                if (!btn) return;

                // Reset error
                const errBox = document.getElementById('aw-error');
                if (errBox) errBox.style.display = 'none';

                // Loading state
                btn.classList.add('loading');
                btn.disabled = true;

                if (!navigator.geolocation) {
                    awShowError('Browser Anda tidak mendukung GPS. Gunakan browser modern seperti Chrome atau Firefox.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        const lat = pos.coords.latitude;
                        const lon = pos.coords.longitude;
                        // Kirim ke Livewire
                        @this.checkIn(lat, lon);
                    },
                    function(err) {
                        const msgs = {
                            1: 'Akses lokasi ditolak. Izinkan akses GPS di browser Anda lalu coba lagi.',
                            2: 'Lokasi tidak tersedia. Pastikan GPS perangkat Anda aktif.',
                            3: 'Permintaan lokasi timeout. Coba lagi.',
                        };
                        awShowError(msgs[err.code] || 'Gagal mendapatkan lokasi.');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        </script>

    </x-filament::section>
</x-filament-widgets::widget>
