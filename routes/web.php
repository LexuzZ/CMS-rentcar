<?php

use App\Http\Controllers\CustomerCheckController;
use App\Http\Controllers\CustomerFileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PdfController;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;

Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');

// Route::get('/{record}/pdf', [ExportController::class, 'download'])->name('invoices.pdf.download');
Route::get('invoices/{record}/pdf', [ExportController::class, 'download'])
    ->name('invoices.pdf.download');
Route::get('/order', [CustomerCheckController::class, 'cekNIK'])->name('cek.nik');
Route::post('/order', [CustomerCheckController::class, 'cekNIKPost'])->name('cek.nik.post');
Route::get('/penyewa', [CustomerCheckController::class, 'dataPenyewa'])->name('data.penyewa');
Route::post('/penyewa', [CustomerCheckController::class, 'dataPenyewaPost'])->name('data.penyewa.post');

Route::get('/booking', [CustomerCheckController::class, 'bookingForm'])->name('booking.form');
Route::get('/customers/{customer}/download-ktp', [CustomerFileController::class, 'downloadKtp'])->name('customers.download.ktp');
Route::get('/customers/{customer}/download-sim', [CustomerFileController::class, 'downloadSim'])->name('customers.download.sim');

Route::group(['middleware' => ['web', Authenticate::class]], function () {

    Route::get('/reports/export-car-bookings/{car}/{year}/{month}', [ExportController::class, 'exportCarBookings'])
        ->name('reports.export.car.bookings');
    // URL diubah menjadi /admin/bookings-calendar agar lebih konsisten
    Route::get('/invoices/{invoice}/pdf/download', [PdfController::class, 'downloadInvoice'])
        ->name('invoices.pdf.download');
    Route::get('/reports/monthly-recap/{year}/{month}/pdf', [PdfController::class, 'downloadMonthlyRecapPdf'])
        ->name('reports.monthly-recap.pdf');
});
