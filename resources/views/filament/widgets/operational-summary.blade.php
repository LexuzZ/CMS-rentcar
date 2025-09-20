<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-bold mb-4">
            📊 Ringkasan Operasional Bulan {{ now()->locale('id')->isoFormat('MMMM YYYY') }}
        </h2>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Top 5 Mobil Paling Sering Disewa --}}
            <div>
                <h3 class="text-md font-semibold mb-2">🔥 Top 5 Mobil Paling Sering Disewa</h3>
                <canvas id="topCarsChart" class="w-full h-64"></canvas>
                <ul class="mt-3 space-y-1 text-sm">
                    @foreach ($this->getTopCars()['top5'] as $item)
                        <li class="flex justify-between border-b pb-1">
                            <span>{{ $item->car->carModel->name }} ({{ $item->car->nopol }})</span>
                            <span class="font-bold">{{ $item->total }}x</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Top 5 Mobil Jarang Disewa --}}
            <div>
                <h3 class="text-md font-semibold mb-2">❄️ Top 5 Mobil Jarang Disewa</h3>
                <canvas id="lowCarsChart" class="w-full h-64"></canvas>
                <ul class="mt-3 space-y-1 text-sm">
                    @foreach ($this->getTopCars()['low5'] as $item)
                        <li class="flex justify-between border-b pb-1">
                            <span>{{ $item->car->carModel->name }} ({{ $item->car->nopol }})</span>
                            <span class="font-bold">{{ $item->total }}x</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-filament::section>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            const topCars = @json($this->getTopCars()['top5']->pluck('car.carModel.name'));
            const topValues = @json($this->getTopCars()['top5']->pluck('total'));

            const lowCars = @json($this->getTopCars()['low5']->pluck('car.carModel.name'));
            const lowValues = @json($this->getTopCars()['low5']->pluck('total'));

            new Chart(document.getElementById('topCarsChart'), {
                type: 'bar',
                data: {
                    labels: topCars,
                    datasets: [{
                        label: 'Jumlah Sewa',
                        data: topValues,
                        backgroundColor: '#16a34a'
                    }]
                }
            });

            new Chart(document.getElementById('lowCarsChart'), {
                type: 'bar',
                data: {
                    labels: lowCars,
                    datasets: [{
                        label: 'Jumlah Sewa',
                        data: lowValues,
                        backgroundColor: '#dc2626'
                    }]
                }
            });
        });
    </script>
</x-filament-widgets::widget>
