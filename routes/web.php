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

Route::get('/api/bookings-calendar', function (Request $request) {
    $mobil = $request->query('mobil');
    $nopol = $request->query('nopol');

    $query = Booking::with(['car', 'customer']);

    if ($mobil) {
        $query->whereHas('car', function ($q) use ($mobil) {
            $q->where('nama_mobil', $mobil);
        });
    }

    if ($nopol) {
        $query->whereHas('car', function ($q) use ($nopol) {
            $q->where('nopol', $nopol);
        });
    }

    return $query->get()->map(function ($booking) {
        // Gabungkan tanggal dan waktu
        // $start = Carbon::parse($booking->tanggal_keluar . ' ' . ($booking->waktu_keluar ?? '00:00:00'))->toDateTimeLocalString();

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_keluar . ' ' . ($booking->waktu_keluar ?? '00:00:00'))->toDateTimeLocalString();
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_kembali . ' ' . ($booking->waktu_kembali ?? '23:59:59'))->toDateTimeLocalString();
        return [
            'title' => $booking->car->nopol  . ' - ' . $booking->car->nama_mobil . ' (' . $booking->customer->nama . ')',
            'start' => $start,
            'end' => $end,
            'color' => '#333446',
        ];
    });
});
