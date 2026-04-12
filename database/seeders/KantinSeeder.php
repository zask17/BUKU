<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Menu;

class KantinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert Vendors
        $vendors = [
            ['nama_vendor' => 'Kantin Sehat'],
            ['nama_vendor' => 'Warung Berkah'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }

        // Insert Menus
        $menus = [
            // Kantin Sehat (idvendor = 1)
            [
                'nama_menu' => 'Nasi Bakar',
                'harga' => 15000,
                'path_gambar' => null,
                'idvendor' => 1,
            ],
            [
                'nama_menu' => 'Ayam Geprek',
                'harga' => 12000,
                'path_gambar' => null,
                'idvendor' => 1,
            ],
            [
                'nama_menu' => 'Mie Goreng Spesial',
                'harga' => 12000,
                'path_gambar' => null,
                'idvendor' => 1,
            ],
            // Warung Berkah (idvendor = 2)
            [
                'nama_menu' => 'Soto Ayam',
                'harga' => 10000,
                'path_gambar' => null,
                'idvendor' => 2,
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'harga' => 3000,
                'path_gambar' => null,
                'idvendor' => 2,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
