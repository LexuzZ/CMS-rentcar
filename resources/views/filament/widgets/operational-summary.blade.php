<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Judul --}}
        <h2 class="text-2xl font-bold">📊 Ringkasan Operasional Bulan {{ now()->isoFormat('MMMM YYYY') }}</h2>

        {{-- Ringkasan Angka --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-100 dark:bg-blue-900 rounded-lg shadow text-center">
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-200">{{ $this->getData()['totalSewa'] }}</p>
                <p class="text-gray-600 dark:text-gray-300">Total Sewa</p>
            </div>
            <div class="p-4 bg-green-100 dark:bg-green-900 rounded-lg shadow text-center">
                <p class="text-2xl font-bold text-green-700 dark:text-green-200">
                    Rp {{ number_format($this->getData()['pendapatan'], 0, ',', '.') }}
                </p>
                <p class="text-gray-600 dark:text-gray-300">Pendapatan</p>
            </div>
            <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg shadow text-center">
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-200">
                    Rp {{ number_format($this->getData()['denda'], 0, ',', '.') }}
                </p>
                <p class="text-gray-600 dark:text-gray-300">Denda</p>
            </div>
            <div class="p-4 bg-purple-100 dark:bg-purple-900 rounded-lg shadow text-center">
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-200">
                    Rp {{ number_format($this->getData()['keuntungan'], 0, ',', '.') }}
                </p>
                <p class="text-gray-600 dark:text-gray-300">Keuntungan</p>
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Top 5 mobil terlaris --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="font-semibold mb-3">🚗 Top 5 Mobil Paling Sering Disewa</h3>
                <canvas id="topCarsChart"></canvas>
            </div>

            {{-- Top 5 mobil jarang disewa --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="font-semibold mb-3">🚙 Top 5 Mobil Jarang Disewa</h3>
                <canvas id="lowCarsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ChartJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const topCars = @json($this->getData()['topCars']);
        const lowCars = @json($this->getData()['lowCars']);

        // Top Cars Pie Chart
        new Chart(document.getElementById('topCarsChart'), {
            type: 'pie',
            data: {
                labels: topCars.map(c => c.car.car_model.name + ' (' + c.car.nopol + ')'),
                datasets: [{
                    data: topCars.map(c => c.total),
                    backgroundColor: ['#3b82f6','#10b981','#f97316','#facc15','#a855f7'],
                }]
            }
        });

        // Low Cars Bar Chart
        new Chart(document.getElementById('lowCarsChart'), {
            type: 'bar',
            data: {
                labels: lowCars.map(c => c.car.car_model.name + ' (' + c.car.nopol + ')'),
                datasets: [{
                    data: lowCars.map(c => c.total),
                    backgroundColor: '#f87171',
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
    </script>
</x-filament-panels::page>
