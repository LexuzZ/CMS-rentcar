<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Load katalog dari JSON (disimpan di storage/app/katalog.json)
     * atau bisa juga taruh di public/katalog.json
     */
    private function getKatalog(): Collection
    {
        $path = base_path('katalog.json'); // taruh katalog.json di root project
        $data = json_decode(file_get_contents($path), true);
        return collect($data);
    }

    public function index(Request $request)
    {
        $katalog = $this->getKatalog();

        /*
        |------------------------------------------------------------------
        | Filter pencarian nama / brand
        |------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $katalog = $katalog->filter(function ($car) use ($search) {
                return str_contains(strtolower($car['nama']), $search)
                    || str_contains(strtolower($car['brand']), $search);
            });
        }

        /*
        |------------------------------------------------------------------
        | Filter transmisi
        |------------------------------------------------------------------
        */
        if ($request->filled('transmisi') && $request->transmisi !== 'semua') {
            $katalog = $katalog->filter(
                fn($car) => strtoupper($car['transmisi']) === strtoupper($request->transmisi)
            );
        }

        /*
        |------------------------------------------------------------------
        | Sorting
        |------------------------------------------------------------------
        */
        $katalog = match ($request->get('sort', 'termurah')) {
            'termahal' => $katalog->sortByDesc('harga_harian'),
            'terbaru'  => $katalog->sortByDesc('id'),
            default    => $katalog->sortBy('harga_harian'),
        };

        /*
        |------------------------------------------------------------------
        | Pagination manual (karena data dari Collection bukan DB)
        |------------------------------------------------------------------
        */
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

        /*
        |------------------------------------------------------------------
        | Hitung total hari sewa
        |------------------------------------------------------------------
        */
        $totalHari = 1;
        if ($request->filled('tanggal_keluar') && $request->filled('tanggal_kembali')) {
            $keluar  = \Carbon\Carbon::parse($request->tanggal_keluar);
            $kembali = \Carbon\Carbon::parse($request->tanggal_kembali);
            if ($kembali->greaterThan($keluar)) {
                $totalHari = max(1, $keluar->diffInDays($kembali));
            }
        }

        return view('home', compact('cars', 'totalHari'));
    }
}
