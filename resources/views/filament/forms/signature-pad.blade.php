@php
    $statePath = $getStatePath();
    $uid = 'sp_' . md5($statePath); // ID konsisten berdasarkan statePath
@endphp

@once
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endonce

<div
    x-data="{
        pad:     null,
        signed:  false,
        drawing: false,

        init() {
            this.$nextTick(() => {
                const canvas = this.$refs.canvas;
                const wrap   = canvas.parentElement;
                const ratio  = Math.max(window.devicePixelRatio || 1, 1);

                canvas.width  = wrap.clientWidth * ratio;
                canvas.height = 220 * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                this.pad = new SignaturePad(canvas, {
                    penColor: '#1e293b',
                    minWidth: 1.2,
                    maxWidth: 2.8,
                });

                // Muat TTD yang sudah ada
                const existing = $wire.get('{{ $statePath }}');
                if (existing) {
                    this.pad.fromDataURL(existing);
                    this.signed = true;
                }

                this.pad.addEventListener('beginStroke', () => {
                    this.drawing = true;
                });

                this.pad.addEventListener('endStroke', () => {
                    this.drawing = false;
                    if (!this.pad.isEmpty()) {
                        const dataUrl = this.pad.toDataURL('image/png');
                        this.signed = true;
                        $wire.set('{{ $statePath }}', dataUrl);
                    }
                });

                // Resize handler
                this._onResize = () => {
                    const data  = this.pad.toData();
                    const r     = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width  = wrap.clientWidth * r;
                    canvas.height = 220 * r;
                    canvas.getContext('2d').scale(r, r);
                    this.pad.clear();
                    if (data && data.length) this.pad.fromData(data);
                };
                window.addEventListener('resize', this._onResize);
            });
        },

        destroy() {
            if (this._onResize) window.removeEventListener('resize', this._onResize);
            if (this.pad) this.pad.off();
        },

        clear() {
            if (this.pad) this.pad.clear();
            this.signed  = false;
            this.drawing = false;
            $wire.set('{{ $statePath }}', null);
        }
    }"
    class="sp-wrap"
>
    {{-- Header --}}
    <div class="sp-header">
        <div class="sp-header-left">
            <div class="sp-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <div>
                <p class="sp-title">Tanda Tangan Digital</p>
                <p class="sp-subtitle">Tanda tangani di area bawah ini</p>
            </div>
        </div>
        <span class="sp-badge" :class="signed ? 'sp-badge--done' : 'sp-badge--empty'"
              x-text="signed ? '✓ Sudah TTD' : 'Belum TTD'"></span>
    </div>

    {{-- Canvas area --}}
    <div class="sp-canvas-area" :class="signed ? 'sp-canvas-area--signed' : ''">
        <canvas x-ref="canvas" class="sp-canvas" style="touch-action:none;display:block;width:100%;"></canvas>

        {{-- Placeholder --}}
        <div class="sp-placeholder" x-show="!signed && !drawing" x-transition>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
            </svg>
            Tanda tangan di sini
        </div>
    </div>

    {{-- Actions --}}
    <div class="sp-footer">
        <button type="button" class="sp-btn-clear" @click="clear">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus
        </button>
        <p class="sp-hint" x-show="!signed">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Gunakan jari atau stylus
        </p>
    </div>
</div>

<style>
    .sp-wrap {
        border-radius: 14px; overflow: hidden;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,.04);
    }
    .dark .sp-wrap { background: #1f2937; border-color: #374151; }

    .sp-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
    }
    .dark .sp-header { background: #111827; border-color: #374151; }
    .sp-header-left { display: flex; align-items: center; gap: 10px; }

    .sp-icon {
        width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        display: flex; align-items: center; justify-content: center;
        color: #fff; box-shadow: 0 2px 8px rgba(99,102,241,.3);
    }
    .sp-title   { font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; }
    .sp-subtitle { font-size: 11px; color: #94a3b8; margin-top: 1px; }
    .dark .sp-title { color: #f1f5f9; }

    .sp-badge {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px; border: 1px solid;
    }
    .sp-badge--empty { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .sp-badge--done  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .dark .sp-badge--empty { background: #451a03; color: #fcd34d; border-color: #78350f; }
    .dark .sp-badge--done  { background: #052e16; color: #4ade80; border-color: #14532d; }

    .sp-canvas-area {
        position: relative;
        background: #fafafa;
        border-bottom: 1px solid #f1f5f9;
        cursor: crosshair;
    }
    .dark .sp-canvas-area { background: #0f172a; border-color: #374151; }
    .sp-canvas-area--signed { background: #f8faff; }
    .dark .sp-canvas-area--signed { background: #1e1b4b; }

    /* Garis panduan */
    .sp-canvas-area::after {
        content: '';
        position: absolute; bottom: 36px; left: 16px; right: 16px; height: 1px;
        background: repeating-linear-gradient(90deg, #e2e8f0 0, #e2e8f0 5px, transparent 5px, transparent 11px);
        pointer-events: none;
    }
    .dark .sp-canvas-area::after {
        background: repeating-linear-gradient(90deg, #374151 0, #374151 5px, transparent 5px, transparent 11px);
    }

    .sp-canvas { height: 220px; }

    .sp-placeholder {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px; pointer-events: none;
        color: #cbd5e1; font-size: 12.5px; font-weight: 500;
    }
    .dark .sp-placeholder { color: #4b5563; }

    .sp-footer {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 10px 14px;
    }

    .sp-btn-clear {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 13px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600; cursor: pointer;
        background: #fef2f2; color: #dc2626;
        border: 1px solid #fecaca; font-family: inherit;
        transition: transform .15s, filter .15s;
    }
    .sp-btn-clear:hover { transform: translateY(-1px); filter: brightness(.93); }
    .dark .sp-btn-clear { background: #450a0a; color: #f87171; border-color: #7f1d1d; }

    .sp-hint {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; color: #94a3b8;
    }
</style>
