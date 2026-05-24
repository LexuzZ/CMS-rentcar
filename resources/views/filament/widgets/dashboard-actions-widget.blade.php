<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Menu
        </x-slot>

        <div class="qm-grid">

            {{-- Form Sewa --}}
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('create') }}" class="qm-card qm-card--light">
                <span class="qm-icon">🧾</span>
                <span class="qm-label">Form Sewa</span>
            </a>

            {{-- Order Via Customer --}}
            <a href="https://adminsemetonpesiarlombok.id/order" target="_blank" class="qm-card qm-card--dark">
                <span class="qm-icon">🛒</span>
                <span class="qm-label">Order Via Cust.</span>
            </a>

            {{-- WhatsApp --}}
            <a href="https://web.whatsapp.com" target="_blank" class="qm-card qm-card--whatsapp">
                <span class="qm-icon qm-icon--whatsapp">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26">
                        <path d="M20.52 3.48A11.8 11.8 0 0012.04 0C5.52 0 .24 5.28.24 11.76c0 2.04.48 4.08 1.44 5.88L0 24l6.6-1.68a11.8 11.8 0 005.4 1.32h.04c6.48 0 11.76-5.28 11.76-11.76 0-3.12-1.2-6.12-3.28-8.4z"/>
                    </svg>
                </span>
                <span class="qm-label">WhatsApp</span>
            </a>

            {{-- Transaksi --}}
            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}" class="qm-card qm-card--light">
                <span class="qm-icon">💳</span>
                <span class="qm-label">Transaksi</span>
            </a>

            {{-- Kalender Unit --}}
            <a href="{{ \App\Filament\Pages\VehicleSchedule::getUrl() }}" class="qm-card qm-card--dark">
                <span class="qm-icon">📅</span>
                <span class="qm-label">Kalender Unit</span>
            </a>

            {{-- Checklist Keluar --}}
            <a href="{{ \App\Filament\Resources\AgreementResource::getUrl('index') }}" class="qm-card qm-card--light">
                <span class="qm-icon">✅</span>
                <span class="qm-label">Checklist Keluar</span>
            </a>

            {{-- Checklist Kembali --}}
            <a href="{{ \App\Filament\Resources\ReturnAgreementResource::getUrl('index') }}" class="qm-card qm-card--dark">
                <span class="qm-icon">📦</span>
                <span class="qm-label">Checklist Kembali</span>
            </a>

        </div>

        <style>
            .qm-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            @media (min-width: 640px) {
                .qm-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (min-width: 1024px) {
                .qm-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            .qm-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 16px 8px;
                border-radius: 12px;
                text-decoration: none;
                transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
                border: 1px solid transparent;
            }

            .qm-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
                opacity: 0.92;
            }

            .qm-card:active {
                transform: translateY(0);
                box-shadow: none;
            }

            /* Light variant */
            .qm-card--light {
                background-color: #f9fafb;
                border-color: #e5e7eb;
            }

            .dark .qm-card--light {
                background-color: #1f2937;
                border-color: #374151;
            }

            /* Dark variant */
            .qm-card--dark {
                background-color: #111827;
                border-color: #1f2937;
            }

            .dark .qm-card--dark {
                background-color: #0f172a;
                border-color: #1e293b;
            }

            /* WhatsApp variant */
            .qm-card--whatsapp {
                background-color: #dcfce7;
                border-color: #bbf7d0;
            }

            .dark .qm-card--whatsapp {
                background-color: #052e16;
                border-color: #14532d;
            }

            .qm-icon {
                font-size: 28px;
                line-height: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 48px;
                height: 48px;
                border-radius: 10px;
                background-color: rgba(255, 255, 255, 0.08);
            }

            .qm-card--light .qm-icon {
                background-color: rgba(0, 0, 0, 0.04);
            }

            .qm-icon--whatsapp {
                color: #16a34a;
                font-size: unset;
            }

            .dark .qm-icon--whatsapp {
                color: #4ade80;
            }

            .qm-label {
                font-size: 12px;
                font-weight: 500;
                text-align: center;
                line-height: 1.3;
                letter-spacing: 0.01em;
            }

            .qm-card--light .qm-label {
                color: #374151;
            }

            .dark .qm-card--light .qm-label {
                color: #e5e7eb;
            }

            .qm-card--dark .qm-label {
                color: #d1d5db;
            }

            .dark .qm-card--dark .qm-label {
                color: #94a3b8;
            }

            .qm-card--whatsapp .qm-label {
                color: #166534;
            }

            .dark .qm-card--whatsapp .qm-label {
                color: #86efac;
            }
        </style>
    </x-filament::section>
</x-filament::widget>
