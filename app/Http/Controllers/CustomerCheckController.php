<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarModel;
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
        $customer = Customer::where('ktp', $request->ktp)->first();

        if ($customer) {
            session(['customer_id' => $customer->id]);
            return redirect()->route('booking.form')
                ->with('info', 'Data ditemukan ✅');
        }

        return redirect()->route('data.penyewa')->with('info', 'NIK tidak terdaftar, silakan isi data penyewa');
    }


    public function bookingForm()
    {
        $customer = Customer::find(session('customer_id'));
        $carModels = CarModel::with('brand')
        ->orderBy('name')
        ->get();

    return view('booking', compact('customer', 'carModels'));
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
            'lisence' => 'nullable|unique:customers,lisence',
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
