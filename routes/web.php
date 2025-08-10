<?php

use App\Http\Controllers\ExportController;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/{record}/pdf', [ExportController::class, 'download'])->name('invoices.pdf.download');
// Route::get('/api/cars', function () {
//     return \App\Models\Car::all()->map(function ($car) {
//         return [
//             'id' => $car->id,
//             'title' => $car->nama_mobil . ' (' . $car->nopol . ')',
//         ];
//     });
// });
// Route::get('/api/bookings-calendar', function () {
//     return \App\Models\Booking::with('car', 'customer')->get()->map(function ($booking) {
//         return [
//             'title' => $booking->customer->nama,
//             'start' => $booking->tanggal_keluar,
//             'end' => \Carbon\Carbon::parse($booking->tanggal_kembali),
//             'resourceId' => $booking->car_id,
//             'color' => '#3b82f6',
//         ];
//     });
// });

Route::get('/bookings-calendar', function (Request $request) {
    $mobilModel = $request->query('mobil'); // Ganti nama variabel agar lebih jelas
    $nopol = $request->query('nopol');

    // 1. Eager load relasi bertingkat untuk efisiensi
    $query = Booking::with(['car.carModel.brand', 'customer']);

    // 2. Sesuaikan filter untuk mencari di relasi carModel
    if ($mobilModel) {
        $query->whereHas('car.carModel', function ($q) use ($mobilModel) {
            $q->where('name', 'like', "%{$mobilModel}%");
        });
    }

    if ($nopol) {
        $query->whereHas('car', function ($q) use ($nopol) {
            $q->where('nopol', 'like', "%{$nopol}%");
        });
    }

    return $query->get()->map(function ($booking) {
        // Pengecekan untuk menghindari error jika ada data relasi yang hilang
        if (!$booking->car || !$booking->car->carModel || !$booking->car->carModel->brand || !$booking->customer) {
            return null; // Lewati booking ini jika datanya tidak lengkap
        }

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_keluar . ' ' . ($booking->waktu_keluar ?? '00:00:00'))->toDateTimeLocalString();
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_kembali . ' ' . ($booking->waktu_kembali ?? '23:59:59'))->toDateTimeLocalString();

        $statusColor = match ($booking->status) {
            'booking' => '#3b82f6',   // biru
            'aktif' => '#10b981',     // hijau
            'selesai' => '#6b7280',   // abu-abu
            'batal' => '#ef4444',     // merah
            default => '#9ca3af',
        };

        // 3. Sesuaikan pembuatan judul dengan relasi baru
        $title = sprintf(
            '%s %s (%s) - %s',
            $booking->car->carModel->brand->name, // Merek
            $booking->car->carModel->name,       // Model
            $booking->car->nopol,                // Nopol
            $booking->customer->nama             // Nama Pelanggan
        );

        return [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'color' => $statusColor,
            'id' => $booking->id, // Tambahkan ID untuk referensi
        ];
    })->filter(); // ->filter() tanpa argumen akan menghapus semua nilai null
});
