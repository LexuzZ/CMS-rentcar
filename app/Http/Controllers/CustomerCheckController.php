<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerCheckController extends Controller
{
    public function cekNIK()
    {
        return view('cek-nik');
    }

    public function cekNIKPost(Request $request)
    {
        $request->validate(['ktp' => 'required|digits:16']);

        $nik = $request->ktp;

        // ══════════════════════════════════════════
        // CEK BLACKLIST — sebelum apapun
        // ══════════════════════════════════════════
        $blacklist = Blacklist::findByNik($nik);

        if ($blacklist) {
            return back()
                ->withInput()
                ->with('error_blacklist', [
                    'nik' => $nik,
                    'alasan' => $blacklist->alasan,
                ]);
        }
        // ══════════════════════════════════════════

        $customer = Customer::where('ktp', $nik)->first();

        if ($customer) {
            session(['customer_id' => $customer->id]);

            return redirect()->route('booking.form')
                ->with('info', 'Data ditemukan ✅');
        }

        return redirect()->route('data.penyewa')
            ->with('info', 'NIK tidak terdaftar, silakan isi data penyewa');
    }

    // Ganti method bookingForm di CustomerCheckController
    // $carModels tidak perlu lagi karena dropdown mobil sudah dihapus

    public function bookingForm(Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        // Ambil data kendaraan dari katalog.json berdasarkan car_id
        $katalog = collect(json_decode(file_get_contents(base_path('katalog.json')), true));
        $car = $katalog->firstWhere('id', (int) $request->car_id);

        abort_if(! $car, 404, 'Kendaraan tidak ditemukan.');

        return view('booking', [
            'customer' => $customer,
            'car' => $car,           // array dari katalog.json
            'tanggalKeluar' => $request->tanggal_keluar,
            'tanggalKembali' => $request->tanggal_kembali,
            'tipeSewa' => $request->tipe_sewa,
        ]);
    }

    public function dataPenyewa()
    {
        return view('form-data-penyewa');
    }

    public function dataPenyewaPost(Request $request)
    {
        $request->validate([
            'ktp' => 'required|unique:customers,ktp|max:16',
            'nama' => 'required',
            'no_telp' => 'required|unique:customers,no_telp',
            'alamat' => 'required',
            'lisence' => 'nullable|max:16',
            'identity_file' => 'nullable|image|max:10240',
            'lisence_file' => 'nullable|image|max:10240',
        ]);

        // handle upload
        $identityPath = null;
        if ($request->hasFile('identity_file')) {
            $identityPath = $request->file('identity_file')->store('identity_docs', 'public');
        }

        $lisencePath = null;
        if ($request->hasFile('lisence_file')) {
            $lisencePath = $request->file('lisence_file')->store('lisence_docs', 'public');
        }

        $customer = Customer::create([
            'ktp' => $request->ktp,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'lisence' => $request->lisence,
            'identity_file' => $identityPath,
            'lisence_file' => $lisencePath,
        ]);

        // simpan session id customer
        session(['customer_id' => $customer->id]);

        return redirect()->route('booking.form')
            ->with('success', 'Data penyewa tersimpan ✅');
    }
}
