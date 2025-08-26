<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\PdfController;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
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

Route::group(['middleware' => ['web', Authenticate::class]], function () {
    Route::get('/reports/export-car-bookings/{car}/{year}/{month}', [ExportController::class, 'exportCarBookings'])
        ->name('reports.export.car.bookings');
    // URL diubah menjadi /admin/bookings-calendar agar lebih konsisten
    Route::get('/invoices/{invoice}/pdf/download', [PdfController::class, 'downloadInvoice'])
        ->name('invoices.pdf.download');



});
