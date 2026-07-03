@php
    $statePath = $getStatePath();
@endphp

<div
    x-data="{
        state: $wire.entangle('{{ $statePath }}'),
        loading: false,

        processFile(file) {
            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file maksimal 10MB.');
                return;
            }

            this.loading = true;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas  = document.createElement('canvas');
                    const ctx     = canvas.getContext('2d');
                    const MAX     = 800;
                    let w         = img.width;
                    let h         = img.height;

                    if (w > h) {
                        if (w > MAX) { h = Math.round(h * MAX / w); w = MAX; }
                    } else {
                        if (h > MAX) { w = Math.round(w * MAX / h); h = MAX; }
                    }

                    canvas.width  = w;
                    canvas.height = h;
                    ctx.drawImage(img, 0, 0, w, h);

                    // ✅ FIX: pakai $data.state agar reaktif di dalam nested callback
                    this.state   = canvas.toDataURL('image/jpeg', 0.72);
                    this.loading = false;
                };
                img.onerror = () => { this.loading = false; };
                img.src = e.target.result;
            };
            reader.onerror = () => { this.loading = false; };
            reader.readAsDataURL(file);
        }
    }"
    class="cc-wrap"
>
    {{-- Preview --}}
    <div x-show="state" x-transition class="cc-preview">
        <img x-bind:src="state" alt="Foto" class="cc-img" />
        <div class="cc-img-overlay">
            <span class="cc-img-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Foto tersimpan
            </span>
        </div>
    </div>

    {{-- Empty state --}}
    <div x-show="!state && !loading" x-transition class="cc-empty">
        <div class="cc-empty-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
        </div>
        <p class="cc-empty-text">Belum ada foto</p>
        <p class="cc-empty-hint">Tap tombol di bawah untuk mengambil foto</p>
    </div>

    {{-- Loading state --}}
    <div x-show="loading" x-transition class="cc-loading">
        <svg class="cc-spinner" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="#e2e8f0" stroke-width="3"/>
            <path d="M12 2a10 10 0 0 1 10 10" stroke="#6366f1" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p class="cc-loading-text">Memproses foto…</p>
    </div>

    {{-- Hidden input (galeri) --}}
    <input
        type="file"
        accept="image/*"
        x-ref="fileGallery"
        class="cc-hidden"
        @change="processFile($event.target.files[0]); $event.target.value = null"
    >

    {{-- Hidden input (kamera langsung — mobile) --}}
    <input
        type="file"
        accept="image/*"
        capture="environment"
        x-ref="fileCamera"
        class="cc-hidden"
        @change="processFile($event.target.files[0]); $event.target.value = null"
    >

    {{-- Action buttons --}}
    <div class="cc-actions">
        {{-- Kamera (mobile) --}}
        <button
            type="button"
            class="cc-btn cc-btn--primary"
            @click="$refs.fileCamera.click()"
            :disabled="loading"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
            <span x-text="state ? 'Foto Ulang' : 'Ambil Foto'"></span>
        </button>

        {{-- Galeri --}}
        <button
            type="button"
            class="cc-btn cc-btn--ghost"
            @click="$refs.fileGallery.click()"
            :disabled="loading"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Galeri
        </button>

        {{-- Hapus --}}
        <button
            type="button"
            class="cc-btn cc-btn--danger"
            x-show="state"
            x-transition
            @click="state = null"
            :disabled="loading"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus
        </button>
    </div>
</div>

<style>
    .cc-wrap {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
        border: 1.5px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
        transition: border-color .2s;
    }
    .dark .cc-wrap { background: #1f2937; border-color: #4b5563; }
    .cc-wrap:has(.cc-img) { border-color: #6366f1; border-style: solid; background: #fafbff; }
    .dark .cc-wrap:has(.cc-img) { background: #1e1b4b; border-color: #4338ca; }

    /* Hidden inputs */
    .cc-hidden { display: none; }

    /* Preview */
    .cc-preview { position: relative; width: 100%; border-radius: 10px; overflow: hidden; }
    .cc-img {
        width: 100%; max-height: 220px;
        object-fit: cover; display: block;
        border-radius: 10px;
        box-shadow: 0 4px 14px rgba(0,0,0,.12);
    }
    .cc-img-overlay {
        position: absolute; bottom: 8px; left: 8px;
    }
    .cc-img-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(22,163,74,.9); color: #fff;
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px;
        backdrop-filter: blur(4px);
    }

    /* Empty state */
    .cc-empty {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 20px 16px; text-align: center;
    }
    .cc-empty-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8; margin-bottom: 4px;
    }
    .dark .cc-empty-icon { background: #374151; border-color: #4b5563; }
    .cc-empty-text { font-size: 13px; font-weight: 600; color: #64748b; }
    .dark .cc-empty-text { color: #9ca3af; }
    .cc-empty-hint { font-size: 11.5px; color: #94a3b8; }

    /* Loading */
    .cc-loading {
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        padding: 20px;
    }
    .cc-spinner {
        width: 36px; height: 36px;
        animation: cc-spin .8s linear infinite;
    }
    @keyframes cc-spin { to { transform: rotate(360deg); } }
    .cc-loading-text { font-size: 12px; color: #94a3b8; font-weight: 500; }

    /* Actions */
    .cc-actions {
        display: flex; gap: 8px; flex-wrap: wrap;
    }

    .cc-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        border: none; cursor: pointer;
        transition: transform .15s, filter .15s, opacity .15s;
        font-family: inherit;
        flex: 1; justify-content: center;
    }
    .cc-btn:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(.92); }
    .cc-btn:active:not(:disabled) { transform: translateY(0); }
    .cc-btn:disabled { opacity: .5; cursor: not-allowed; }

    .cc-btn--primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        box-shadow: 0 3px 10px rgba(99,102,241,.3);
    }
    .cc-btn--ghost {
        background: #f1f5f9; color: #374151;
        border: 1.5px solid #e2e8f0;
    }
    .dark .cc-btn--ghost { background: #374151; color: #d1d5db; border-color: #4b5563; }

    .cc-btn--danger {
        background: #fff1f2; color: #dc2626;
        border: 1.5px solid #fecaca;
    }
    .dark .cc-btn--danger { background: #450a0a; color: #f87171; border-color: #7f1d1d; }
</style>
