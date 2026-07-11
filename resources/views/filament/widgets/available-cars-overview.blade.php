<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 dark:text-emerald-400">
                        <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v5" />
                        <circle cx="16" cy="17" r="3" />
                        <circle cx="7" cy="17" r="3" />
                    </svg>
                </span>
                <span class="text-gray-900 dark:text-gray-100 font-semibold">Mobil Ready Hari Ini</span>
                @if($cars->isNotEmpty())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                     bg-emerald-100 text-emerald-700
                                     dark:bg-emerald-900/50 dark:text-emerald-300">
                        {{ $cars->flatten()->count() }} unit
                    </span>
                @endif
            </span>
        </x-slot>

        @forelse ($cars as $merek => $mobilList)
            <div class="cr-brand-block">
                {{-- Brand Header --}}
                <div class="cr-brand-header">
                    <span class="cr-brand-dot"></span>
                    <h3 class="cr-brand-name">{{ Illuminate\Support\Str::upper($merek) }}</h3>
                    <span class="cr-brand-count">{{ $mobilList->count() }} unit</span>
                </div>

                {{-- Cars Grid --}}
                <div class="cr-cars-grid">
                    @foreach ($mobilList as $mobil)
                        <div class="cr-car-card">
                            <div class="cr-car-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z" />
                                    <circle cx="7.5" cy="17.5" r="1.5" />
                                    <circle cx="16.5" cy="17.5" r="1.5" />
                                </svg>
                            </div>
                            <div class="cr-car-info">
                                <p class="cr-car-name">{{ $mobil->carModel->name }}</p>
                                <span class="cr-car-nopol">{{ $mobil->nopol }}</span>
                            </div>
                            <div class="cr-car-status">
                                <span class="cr-status-dot"></span>
                                <span class="cr-status-text">Ready</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="cr-empty">
                <div class="cr-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                    </svg>
                </div>
                <p class="cr-empty-title">Tidak ada unit tersedia</p>
                <p class="cr-empty-sub">Semua mobil sedang dalam status booking atau maintenance hari ini.</p>
            </div>
        @endforelse

        <style>
            /* === Brand Block === */
            .cr-brand-block {
                margin-bottom: 1.25rem;
            }

            .cr-brand-block:last-child {
                margin-bottom: 0;
            }

            .cr-brand-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding: 0 2px;
            }

            .cr-brand-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #10b981;
                flex-shrink: 0;
            }

            .cr-brand-name {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.08em;
                color: #6b7280;
                margin: 0;
            }

            .dark .cr-brand-name {
                color: #9ca3af;
            }

            .cr-brand-count {
                font-size: 11px;
                color: #9ca3af;
                margin-left: auto;
            }

            /* === Cars Grid === */
            .cr-cars-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 8px;
            }

            /* === Car Card === */
            .cr-car-card {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 11px 14px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                background: #ffffff;
                transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
                cursor: default;
            }

            .dark .cr-car-card {
                background: #1f2937;
                border-color: #374151;
            }

            .cr-car-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 14px rgba(16, 185, 129, 0.12);
                border-color: #6ee7b7;
            }

            .dark .cr-car-card:hover {
                border-color: #059669;
                box-shadow: 0 4px 14px rgba(16, 185, 129, 0.08);
            }

            /* Icon */
            .cr-car-icon {
                flex-shrink: 0;
                width: 38px;
                height: 38px;
                border-radius: 10px;
                background: #f0fdf4;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #059669;
            }

            .dark .cr-car-icon {
                background: #064e3b;
                color: #34d399;
            }

            /* Info */
            .cr-car-info {
                flex: 1;
                min-width: 0;
            }

            .cr-car-name {
                font-size: 13px;
                font-weight: 600;
                color: #111827;
                margin: 0 0 3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .dark .cr-car-name {
                color: #f3f4f6;
            }

            .cr-car-nopol {
                display: inline-block;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.1em;
                padding: 2px 7px;
                border-radius: 5px;
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #e5e7eb;
                font-family: ui-monospace, monospace;
            }

            .dark .cr-car-nopol {
                background: #111827;
                color: #d1d5db;
                border-color: #4b5563;
            }

            /* Status */
            .cr-car-status {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .cr-status-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #10b981;
                box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
                animation: cr-pulse 2s infinite;
            }

            @keyframes cr-pulse {

                0%,
                100% {
                    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
                }

                50% {
                    box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.08);
                }
            }

            .cr-status-text {
                font-size: 10px;
                font-weight: 600;
                color: #059669;
                letter-spacing: 0.04em;
            }

            .dark .cr-status-text {
                color: #34d399;
            }

            /* === Empty State === */
            .cr-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 40px 20px;
                border-radius: 14px;
                border: 1.5px dashed #d1d5db;
                background: #f9fafb;
                text-align: center;
            }

            .dark .cr-empty {
                background: #111827;
                border-color: #374151;
            }

            .cr-empty-icon {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #9ca3af;
            }

            .dark .cr-empty-icon {
                background: #1f2937;
                color: #6b7280;
            }

            .cr-empty-title {
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin: 0;
            }

            .dark .cr-empty-title {
                color: #d1d5db;
            }

            .cr-empty-sub {
                font-size: 12px;
                color: #9ca3af;
                margin: 0;
                max-width: 280px;
            }

            /* ==========================================
   Mobile Responsive (Tidak mengubah CSS lama)
========================================== */
            @media (max-width: 768px) {

                .cr-cars-grid {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }

                .cr-car-card {
                    padding: 12px;
                    gap: 10px;
                }

                .cr-car-icon {
                    width: 34px;
                    height: 34px;
                }

                .cr-car-name {
                    font-size: 12px;
                }

                .cr-car-nopol {
                    font-size: 9px;
                    padding: 2px 6px;
                }

                .cr-status-text {
                    display: none;
                }

                .cr-brand-header {
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .cr-brand-count {
                    margin-left: 0;
                }

            }

            /* Extra Small Device */
            @media (max-width: 480px) {

                .cr-car-card {
                    padding: 10px;
                }

                .cr-car-icon {
                    width: 30px;
                    height: 30px;
                }

                .cr-car-name {
                    font-size: 11px;
                }

                .cr-car-nopol {
                    font-size: 8px;
                }

            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
