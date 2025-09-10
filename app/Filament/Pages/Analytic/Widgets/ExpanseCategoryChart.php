<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Pengeluaran;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Tables\Concerns\HasFilters;
use Filament\Widgets\ChartWidget;
// use Filament\Widgets\Concerns\InteractsWithFilters;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class ExpanseCategoryChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Komposisi Pengeluaran per Kategori';
    protected static ?int $sort = 3; // Atur urutan widget di dashboard
    public ?string $filter = 'this_month';

    /**
     * Menggunakan metode filter dasar.
     */
    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'this_year' => 'Tahun Ini',
        ];

    }

     protected function getData(): array
    {
        $period = $this->filter;

        // Tentukan rentang tanggal berdasarkan filter yang dipilih
        [$startDate, $endDate] = match ($period) {
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()], // 'this_month'
        };

        // Ambil data pengeluaran berdasarkan kategori dalam rentang tanggal
        $expenseData = Pengeluaran::query()
            ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->groupBy('nama_pengeluaran') // Ganti 'kategori' jika nama kolomnya berbeda
            ->select('nama_pengeluaran', DB::raw('SUM(pembayaran) as total'))
            ->pluck('total', 'nama_pengeluaran');

        // Siapkan data untuk chart
        $labels = $expenseData->keys()->toArray();
        $data = $expenseData->values()->toArray();

        // Gunakan palet warna yang sudah ditentukan
        $colorPalette = [
            '#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981',
            '#3B82F6', '#EF4444', '#84CC16', '#06B6D4', '#D946EF',
        ];

        // Petakan warna ke setiap label
        $colors = collect($labels)->map(function ($label, $index) use ($colorPalette) {
            return $colorPalette[$index % count($colorPalette)];
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Tipe chart adalah pie
    }
}
