<x-filament::section>
    <h2 class="text-xl font-bold mb-4">📊 Dashboard Bulanan {{ now()->isoFormat('MMMM YYYY') }}</h2>

    {{-- Ringkasan Angka --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-blue-100 rounded-lg text-center shadow">
            <p class="text-lg font-bold">{{ $this->getData()['totalSewa'] }}</p>
            <p class="text-gray-600">Total Sewa</p>
        </div>
        <div class="p-4 bg-green-100 rounded-lg text-center shadow">
            <p class="text-lg font-bold">Rp {{ number_format($this->getData()['pendapatan'], 0, ',', '.') }}</p>
            <p class="text-gray-600">Pendapatan</p>
        </div>
        <div class="p-4 bg-yellow-100 rounded-lg text-center shadow">
            <p class="text-lg font-bold">Rp {{ number_format($this->getData()['denda'], 0, ',', '.') }}</p>
            <p class="text-gray-600">Denda</p>
        </div>
        <div class="p-4 bg-purple-100 rounded-lg text-center shadow">
            <p class="text-lg font-bold">Rp {{ number_format($this->getData()['keuntungan'], 0, ',', '.') }}</p>
            <p class="text-gray-600">Keuntungan</p>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="grid grid-cols-2 gap-6">
        {{-- Top 5 mobil terlaris (Pie Chart) --}}
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h3 class="font-bold mb-2">Top 5 Mobil Paling Sering Disewa</h3>
            <canvas id="topCarsChart"></canvas>
        </div>

        {{-- Top 5 mobil jarang disewa (Bar Chart) --}}
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h3 class="font-bold mb-2">Top 5 Mobil Jarang Disewa</h3>
            <canvas id="lowCarsChart"></canvas>
        </div>
    </div>
</x-filament::section>

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
            labels: topCars.map(c => c.car.car_model.name + ' - ' + c.car.nopol),
            datasets: [{
                data: topCars.map(c => c.total),
                backgroundColor: ['#60a5fa','#34d399','#f87171','#fbbf24','#a78bfa'],
            }]
        }
    });

    // Low Cars Bar Chart
    new Chart(document.getElementById('lowCarsChart'), {
        type: 'bar',
        data: {
            labels: lowCars.map(c => c.car.car_model.name + ' - ' + c.car.nopol),
            datasets: [{
                data: lowCars.map(c => c.total),
                backgroundColor: '#f87171',
            }]
        },
        options: {
            indexAxis: 'y'
        }
    });
});
</script>
