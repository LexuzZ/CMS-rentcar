<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandAndModelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Toyota' => ['Avanza', 'Innova Reborn', 'Rush', 'Fortuner', 'Calya'],
            'Mitsubishi' => ['Xpander', 'Pajero Sport', 'Triton'],
            'Suzuki' => ['Ertiga', 'XL7', 'Carry'],
            'Daihatsu' => ['Xenia', 'Terios', 'Sigra', 'Gran Max'],
            'Honda' => ['Brio', 'HR-V', 'CR-V', 'Mobilio'],
        ];

        foreach ($data as $brandName => $models) {
            // Buat merek baru
            $brand = Brand::create(['name' => $brandName]);

            // Buat model-model yang berelasi dengan merek tersebut
            foreach ($models as $modelName) {
                $brand->carModels()->create(['name' => $modelName]);
            }
        }
    }
}
