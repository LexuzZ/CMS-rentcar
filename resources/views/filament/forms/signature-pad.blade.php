@php
    $statePath = $getStatePath();
@endphp

{{-- Load CDN sekali saja, cek apakah sudah ada --}}
@once
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endonce

<div
    x-data="signaturePad_{{ \Illuminate\Support\Str::random(6) }}()"
    x-init="init($wire, '{{ $statePath }}')"
    class="sp-wrap"
>
    {{-- Header --}}
    <div class="sp-header">
        <div class="sp-header-left">
            <div class="sp-header-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <div>
                <p class="sp-title">Tanda Tangan Digital</p>
                <p class="sp-subtitle">Tanda tangani di area bawah ini</p>
            </div>
        </div>

        {{-- Status badge --}}
        <span
            class="sp-status"
            :class="signed ? 'sp-status--done' : 'sp-status--empty'"
            x-text="signed ? '✓ Sudah TTD' : 'Belum TTD'"
        ></span>
    </div>

    {{-- Canvas area --}}
    <div class="sp-canvas-wrap" :class="signed ? 'sp-canvas-wrap--signed' : ''">
        <canvas
            x-ref="canvas"
            class="sp-canvas"
            style="touch-action: none;"
        ></canvas>

        {{-- Placeholder --}}
        <div class="sp-placeholder" x-show="!signed && !drawing" x-transition>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
            </svg>
            <span>Tanda tangan di sini</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="sp-actions">
        <button type="button" class="sp-btn sp-btn--clear" @click="clear">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus Tanda Tangan
        </button>

        <p class="sp-hint" x-show="!signed">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Gunakan jari atau stylus untuk menandatangani
        </p>
    </div>
</div>

<script>
(function() {
    // Buat nama unik agar tidak konflik jika ada multiple instance
    const uid = '{{ \Illuminate\Support\Str::random(6) }}';
    const fnName = 'signaturePad_' + uid;

    // Guard: daftarkan hanya sekali saat alpine ready
    function register() {
        if (window.Alpine && window.SignaturePad) {
            window.Alpine.data(fnName, () => ({
                pad:     null,
                state:   null,
                signed:  false,
                drawing: false,
                _wire:   null,
                _path:   null,

                init(wire, statePath) {
                    this._wire = wire;
                    this._path = statePath;

                    // Ambil nilai awal dari wire
                    this.state  = wire.get(statePath);
                    this.signed = !!this.state;

                    this.$nextTick(() => {
                        this._setupCanvas();

                        // Muat TTD yang sudah ada
                        if (this.state) {
                            this.pad.fromDataURL(this.state);
                        }

                        // Event: mulai menggambar
                        this.pad.addEventListener('beginStroke', () => {
                            this.drawing = true;
                        });

                        // Event: selesai 1 stroke → simpan
                        this.pad.addEventListener('endStroke', () => {
                            this.drawing = false;
                            if (!this.pad.isEmpty()) {
                                this.state  = this.pad.toDataURL('image/png');
                                this.signed = true;
                                // ✅ FIX: sync ke Livewire via set(), bukan assignment langsung
                                this._wire.set(this._path, this.state);
                            }
                        });
                    });

                    // ✅ FIX: resize handler agar canvas tidak blur saat resize
                    this._resizeHandler = () => this._resizeCanvas();
                    window.addEventListener('resize', this._resizeHandler);
                },

                destroy() {
                    window.removeEventListener('resize', this._resizeHandler);
                    if (this.pad) this.pad.off();
                },

                _setupCanvas() {
                    const canvas = this.$refs.canvas;
                    const wrap   = canvas.parentElement;

                    // ✅ FIX: set CSS size dulu, baru internal size
                    canvas.style.width  = '100%';
                    canvas.style.height = '220px';

                    const ratio  = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width  = wrap.clientWidth  * ratio;
                    canvas.height = 220              * ratio;

                    // ✅ FIX: scale SETELAH set width/height
                    canvas.getContext('2d').scale(ratio, ratio);

                    this.pad = new SignaturePad(canvas, {
                        penColor:   '#1e293b',
                        minWidth:   1.2,
                        maxWidth:   2.8,
                        throttle:   16,
                    });
                },

                _resizeCanvas() {
                    if (!this.pad) return;

                    const canvas  = this.$refs.canvas;
                    const wrap    = canvas.parentElement;
                    const ratio   = Math.max(window.devicePixelRatio || 1, 1);

                    // Simpan data sebelum resize
                    const data = this.pad.toData();

                    canvas.width  = wrap.clientWidth * ratio;
                    canvas.height = 220             * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);

                    this.pad.clear();

                    // Restore data setelah resize
                    if (data && data.length) {
                        this.pad.fromData(data);
                    }
                },

                clear() {
                    if (this.pad) this.pad.clear();
                    this.state  = null;
                    this.signed = false;
                    // ✅ FIX: sync clear ke Livewire
                    this._wire.set(this._path, null);
                },
            }));
        } else {
            // Tunggu sampai keduanya siap
            setTimeout(register, 80);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', register);
    } else {
        register();
    }
})();
</script>

<style>
    .sp-wrap {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,.04);
    }
    .dark .sp-wrap { background: #1f2937; border-color: #374151; }

    /* Header */
    .sp-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
    }
    .dark .sp-header { background: #111827; border-color: #374151; }

    .sp-header-left { display: flex; align-items: center; gap: 10px; }

    .sp-header-icon {
        width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        display: flex; align-items: center; justify-content: center;
        color: #fff; box-shadow: 0 2px 8px rgba(99,102,241,.3);
    }

    .sp-title { font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; }
    .dark .sp-title { color: #f1f5f9; }
    .sp-subtitle { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* Status badge */
    .sp-status {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px; border: 1px solid;
        white-space: nowrap;
    }
    .sp-status--empty { background: #fff7ed; color: #92400e; border-color: #fde68a; }
    .sp-status--done  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .dark .sp-status--empty { background: #451a03; color: #fcd34d; border-color: #78350f; }
    .dark .sp-status--done  { background: #052e16; color: #4ade80; border-color: #14532d; }

    /* Canvas wrap */
    .sp-canvas-wrap {
        position: relative;
        background: #fdfdfd;
        border-bottom: 1px solid #f1f5f9;
        cursor: crosshair;
        transition: background .2s;
    }
    .dark .sp-canvas-wrap { background: #0f172a; border-color: #374151; }
    .sp-canvas-wrap--signed { background: #fafbff; }
    .dark .sp-canvas-wrap--signed { background: #1e1b4b; }

    .sp-canvas { display: block; width: 100%; }

    /* Guide lines */
    .sp-canvas-wrap::after {
        content: '';
        position: absolute;
        bottom: 40px; left: 16px; right: 16px;
        height: 1px;
        background: repeating-linear-gradient(
            90deg, #e2e8f0 0, #e2e8f0 6px, transparent 6px, transparent 12px
        );
        pointer-events: none;
    }
    .dark .sp-canvas-wrap::after {
        background: repeating-linear-gradient(
            90deg, #374151 0, #374151 6px, transparent 6px, transparent 12px
        );
    }

    /* Placeholder */
    .sp-placeholder {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px; pointer-events: none;
        color: #cbd5e1; font-size: 12.5px; font-weight: 500;
    }
    .dark .sp-placeholder { color: #4b5563; }

    /* Actions */
    .sp-actions {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 12px 16px; flex-wrap: wrap;
    }

    .sp-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600; cursor: pointer;
        border: none; font-family: inherit;
        transition: transform .15s, filter .15s;
    }
    .sp-btn:hover { transform: translateY(-1px); filter: brightness(.93); }
    .sp-btn:active { transform: translateY(0); }

    .sp-btn--clear {
        background: #fef2f2; color: #dc2626;
        border: 1px solid #fecaca;
    }
    .dark .sp-btn--clear { background: #450a0a; color: #f87171; border-color: #7f1d1d; }

    .sp-hint {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; color: #94a3b8;
    }
</style>
