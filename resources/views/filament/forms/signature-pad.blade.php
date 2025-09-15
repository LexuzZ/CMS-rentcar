@php
    $statePath = $getStatePath();
@endphp

<div x-data="signaturePad({ state: $wire.entangle('{{ $statePath }}') })">
    <canvas x-ref="canvas"
        class="border border-gray-300 rounded-lg shadow-sm w-full"
        style="height: 250px;"></canvas>

    <div class="flex gap-2 mt-2">
        <x-filament::button color="danger" size="sm" @click="clear">Hapus</x-filament::button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', ({ state }) => ({
            signaturePadInstance: null,
            state: state,

            init() {
                this.$nextTick(() => {
                    const canvas = this.$refs.canvas;

                    const resizeCanvas = () => {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        // simpan data lama sebelum resize
                        let data = this.signaturePadInstance ? this.signaturePadInstance.toData() : null;

                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = 250 * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);

                        if (this.signaturePadInstance) {
                            this.signaturePadInstance.clear();
                            if (data) {
                                this.signaturePadInstance.fromData(data);
                            }
                        }
                    };

                    this.signaturePadInstance = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(255,255,255,0)',
                    });

                    if (this.state) {
                        this.signaturePadInstance.fromDataURL(this.state);
                    }

                    this.signaturePadInstance.addEventListener("endStroke", () => {
                        this.state = this.signaturePadInstance.toDataURL();
                    });

                    // panggil sekali waktu awal
                    resizeCanvas();
                    // update kalau ukuran layar berubah
                    window.addEventListener("resize", resizeCanvas);
                });
            },

            clear() {
                if (this.signaturePadInstance) {
                    this.signaturePadInstance.clear();
                }
                this.state = null;
            }
        }))
    })
</script>
