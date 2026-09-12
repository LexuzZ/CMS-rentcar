<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    private function getKatalog(): Collection
    {
        $path = base_path('katalog.json');

        $data = json_decode(
            file_get_contents($path),
            true
        );

        return collect($data);
    }

    public function index(Request $request)
    {
        $katalog = $this->getKatalog();
        $tipeSewa = $request->get('tipe_sewa', 'semua');

        // =====================================================
        // FILTER TIPE SEWA
        // =====================================================

        if ($tipeSewa !== 'semua') {
            $katalog = $katalog->filter(
                fn ($car) => in_array($tipeSewa, $car['tipe_sewa'] ?? [])
            );
        }

        // =====================================================
        // FILTER PENCARIAN
        // =====================================================

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));

            $katalog = $katalog->filter(
                fn ($car) => str_contains(
                    strtolower($car['nama'] ?? ''),
                    $search
                )
                    ||
                    str_contains(
                        strtolower($car['brand'] ?? ''),
                        $search
                    )
            );
        }

        // =====================================================
        // FILTER TRANSMISI
        // =====================================================

        if (
            $request->filled('transmisi') &&
            $request->transmisi !== 'semua'
        ) {
            $katalog = $katalog->filter(
                fn ($car) => strtoupper($car['transmisi'] ?? '') ===
                    strtoupper($request->transmisi)
            );
        }

        // =====================================================
        // HARGA AKTIF
        // =====================================================

        $katalog = $katalog->map(function ($car) use ($tipeSewa) {

            $car['harga_aktif'] = match ($tipeSewa) {

                'dengan_sopir' => $car['harga_dengan_sopir']
                    ?? $car['harga_lepas_kunci']
                    ?? 0,

                default => $car['harga_lepas_kunci']
                    ?? $car['harga_dengan_sopir']
                    ?? 0,
            };

            return $car;
        });

        // =====================================================
        // SORTING
        // =====================================================

        $katalog = match ($request->get('sort', 'termurah')) {

            'termahal' => $katalog->sortByDesc('harga_aktif'),

            'terbaru' => $katalog->sortByDesc('id'),

            default => $katalog->sortBy('harga_aktif'),
        };

        // =====================================================
        // PAGINATION
        // =====================================================

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $items = $katalog
            ->values()
            ->forPage($currentPage, $perPage);

        $cars = new LengthAwarePaginator(
            $items,
            $katalog->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // =====================================================
        // TOTAL HARI
        // =====================================================

        $totalHari = 1;

        if (
            $request->filled('tanggal_keluar') &&
            $request->filled('tanggal_kembali')
        ) {
            $keluar = \Carbon\Carbon::parse(
                $request->tanggal_keluar
            );

            $kembali = \Carbon\Carbon::parse(
                $request->tanggal_kembali
            );

            if ($kembali->greaterThan($keluar)) {
                $totalHari = max(
                    1,
                    $keluar->diffInDays($kembali)
                );
            }
        }

        return view(
            'home',
            compact(
                'cars',
                'totalHari',
                'tipeSewa'
            )
        );
    }

    // =========================================================
    // CEK NIK DARI POPUP HOME
    // =========================================================

    public function cekNikAjax(Request $request)
    {
        $validated = $request->validate([
            'nik' => [
                'required',
                'digits:16',
            ],

            'car_id' => [
                'required',
                'integer',
            ],

            'tanggal_keluar' => [
                'required',
                'date',
            ],

            'tanggal_kembali' => [
                'required',
                'date',
            ],

            'tipe_sewa' => [
                'nullable',
                'string',
            ],
        ]);

        $customer = Customer::where(
            'ktp',
            $validated['nik']
        )->first();

        if ($customer && $customer->status === 'blacklist') {
            return response()->json([
                'success' => false,
                'message' => 'NIK terdaftar dalam daftar hitam dan tidak dapat melakukan booking.',
            ], 422);
        }

        if (! $customer) {
            $redirectUrl = route('data.penyewa', [
                'ktp' => $validated['nik'],
                'car_id' => $validated['car_id'],
                'tanggal_keluar' => $validated['tanggal_keluar'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'tipe_sewa' => $validated['tipe_sewa'],
            ]);

            return response()->json([
                'success' => true,
                'registered' => false,
                'message' => 'NIK belum terdaftar. Silakan lengkapi data penyewa.',
                'redirect' => $redirectUrl,
            ]);
        }

        $redirectUrl = route('booking.form', [
            'customer_id' => $customer->id,
            'car_id' => $validated['car_id'],
            'tanggal_keluar' => $validated['tanggal_keluar'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'tipe_sewa' => $validated['tipe_sewa'],
        ]);

        return response()->json([
            'success' => true,
            'registered' => true,
            'customer_id' => $customer->id,
            'message' => 'NIK berhasil diverifikasi. Mengarahkan ke form booking.',
            'redirect' => $redirectUrl,
        ]);
    }

    // =========================================================
    // FORM DATA PENYEWA
    // =========================================================

    public function formPenyewa(Request $request)
    {
        return view('form-data-penyewa');
    }

    // =========================================================
    // HALAMAN BOOKING
    // =========================================================

    public function create(Request $request)
    {
        $customer = Customer::findOrFail(
            $request->customer_id
        );

        $car = Car::with([
            'carModel.brand',
        ])->findOrFail(
            $request->car_id
        );

        $carModels = CarModel::with('brand')->get();

        return view('booking', [
            'customer' => $customer,
            'car' => $car,
            'carModels' => $carModels,

            'tanggalKeluar' => $request->tanggal_keluar,

            'tanggalKembali' => $request->tanggal_kembali,

            'tipeSewa' => $request->tipe_sewa,
        ]);
    }
}
