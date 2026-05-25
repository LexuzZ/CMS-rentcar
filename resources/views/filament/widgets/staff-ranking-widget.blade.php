<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span class="lb-heading-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </span>
                <span class="lb-heading-text">Leaderboard Staff</span>
            </span>
        </x-slot>

        <x-slot name="description">
            <span class="lb-description">Statistik penugasan antar &amp; jemput pada {{ $dateForHumans }}</span>
        </x-slot>

        {{-- Date picker --}}
        <div class="lb-datepicker">
            {{ $this->form }}
        </div>

        {{-- Podium top 3 --}}
        @if($stats->count() >= 1)
        <div class="lb-podium">
            @php
                $top    = $stats->take(3)->values();
                $first  = $top->get(0);
                $second = $top->get(1);
                $third  = $top->get(2);
            @endphp

            {{-- 2nd --}}
            <div class="lb-podium-slot lb-podium-slot--2 {{ $second ? '' : 'lb-podium-slot--empty' }}">
                @if($second)
                <div class="lb-avatar lb-avatar--silver">
                    {{ mb_strtoupper(mb_substr($second['staff_name'], 0, 1)) }}
                </div>
                <p class="lb-pod-name">{{ $second['staff_name'] }}</p>
                <p class="lb-pod-jobs">{{ $second['total'] }} job</p>
                <div class="lb-pod-block lb-pod-block--silver">
                    <span class="lb-pod-medal">🥈</span>
                    <span class="lb-pod-rank">2</span>
                </div>
                @else
                <div class="lb-pod-block lb-pod-block--silver lb-pod-block--vacant">—</div>
                @endif
            </div>

            {{-- 1st --}}
            <div class="lb-podium-slot lb-podium-slot--1">
                @if($first)
                <div class="lb-pod-crown">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M2 19h20l-2-9-5 4-3-7-3 7-5-4-2 9z"/>
                    </svg>
                </div>
                <div class="lb-avatar lb-avatar--gold">
                    {{ mb_strtoupper(mb_substr($first['staff_name'], 0, 1)) }}
                </div>
                <p class="lb-pod-name lb-pod-name--first">{{ $first['staff_name'] }}</p>
                <p class="lb-pod-jobs">{{ $first['total'] }} job</p>
                <div class="lb-pod-block lb-pod-block--gold">
                    <span class="lb-pod-medal">🥇</span>
                    <span class="lb-pod-rank">1</span>
                </div>
                @endif
            </div>

            {{-- 3rd --}}
            <div class="lb-podium-slot lb-podium-slot--3 {{ $third ? '' : 'lb-podium-slot--empty' }}">
                @if($third)
                <div class="lb-avatar lb-avatar--bronze">
                    {{ mb_strtoupper(mb_substr($third['staff_name'], 0, 1)) }}
                </div>
                <p class="lb-pod-name">{{ $third['staff_name'] }}</p>
                <p class="lb-pod-jobs">{{ $third['total'] }} job</p>
                <div class="lb-pod-block lb-pod-block--bronze">
                    <span class="lb-pod-medal">🥉</span>
                    <span class="lb-pod-rank">3</span>
                </div>
                @else
                <div class="lb-pod-block lb-pod-block--bronze lb-pod-block--vacant">—</div>
                @endif
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div class="lb-table-wrap">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th class="lb-th lb-th--center" style="width:52px">Rank</th>
                        <th class="lb-th">Nama Staff</th>
                        <th class="lb-th lb-th--center">Total</th>
                        <th class="lb-th lb-th--center">Antar</th>
                        <th class="lb-th lb-th--center">Jemput</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stats as $stat)
                    @php
                        $rank = $loop->iteration;
                        $isTop3 = $rank <= 3;
                        $totalColor = $stat['total'] >= 5
                            ? '#15803d' : ($stat['total'] >= 3 ? '#b45309' : '#6b7280');
                        $totalBg = $stat['total'] >= 5
                            ? '#dcfce7' : ($stat['total'] >= 3 ? '#fef3c7' : '#f3f4f6');
                        $totalBorder = $stat['total'] >= 5
                            ? '#bbf7d0' : ($stat['total'] >= 3 ? '#fde68a' : '#e5e7eb');
                    @endphp
                    <tr class="lb-tr {{ $isTop3 ? 'lb-tr--top3' : '' }}">

                        <td class="lb-td lb-td--center">
                            @if($rank === 1)
                                <span class="lb-rank-medal">🥇</span>
                            @elseif($rank === 2)
                                <span class="lb-rank-medal">🥈</span>
                            @elseif($rank === 3)
                                <span class="lb-rank-medal">🥉</span>
                            @else
                                <span class="lb-rank-num">{{ $rank }}</span>
                            @endif
                        </td>

                        <td class="lb-td">
                            <div class="lb-staff-row">
                                <div class="lb-staff-avatar" style="
                                    background: {{ $rank===1 ? 'linear-gradient(135deg,#fef3c7,#fde68a)' : ($rank===2 ? 'linear-gradient(135deg,#f1f5f9,#e2e8f0)' : ($rank===3 ? 'linear-gradient(135deg,#fff7ed,#fed7aa)' : '#f8fafc')) }};
                                    color: {{ $rank===1 ? '#b45309' : ($rank===2 ? '#475569' : ($rank===3 ? '#c2410c' : '#64748b')) }};
                                    border-color: {{ $rank===1 ? '#fde68a' : ($rank===2 ? '#e2e8f0' : ($rank===3 ? '#fed7aa' : '#e2e8f0')) }};
                                ">
                                    {{ mb_strtoupper(mb_substr($stat['staff_name'], 0, 1)) }}
                                </div>
                                <span class="lb-staff-name {{ $isTop3 ? 'lb-staff-name--bold' : '' }}">
                                    {{ $stat['staff_name'] }}
                                </span>
                            </div>
                        </td>

                        <td class="lb-td lb-td--center">
                            <span class="lb-badge" style="background:{{ $totalBg }};color:{{ $totalColor }};border-color:{{ $totalBorder }};">
                                {{ $stat['total'] }}
                            </span>
                        </td>

                        <td class="lb-td lb-td--center">
                            <span class="lb-badge lb-badge--blue">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                </svg>
                                {{ $stat['penyerahan'] }}
                            </span>
                        </td>

                        <td class="lb-td lb-td--center">
                            <span class="lb-badge lb-badge--purple">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                                </svg>
                                {{ $stat['pengembalian'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="lb-empty">
                                <div class="lb-empty-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <p class="lb-empty-title">Belum ada aktivitas</p>
                                <p class="lb-empty-sub">Tidak ada penugasan staff pada tanggal ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <style>
            /* Heading */
            .lb-heading-icon {
                display:flex;align-items:center;justify-content:center;
                width:28px;height:28px;border-radius:8px;
                background:linear-gradient(135deg,#fef3c7,#fde68a);
                border:1px solid #fcd34d;color:#d97706;
                box-shadow:0 2px 8px rgba(251,191,36,.25);
            }
            .dark .lb-heading-icon { background:#451a03;border-color:#78350f;color:#fcd34d; }
            .lb-heading-text { font-size:15px;font-weight:700;color:inherit; }
            .lb-description { font-size:12px;color:#94a3b8; }

            /* Datepicker */
            .lb-datepicker { max-width:240px;margin-bottom:20px; }

            /* ── Podium ── */
            .lb-podium {
                display:flex;align-items:flex-end;justify-content:center;
                gap:8px;margin-bottom:20px;padding:16px 8px 0;
            }
            .lb-podium-slot {
                display:flex;flex-direction:column;align-items:center;
                gap:6px;flex:1;max-width:140px;
            }
            .lb-pod-crown { color:#f59e0b;margin-bottom:-4px; }

            /* Avatars */
            .lb-avatar {
                width:48px;height:48px;border-radius:50%;
                display:flex;align-items:center;justify-content:center;
                font-size:18px;font-weight:800;border:2px solid;
            }
            .lb-avatar--gold   { background:linear-gradient(135deg,#fef3c7,#fde68a);color:#b45309;border-color:#fcd34d;box-shadow:0 4px 14px rgba(251,191,36,.4); }
            .lb-avatar--silver { background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#475569;border-color:#cbd5e1;box-shadow:0 3px 10px rgba(100,116,139,.2); }
            .lb-avatar--bronze { background:linear-gradient(135deg,#fff7ed,#fed7aa);color:#c2410c;border-color:#fdba74;box-shadow:0 3px 10px rgba(194,65,12,.2); }

            .lb-pod-name {
                font-size:12px;font-weight:600;color:#374151;text-align:center;
                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:100%;
            }
            .dark .lb-pod-name { color:#d1d5db; }
            .lb-pod-name--first { font-size:13px;font-weight:700; }
            .lb-pod-jobs { font-size:11px;color:#9ca3af;margin-top:-4px; }

            .lb-pod-block {
                width:100%;padding:10px 6px;border-radius:10px 10px 0 0;
                display:flex;align-items:center;justify-content:center;gap:6px;
                font-size:13px;font-weight:700;
            }
            .lb-pod-block--gold   { background:linear-gradient(180deg,#fef3c7,#fde68a);border:1px solid #fcd34d;border-bottom:none;min-height:70px;align-items:flex-start;padding-top:14px; }
            .lb-pod-block--silver { background:linear-gradient(180deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0;border-bottom:none;min-height:52px;align-items:flex-start;padding-top:10px; }
            .lb-pod-block--bronze { background:linear-gradient(180deg,#fff7ed,#fed7aa);border:1px solid #fdba74;border-bottom:none;min-height:40px;align-items:flex-start;padding-top:8px; }
            .lb-pod-block--vacant { min-height:40px;opacity:.4;font-size:18px;color:#9ca3af;justify-content:center;align-items:center; }
            .lb-pod-rank { font-size:11px;color:#9ca3af; }

            /* ── Table ── */
            .lb-table-wrap {
                border-radius:12px;overflow:hidden;
                border:1px solid #e5e7eb;
            }
            .dark .lb-table-wrap { border-color:#374151; }

            .lb-table { width:100%;border-collapse:collapse; }

            .lb-th {
                padding:10px 14px;
                font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                color:#9ca3af;background:#f8fafc;
                border-bottom:1px solid #e5e7eb;
            }
            .dark .lb-th { background:#111827;border-color:#374151;color:#6b7280; }
            .lb-th--center { text-align:center; }

            .lb-tr { border-bottom:1px solid #f1f5f9;transition:background .12s; }
            .dark .lb-tr { border-color:#374151; }
            .lb-tr:last-child { border-bottom:none; }
            .lb-tr:hover { background:#f8fafc; }
            .dark .lb-tr:hover { background:#111827; }
            .lb-tr--top3 { background:#fdfdf9; }
            .dark .lb-tr--top3 { background:#1a1a0f; }

            .lb-td { padding:11px 14px;font-size:13px; }
            .lb-td--center { text-align:center; }

            /* Rank */
            .lb-rank-medal { font-size:18px;line-height:1; }
            .lb-rank-num {
                display:inline-flex;align-items:center;justify-content:center;
                width:24px;height:24px;border-radius:50%;
                background:#f1f5f9;color:#6b7280;
                font-size:11px;font-weight:700;
                border:1px solid #e2e8f0;
            }
            .dark .lb-rank-num { background:#1f2937;border-color:#374151;color:#9ca3af; }

            /* Staff row */
            .lb-staff-row { display:flex;align-items:center;gap:10px; }
            .lb-staff-avatar {
                width:32px;height:32px;border-radius:8px;flex-shrink:0;
                display:flex;align-items:center;justify-content:center;
                font-size:13px;font-weight:700;border:1px solid;
            }
            .lb-staff-name { font-size:13px;font-weight:500;color:#374151; }
            .dark .lb-staff-name { color:#d1d5db; }
            .lb-staff-name--bold { font-weight:700;color:#111827; }
            .dark .lb-staff-name--bold { color:#f3f4f6; }

            /* Badges */
            .lb-badge {
                display:inline-flex;align-items:center;gap:4px;
                padding:3px 10px;border-radius:100px;
                font-size:12px;font-weight:700;
                border:1px solid;
            }
            .lb-badge--blue   { background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe; }
            .lb-badge--purple { background:#faf5ff;color:#6d28d9;border-color:#ddd6fe; }
            .dark .lb-badge--blue   { background:#1e3a5f;color:#93c5fd;border-color:#1d4ed833; }
            .dark .lb-badge--purple { background:#2e1065;color:#c4b5fd;border-color:#6d28d933; }

            /* Empty */
            .lb-empty {
                display:flex;flex-direction:column;align-items:center;gap:8px;
                padding:40px 20px;text-align:center;
            }
            .lb-empty-icon {
                width:48px;height:48px;border-radius:50%;
                background:#f8fafc;border:1px solid #e2e8f0;
                display:flex;align-items:center;justify-content:center;color:#9ca3af;
            }
            .dark .lb-empty-icon { background:#1f2937;border-color:#374151; }
            .lb-empty-title { font-size:14px;font-weight:600;color:#374151; }
            .dark .lb-empty-title { color:#d1d5db; }
            .lb-empty-sub { font-size:12px;color:#9ca3af; }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
