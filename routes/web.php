<?php

use App\Http\Controllers\CustomerCheckController;
use App\Http\Controllers\CustomerFileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PdfController;
use App\Models\Customer;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/'.$path);

    if (! file_exists($fullPath)) {
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
Route::get('/home', [HomeController::class, 'index'])->name('home');
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
Route::post('/cek-nik-ajax', function (Request $request) {

    $request->validate([
        'nik' => ['required', 'digits:16'],
        'car_id' => ['required', 'integer'],
    ]);

    $customer = Customer::where('nik', $request->nik)->first();

    if (!$customer) {

        return response()->json([
            'success' => true,
            'message' => 'NIK belum terdaftar dan dapat melanjutkan booking.',
        ]);
    }

    if ($customer->status === 'blacklist') {

        return response()->json([
            'success' => false,
            'message' => 'NIK ini terdaftar dalam daftar hitam dan tidak dapat melakukan booking.',
        ], 422);
    }

    return response()->json([
        'success' => true,
        'message' => 'NIK berhasil diverifikasi. Silakan lanjutkan booking.',
    ]);

})->name('cek.nik.ajax');
