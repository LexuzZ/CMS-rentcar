<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Menu
        </x-slot>

        <div class="qm-grid">

            {{-- Form Sewa --}}
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('create') }}" class="qm-card" style="--qm-bg:#eff6ff;--qm-border:#bfdbfe;--qm-bg-dark:#1e3a5f;--qm-border-dark:#2563eb33;--qm-icon-bg:#dbeafe;--qm-icon-bg-dark:#1d3e6e;--qm-icon-color:#2563eb;--qm-icon-color-dark:#93c5fd;--qm-label:#1e40af;--qm-label-dark:#bfdbfe;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </span>
                <span class="qm-label">Form Sewa</span>
            </a>

            {{-- Order Via Customer --}}
            <a href="https://adminsemetonpesiarlombok.id/order" target="_blank" class="qm-card" style="--qm-bg:#faf5ff;--qm-border:#e9d5ff;--qm-bg-dark:#2e1065;--qm-border-dark:#7c3aed33;--qm-icon-bg:#ede9fe;--qm-icon-bg-dark:#3b1f7a;--qm-icon-color:#7c3aed;--qm-icon-color-dark:#c4b5fd;--qm-label:#5b21b6;--qm-label-dark:#ddd6fe;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </span>
                <span class="qm-label">Order Via Cust.</span>
            </a>

            {{-- WhatsApp --}}
            <a href="https://web.whatsapp.com" target="_blank" class="qm-card" style="--qm-bg:#f0fdf4;--qm-border:#bbf7d0;--qm-bg-dark:#052e16;--qm-border-dark:#16a34a33;--qm-icon-bg:#dcfce7;--qm-icon-bg-dark:#0a3d1f;--qm-icon-color:#16a34a;--qm-icon-color-dark:#4ade80;--qm-label:#166534;--qm-label-dark:#86efac;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </span>
                <span class="qm-label">WhatsApp</span>
            </a>

            {{-- Transaksi --}}
            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}" class="qm-card" style="--qm-bg:#fff7ed;--qm-border:#fed7aa;--qm-bg-dark:#431407;--qm-border-dark:#ea580c33;--qm-icon-bg:#ffedd5;--qm-icon-bg-dark:#5a1e0a;--qm-icon-color:#ea580c;--qm-icon-color-dark:#fdba74;--qm-label:#9a3412;--qm-label-dark:#fed7aa;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </span>
                <span class="qm-label">Transaksi</span>
            </a>

            {{-- Kalender Unit --}}
            <a href="{{ \App\Filament\Pages\VehicleSchedule::getUrl() }}" class="qm-card" style="--qm-bg:#fffbeb;--qm-border:#fde68a;--qm-bg-dark:#451a03;--qm-border-dark:#d9770633;--qm-icon-bg:#fef3c7;--qm-icon-bg-dark:#5c2a08;--qm-icon-color:#d97706;--qm-icon-color-dark:#fcd34d;--qm-label:#92400e;--qm-label-dark:#fde68a;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                        <rect x="7" y="14" width="3" height="3" rx="0.5" fill="currentColor" stroke="none"/>
                    </svg>
                </span>
                <span class="qm-label">Kalender Unit</span>
            </a>

            {{-- Checklist Keluar --}}
            <a href="{{ \App\Filament\Resources\AgreementResource::getUrl('index') }}" class="qm-card" style="--qm-bg:#f0fdf4;--qm-border:#bbf7d0;--qm-bg-dark:#052e16;--qm-border-dark:#15803d33;--qm-icon-bg:#dcfce7;--qm-icon-bg-dark:#073b1f;--qm-icon-color:#15803d;--qm-icon-color-dark:#4ade80;--qm-label:#14532d;--qm-label-dark:#bbf7d0;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </span>
                <span class="qm-label">Checklist Keluar</span>
            </a>

            {{-- Checklist Kembali --}}
            <a href="{{ \App\Filament\Resources\ReturnAgreementResource::getUrl('index') }}" class="qm-card" style="--qm-bg:#fdf2f8;--qm-border:#f9a8d4;--qm-bg-dark:#500724;--qm-border-dark:#db277733;--qm-icon-bg:#fce7f3;--qm-icon-bg-dark:#6b1030;--qm-icon-color:#db2777;--qm-icon-color-dark:#f9a8d4;--qm-label:#9d174d;--qm-label-dark:#fbcfe8;">
                <span class="qm-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="1 4 1 10 7 10"/>
                        <path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                        <polyline points="12 8 12 12 15 14"/>
                    </svg>
                </span>
                <span class="qm-label">Checklist Kembali</span>
            </a>

        </div>

        <style>
            .qm-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            @media (min-width: 640px) { .qm-grid { grid-template-columns: repeat(3, 1fr); } }
            @media (min-width: 1024px) { .qm-grid { grid-template-columns: repeat(4, 1fr); } }

            .qm-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 18px 10px;
                border-radius: 14px;
                text-decoration: none;
                border: 1px solid var(--qm-border);
                background-color: var(--qm-bg);
                transition: transform 0.18s ease, box-shadow 0.18s ease;
            }
            .dark .qm-card {
                background-color: var(--qm-bg-dark);
                border-color: var(--qm-border-dark);
            }
            .qm-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            }
            .qm-card:active { transform: translateY(-1px); }

            .qm-icon-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 52px;
                height: 52px;
                border-radius: 14px;
                background-color: var(--qm-icon-bg);
                color: var(--qm-icon-color);
                flex-shrink: 0;
            }
            .dark .qm-icon-wrap {
                background-color: var(--qm-icon-bg-dark);
                color: var(--qm-icon-color-dark);
            }

            .qm-label {
                font-size: 12px;
                font-weight: 600;
                text-align: center;
                line-height: 1.4;
                letter-spacing: 0.01em;
                color: var(--qm-label);
            }
            .dark .qm-label { color: var(--qm-label-dark); }
        </style>
    </x-filament::section>
</x-filament::widget>
