<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with(['carModel']) // <-- Eager load relasi
            ->where('status', 'ready')
            ->where('garasi', 'SPT')
            ->get();

        // ── Filter pencarian nama ────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('carModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        // ── Filter jenis kendaraan ───────────────────────────
        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $query->where('jenis', $request->jenis); // kolom: mobil / motor
        }

        // ── Filter transmisi ─────────────────────────────────
        if ($request->filled('transmisi') && $request->transmisi !== 'semua') {
            $query->where('transmisi', $request->transmisi); // AT / MT
        }

        // ── Filter ketersediaan berdasarkan tanggal ──────────
        if ($request->filled('tgl_keluar') && $request->filled('tgl_kembali')) {
            $tglKeluar = $request->tgl_keluar;
            $tglKembali = $request->tgl_kembali;

            $query->whereDoesntHave('bookings', function ($q) use ($tglKeluar, $tglKembali) {
                $q->whereNotIn('status', ['booking', 'selesai', 'batal', 'disewa'])
                    ->where(function ($q2) use ($tglKeluar, $tglKembali) {
                        // Overlap: booking yang bentrok dengan rentang yang diminta
                        $q2->where('tanggal_keluar', '<', $tglKembali)
                            ->where('tanggal_kembali', '>', $tglKeluar);
                    });
            });
        }

        // ── Sorting ──────────────────────────────────────────
        match ($request->get('sort', 'termurah')) {
            'termahal' => $query->orderBy('harga_per_hari', 'desc'),
            'terbaru' => $query->latest(),
            default => $query->orderBy('harga_per_hari', 'asc'),
        };

        $cars = $query->paginate(12)->withQueryString();
        $totalCars = $query->toBase()->getCountForPagination();

        // Hitung jumlah hari untuk tampilan harga total
        $totalHari = 1;
        if ($request->filled('tgl_keluar') && $request->filled('tgl_kembali')) {
            $totalHari = max(1, \Carbon\Carbon::parse($request->tgl_keluar)
                ->diffInDays(\Carbon\Carbon::parse($request->tgl_kembali)));
        }

        return view('home', compact('cars', 'totalHari'));
    }
}
