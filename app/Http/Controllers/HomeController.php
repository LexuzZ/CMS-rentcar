<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Query dasar
        |--------------------------------------------------------------------------
        | Ambil hanya 1 mobil untuk setiap car_model_id.
        | Karena setiap CarModel memiliki 1 brand, ini otomatis menghasilkan
        | 1 data untuk setiap kombinasi Brand + Car Model.
        |
        | MIN(id) digunakan sebagai mobil yang akan ditampilkan.
        |--------------------------------------------------------------------------
        */

        $query = Car::query()
            ->with(['carModel.brand'])
            ->whereIn('id', function ($subQuery) {
                $subQuery->selectRaw('MIN(id)')
                    ->from('cars')
                    ->groupBy('car_model_id');
            });

        /*
        |--------------------------------------------------------------------------
        | Filter pencarian Brand / Car Model
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('carModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($search) {
                        $brandQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter transmisi
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('transmisi') &&
            $request->transmisi !== 'semua'
        ) {
            $query->where('transmisi', $request->transmisi);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter berdasarkan ketersediaan tanggal
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('tanggal_keluar') &&
            $request->filled('tanggal_kembali')
        ) {
            $tglKeluar = Carbon::parse($request->tanggal_keluar);
            $tglKembali = Carbon::parse($request->tanggal_kembali);

            // Pastikan tanggal kembali tidak lebih kecil dari tanggal keluar
            if ($tglKembali->greaterThan($tglKeluar)) {
                $query->whereDoesntHave('bookings', function ($bookingQuery) use (
                    $tglKeluar,
                    $tglKembali
                ) {
                    $bookingQuery
                        ->whereIn('status', ['booking', 'disewa'])
                        ->where(function ($overlapQuery) use (
                            $tglKeluar,
                            $tglKembali
                        ) {
                            /*
                            |--------------------------------------------------------------------------
                            | Cek apakah periode booking bentrok
                            |
                            | Booking bentrok jika:
                            | tanggal_keluar < tanggal yang dipilih
                            | DAN
                            | tanggal_kembali > tanggal yang dipilih
                            |--------------------------------------------------------------------------
                            */

                            $overlapQuery
                                ->where('tanggal_keluar', '<', $tglKembali)
                                ->where('tanggal_kembali', '>', $tglKeluar);
                        });
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($request->get('sort', 'termurah')) {
            case 'termahal':
                $query->orderBy('harga_harian', 'desc');
                break;

            case 'terbaru':
                $query->latest();
                break;

            case 'termurah':
            default:
                $query->orderBy('harga_harian', 'asc');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $cars = $query
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Hitung total hari sewa
        |--------------------------------------------------------------------------
        */

        $totalHari = 1;

        if (
            $request->filled('tanggal_keluar') &&
            $request->filled('tanggal_kembali')
        ) {
            $tglKeluar = Carbon::parse($request->tanggal_keluar);
            $tglKembali = Carbon::parse($request->tanggal_kembali);

            if ($tglKembali->greaterThan($tglKeluar)) {
                $totalHari = max(
                    1,
                    $tglKeluar->diffInDays($tglKembali)
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('home', compact(
            'cars',
            'totalHari'
        ));
    }
}
