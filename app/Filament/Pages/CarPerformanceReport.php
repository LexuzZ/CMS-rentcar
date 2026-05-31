<x-filament-panels::page>
<div x-data="{
    isModalOpen: false,
    modalBookings: [],
    modalCarName: '',
    modalCarId: null,
    reportDateString: @entangle('reportDateString')
}">

    {{-- Filter --}}
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    {{-- Report Table --}}
    <x-filament::section class="mt-4">
        <x-slot name="heading">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="vr-heading-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                    </span>
                    <div>
                        <span class="vr-heading-title">Ringkasan Kinerja</span>
                        <span class="vr-heading-period">{{ $reportTitle }}</span>
                    </div>
                </div>
                <x-filament::button
                    wire:click="exportReport"
                    icon="heroicon-o-arrow-down-tray"
                    color="gray"
                    wire:loading.attr="disabled"
                    wire:target="exportReport">
                    Export Excel
                </x-filament::button>
            </div>
        </x-slot>

        <div class="vr-table-wrap">
            <table class="vr-table">
                <thead>
                    <tr>
                        <th class="vr-th">Kendaraan</th>
                        <th class="vr-th vr-th--center">Total Hari</th>
                        <th class="vr-th vr-th--right">Pendapatan</th>
                        <th class="vr-th vr-th--right">Harga Pokok</th>
                        <th class="vr-th vr-th--right">Laba Kotor</th>
                        <th class="vr-th vr-th--center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportTableData as $data)
                    @php
                        $laba = $data['revenue'] - $data['cost'];
                        $margin = $data['revenue'] > 0 ? round($laba / $data['revenue'] * 100) : 0;
                    @endphp
                    <tr class="vr-tr">
                        <td class="vr-td">
                            <div class="vr-car-name">{{ $data['model'] }}</div>
                            <span class="vr-nopol">{{ $data['nopol'] }}</span>
                        </td>
                        <td class="vr-td vr-td--center">
                            <span class="vr-days-badge">{{ $data['days_rented'] }} hari</span>
                        </td>
                        <td class="vr-td vr-td--right">
                            <span class="vr-revenue">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</span>
                        </td>
                        <td class="vr-td vr-td--right">
                            <span class="vr-cost">Rp {{ number_format($data['cost'], 0, ',', '.') }}</span>
                        </td>
                        <td class="vr-td vr-td--right">
                            <span class="vr-profit {{ $laba >= 0 ? 'vr-profit--pos' : 'vr-profit--neg' }}">
                                Rp {{ number_format($laba, 0, ',', '.') }}
                            </span>
                            <span class="vr-margin {{ $margin >= 0 ? 'vr-margin--pos' : 'vr-margin--neg' }}">
                                {{ $margin }}%
                            </span>
                        </td>
                        <td class="vr-td vr-td--center">
                            <button
                                @click="isModalOpen = true; modalBookings = @js($data['bookings']); modalCarName = '{{ $data['model'] }} ({{ $data['nopol'] }})'; modalCarId = {{ $data['car_id'] }}"
                                class="vr-detail-btn">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                Lihat
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="vr-empty">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                                    <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                                </svg>
                                <p>Tidak ada data kinerja untuk periode ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- MODAL --}}
    <div
        x-show="isModalOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display:none">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="isModalOpen = false"></div>

        {{-- Modal panel --}}
        <div class="vm-panel relative" @click.stop>

            {{-- Modal header --}}
            <div class="vm-header">
                <div class="vm-header-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                        <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="vm-title" x-text="`Detail Booking`"></h3>
                    <p class="vm-subtitle" x-text="modalCarName"></p>
                </div>
                <button @click="isModalOpen = false" class="vm-close" aria-label="Tutup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="vm-body">
                <div class="vm-table-wrap">
                    <table class="vm-table">
                        <thead>
                            <tr>
                                <th class="vm-th">Pelanggan</th>
                                <th class="vm-th vm-th--center">Tanggal Sewa</th>
                                <th class="vm-th vm-th--right">Pendapatan</th>
                                <th class="vm-th vm-th--right">Harga Pokok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="booking in modalBookings" :key="booking.id">
                                <tr class="vm-tr">
                                    <td class="vm-td vm-td--customer" x-text="booking.customer"></td>
                                    <td class="vm-td vm-td--center">
                                        <span x-text="new Date(booking.start).toLocaleDateString('id-ID', { day:'numeric', month:'short' })"></span>
                                        <span class="vm-date-sep">→</span>
                                        <span x-text="new Date(booking.end).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' })"></span>
                                    </td>
                                    <td class="vm-td vm-td--revenue vm-td--right"
                                        x-text="`Rp ${Number(booking.revenue).toLocaleString('id-ID')}`"></td>
                                    <td class="vm-td vm-td--right vm-td--cost"
                                        x-text="`Rp ${Number(booking.cost).toLocaleString('id-ID')}`"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="vm-footer">
                <a :href="`/reports/export-car-bookings/${modalCarId}/${reportDateString.split('-')[0]}/${reportDateString.split('-')[1]}`"
                   class="vm-export-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export CSV
                </a>
                <button @click="isModalOpen = false" class="vm-close-btn">Tutup</button>
            </div>
        </div>
    </div>

</div>

<style>
    /* ── Heading ── */
    .vr-heading-icon {
        width:32px;height:32px;border-radius:9px;flex-shrink:0;
        background:linear-gradient(135deg,#6366f1,#4f46e5);
        display:flex;align-items:center;justify-content:center;
        color:#fff;box-shadow:0 3px 10px rgba(99,102,241,.3);
    }
    .vr-heading-title { font-size:15px;font-weight:700;color:inherit;display:block; }
    .vr-heading-period {
        font-size:12px;font-weight:500;color:#6366f1;
        background:#eef2ff;border-radius:100px;padding:1px 10px;
        margin-left:8px;
    }
    .dark .vr-heading-period { background:#1e1b4b;color:#a5b4fc; }

    /* ── Main table ── */
    .vr-table-wrap { overflow-x:auto;border-radius:12px;border:1px solid #e5e7eb; }
    .dark .vr-table-wrap { border-color:#374151; }

    .vr-table { width:100%;border-collapse:collapse; }

    .vr-th {
        padding:10px 14px;
        font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
        color:#9ca3af;background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
    }
    .dark .vr-th { background:#111827;border-color:#374151;color:#6b7280; }
    .vr-th--center { text-align:center; }
    .vr-th--right  { text-align:right; }

    .vr-tr { border-bottom:1px solid #f1f5f9;transition:background .12s; }
    .dark .vr-tr { border-color:#374151; }
    .vr-tr:last-child { border-bottom:none; }
    .vr-tr:hover { background:#f8fafc; }
    .dark .vr-tr:hover { background:#111827; }

    .vr-td { padding:12px 14px;font-size:13px; }
    .vr-td--center { text-align:center; }
    .vr-td--right  { text-align:right; }

    /* Kendaraan */
    .vr-car-name { font-weight:700;color:#111827;margin-bottom:3px; }
    .dark .vr-car-name { color:#f3f4f6; }
    .vr-nopol {
        display:inline-block;font-size:10px;font-weight:700;
        font-family:ui-monospace,monospace;letter-spacing:.05em;
        background:#fef3c7;color:#92400e;
        border:1px solid #fde68a;border-radius:4px;padding:1px 7px;
    }
    .dark .vr-nopol { background:#451a03;color:#fcd34d;border-color:#78350f; }

    /* Days badge */
    .vr-days-badge {
        display:inline-block;
        background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;
        border-radius:100px;padding:2px 10px;
        font-size:11px;font-weight:700;
    }
    .dark .vr-days-badge { background:#1e3a5f;color:#93c5fd;border-color:#1d4ed833; }

    /* Revenue / Cost */
    .vr-revenue { font-weight:700;color:#15803d; }
    .dark .vr-revenue { color:#4ade80; }
    .vr-cost { color:#6b7280; }

    /* Profit */
    .vr-profit { display:block;font-weight:700;font-size:13px; }
    .vr-profit--pos { color:#15803d; }
    .vr-profit--neg { color:#b91c1c; }
    .dark .vr-profit--pos { color:#4ade80; }
    .dark .vr-profit--neg { color:#f87171; }
    .vr-margin {
        display:inline-block;font-size:10px;font-weight:700;
        margin-top:3px;padding:1px 7px;border-radius:100px;
    }
    .vr-margin--pos { background:#dcfce7;color:#15803d; }
    .vr-margin--neg { background:#fee2e2;color:#b91c1c; }
    .dark .vr-margin--pos { background:#052e16;color:#4ade80; }
    .dark .vr-margin--neg { background:#450a0a;color:#f87171; }

    /* Detail button */
    .vr-detail-btn {
        display:inline-flex;align-items:center;gap:5px;
        padding:5px 12px;border-radius:8px;
        font-size:12px;font-weight:600;cursor:pointer;
        background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;
        transition:background .15s,border-color .15s;
    }
    .vr-detail-btn:hover { background:#dbeafe;border-color:#93c5fd; }
    .dark .vr-detail-btn { background:#1e3a5f;color:#93c5fd;border-color:#1d4ed833; }
    .dark .vr-detail-btn:hover { background:#1e40af; }

    /* Empty */
    .vr-empty {
        display:flex;flex-direction:column;align-items:center;gap:8px;
        padding:40px 20px;color:#9ca3af;text-align:center;font-size:13px;
    }

    /* ── MODAL ── */
    .vm-panel {
        background:#fff;border-radius:16px;width:100%;max-width:680px;
        margin:16px;overflow:hidden;
        box-shadow:0 25px 60px rgba(0,0,0,.3),0 2px 8px rgba(0,0,0,.1);
    }
    .dark .vm-panel { background:#1f2937; }

    .vm-header {
        display:flex;align-items:center;gap:12px;
        padding:18px 20px;
        border-bottom:1px solid #f1f5f9;
        background:#fafafa;
    }
    .dark .vm-header { background:#111827;border-color:#374151; }

    .vm-header-icon {
        width:38px;height:38px;border-radius:10px;flex-shrink:0;
        background:linear-gradient(135deg,#6366f1,#4f46e5);
        display:flex;align-items:center;justify-content:center;
        color:#fff;box-shadow:0 3px 8px rgba(99,102,241,.3);
    }
    .vm-title { font-size:15px;font-weight:700;color:#0f172a;margin:0; }
    .dark .vm-title { color:#f1f5f9; }
    .vm-subtitle { font-size:12px;color:#64748b;margin-top:2px; }
    .dark .vm-subtitle { color:#94a3b8; }

    .vm-close {
        margin-left:auto;width:30px;height:30px;border-radius:8px;
        display:flex;align-items:center;justify-content:center;
        background:#f1f5f9;color:#6b7280;border:none;cursor:pointer;
        transition:background .15s,color .15s;
    }
    .vm-close:hover { background:#e2e8f0;color:#374151; }
    .dark .vm-close { background:#374151;color:#9ca3af; }
    .dark .vm-close:hover { background:#4b5563;color:#d1d5db; }

    .vm-body { padding:16px 20px;max-height:380px;overflow-y:auto; }

    .vm-table-wrap { border-radius:10px;border:1px solid #e5e7eb;overflow:hidden; }
    .dark .vm-table-wrap { border-color:#374151; }

    .vm-table { width:100%;border-collapse:collapse; }

    .vm-th {
        padding:9px 12px;
        font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
        color:#9ca3af;background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
    }
    .dark .vm-th { background:#111827;border-color:#374151;color:#6b7280; }
    .vm-th--center { text-align:center; }
    .vm-th--right  { text-align:right; }

    .vm-tr { border-bottom:1px solid #f1f5f9;transition:background .1s; }
    .dark .vm-tr { border-color:#374151; }
    .vm-tr:last-child { border-bottom:none; }
    .vm-tr:hover { background:#f8fafc; }
    .dark .vm-tr:hover { background:#111827; }

    .vm-td { padding:10px 12px;font-size:12.5px;color:#374151; }
    .dark .vm-td { color:#d1d5db; }
    .vm-td--center { text-align:center; }
    .vm-td--right  { text-align:right; }
    .vm-td--customer { font-weight:600;color:#111827; }
    .dark .vm-td--customer { color:#f3f4f6; }
    .vm-td--revenue { color:#15803d;font-weight:700; }
    .dark .vm-td--revenue { color:#4ade80; }
    .vm-td--cost { color:#6b7280; }

    .vm-date-sep { color:#9ca3af;margin:0 4px; }

    .vm-footer {
        display:flex;align-items:center;justify-content:space-between;gap:10px;
        padding:14px 20px;
        border-top:1px solid #f1f5f9;
        background:#fafafa;
    }
    .dark .vm-footer { background:#111827;border-color:#374151; }

    .vm-export-btn {
        display:inline-flex;align-items:center;gap:7px;
        padding:8px 16px;border-radius:9px;
        font-size:13px;font-weight:700;
        background:linear-gradient(135deg,#22c55e,#16a34a);
        color:#fff;text-decoration:none;
        box-shadow:0 3px 10px rgba(34,197,94,.3);
        transition:filter .15s,transform .15s;
    }
    .vm-export-btn:hover { filter:brightness(.92);transform:translateY(-1px); }

    .vm-close-btn {
        padding:8px 18px;border-radius:9px;
        font-size:13px;font-weight:600;
        background:#f1f5f9;color:#374151;
        border:1.5px solid #e2e8f0;cursor:pointer;
        transition:background .15s,border-color .15s;
        font-family:inherit;
    }
    .vm-close-btn:hover { background:#e2e8f0;border-color:#cbd5e1; }
    .dark .vm-close-btn { background:#374151;color:#d1d5db;border-color:#4b5563; }
    .dark .vm-close-btn:hover { background:#4b5563; }
</style>
</x-filament-panels::page>
