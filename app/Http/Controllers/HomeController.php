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
        $data = json_decode(file_get_contents($path), true);
        return collect($data);
    }

    public function index(Request $request)
    {
        $katalog   = $this->getKatalog();
        $tipeSewa  = $request->get('tipe_sewa', 'semua');

        // ── Filter tipe sewa ─────────────────────────────────
        if ($tipeSewa !== 'semua') {
            $katalog = $katalog->filter(
                fn($car) => in_array($tipeSewa, $car['tipe_sewa'])
            );
        }

        // ── Filter pencarian ─────────────────────────────────
        if ($request->filled('search')) {
            $search  = strtolower(trim($request->search));
            $katalog = $katalog->filter(
                fn($car) => str_contains(strtolower($car['nama']), $search)
                    || str_contains(strtolower($car['brand']), $search)
            );
        }

        // ── Filter transmisi ─────────────────────────────────
        if ($request->filled('transmisi') && $request->transmisi !== 'semua') {
            $katalog = $katalog->filter(
                fn($car) => strtoupper($car['transmisi']) === strtoupper($request->transmisi)
            );
        }

        // ── Tambahkan kolom harga aktif berdasarkan tipe sewa ─
        // Ini yang menentukan harga yang ditampilkan di card
        $katalog = $katalog->map(function ($car) use ($tipeSewa) {
            $car['harga_aktif'] = match ($tipeSewa) {
                'dengan_sopir' => $car['harga_dengan_sopir'] ?? $car['harga_lepas_kunci'],
                default        => $car['harga_lepas_kunci']  ?? $car['harga_dengan_sopir'],
            };
            return $car;
        });

        // ── Sorting berdasarkan harga aktif ──────────────────
        $katalog = match ($request->get('sort', 'termurah')) {
            'termahal' => $katalog->sortByDesc('harga_aktif'),
            'terbaru'  => $katalog->sortByDesc('id'),
            default    => $katalog->sortBy('harga_aktif'),
        };

        // ── Pagination manual ────────────────────────────────
        $perPage     = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items       = $katalog->values()->forPage($currentPage, $perPage);

        $cars = new LengthAwarePaginator(
            $items,
            $katalog->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ── Hitung total hari ─────────────────────────────────
        $totalHari = 1;
        if ($request->filled('tanggal_keluar') && $request->filled('tanggal_kembali')) {
            $keluar  = \Carbon\Carbon::parse($request->tanggal_keluar);
            $kembali = \Carbon\Carbon::parse($request->tanggal_kembali);
            if ($kembali->greaterThan($keluar)) {
                $totalHari = max(1, $keluar->diffInDays($kembali));
            }
        }

        return view('home', compact('cars', 'totalHari', 'tipeSewa'));
    }
    public function cekNikAjax(Request $request)
    {
        $request->validate([
            'nik'             => ['required', 'digits:16'],
            'car_id'          => ['required', 'integer'],
            'tanggal_keluar'  => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date'],
            'tipe_sewa'       => ['nullable', 'string'],
        ]);

        $customer = Customer::where('ktp', $request->nik)->first();

        if (!$customer) {
            return response()->json([
                'success' => true,
                'registered' => false,

                'redirect' => route('data.penyewa', [
                    'ktp' => $request->nik,
                    'car_id' => $request->car_id,
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'tipe_sewa' => $request->tipe_sewa,
                ]),
            ]);
        }

        if ($customer->status === 'blacklist') {
            return response()->json([
                'success' => false,
                'message' => 'NIK terdaftar sebagai blacklist.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'registered' => true,

            'redirect' => route('booking', [
                'customer_id' => $customer->id,
                'car_id' => $request->car_id,
                'tanggal_keluar' => $request->tanggal_keluar,
                'tanggal_kembali' => $request->tanggal_kembali,
                'tipe_sewa' => $request->tipe_sewa,
            ]),
        ]);
    }
    public function create(Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        $car = Car::with([
            'carModel.brand'
        ])->findOrFail($request->car_id);

        $carModels = CarModel::with('brand')->get();

        return view('booking', compact(
            'customer',
            'car',
            'carModels'
        ))->with([
            'tanggalKeluar' => $request->tanggal_keluar,
            'tanggalKembali' => $request->tanggal_kembali,
            'tipeSewa' => $request->tipe_sewa,
        ]);
    }
}
