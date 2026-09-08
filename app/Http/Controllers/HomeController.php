<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with(['carModel.brand'])
            ->where('status', 'ready')
            ->where('garasi', 'SPT');

        // ── Filter pencarian nama ────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('carModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        // ── Filter transmisi ─────────────────────────────────
        // Kolom 'jenis' dihapus karena tidak ada di tabel cars
        if ($request->filled('transmisi') && $request->transmisi !== 'semua') {
            $query->where('transmisi', $request->transmisi);
        }

        // ── Filter ketersediaan berdasarkan tanggal ──────────
        if ($request->filled('tgl_keluar') && $request->filled('tgl_kembali')) {
            $tglKeluar  = $request->tgl_keluar;
            $tglKembali = $request->tgl_kembali;

            $query->whereDoesntHave('bookings', function ($q) use ($tglKeluar, $tglKembali) {
                // Hanya booking aktif (booking & disewa) yang dianggap menghalangi
                $q->whereIn('status', ['booking', 'disewa'])
                  ->where(function ($q2) use ($tglKeluar, $tglKembali) {
                      $q2->where('tanggal_keluar', '<', $tglKembali)
                         ->where('tanggal_kembali', '>', $tglKeluar);
                  });
            });
        }

        // ── Sorting ──────────────────────────────────────────
        match ($request->get('sort', 'termurah')) {
            'termahal' => $query->orderBy('harga_per_hari', 'desc'),
            'terbaru'  => $query->latest(),
            default    => $query->orderBy('harga_per_hari', 'asc'),
        };

        $cars = $query->paginate(12)->withQueryString();

        // ── Hitung total hari ─────────────────────────────────
        $totalHari = 1;
        if ($request->filled('tgl_keluar') && $request->filled('tgl_kembali')) {
            $totalHari = max(1, Carbon::parse($request->tgl_keluar)
                ->diffInDays(Carbon::parse($request->tgl_kembali)));
        }

        return view('home', compact('cars', 'totalHari'));
    }
}
