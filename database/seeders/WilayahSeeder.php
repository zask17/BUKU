<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Provinsi
        $provinsis = [
            ['id' => '11', 'name' => 'Aceh'],
            ['id' => '12', 'name' => 'Sumatera Utara'],
            ['id' => '13', 'name' => 'Sumatera Barat'],
            ['id' => '14', 'name' => 'Riau'],
            ['id' => '15', 'name' => 'Jambi'],
            ['id' => '16', 'name' => 'Sumatera Selatan'],
            ['id' => '17', 'name' => 'Lampung'],
            ['id' => '18', 'name' => 'Kepulauan Bangka Belitung'],
            ['id' => '19', 'name' => 'Kepulauan Riau'],
            ['id' => '21', 'name' => 'Jawa Barat'],
            ['id' => '31', 'name' => 'DKI Jakarta'],
            ['id' => '32', 'name' => 'Jawa Tengah'],
            ['id' => '33', 'name' => 'DI Yogyakarta'],
            ['id' => '34', 'name' => 'Jawa Timur'],
            ['id' => '35', 'name' => 'Banten'],
            ['id' => '36', 'name' => 'Bali'],
            ['id' => '51', 'name' => 'Barat Nusa Tenggara'],
            ['id' => '52', 'name' => 'Nusa Tenggara Timur'],
            ['id' => '61', 'name' => 'Kalimantan Barat'],
            ['id' => '62', 'name' => 'Kalimantan Tengah'],
            ['id' => '63', 'name' => 'Kalimantan Selatan'],
            ['id' => '64', 'name' => 'Kalimantan Timur'],
            ['id' => '65', 'name' => 'Kalimantan Utara'],
            ['id' => '71', 'name' => 'Sulawesi Utara'],
            ['id' => '72', 'name' => 'Sulawesi Tengah'],
            ['id' => '73', 'name' => 'Sulawesi Selatan'],
            ['id' => '74', 'name' => 'Sulawesi Tenggara'],
            ['id' => '75', 'name' => 'Gorontalo'],
            ['id' => '76', 'name' => 'Sulawesi Barat'],
            ['id' => '81', 'name' => 'Maluku'],
            ['id' => '82', 'name' => 'Maluku Utara'],
            ['id' => '91', 'name' => 'Papua Barat'],
            ['id' => '92', 'name' => 'Papua'],
            ['id' => '94', 'name' => 'Papua Selatan'],
            ['id' => '95', 'name' => 'Papua Tengah'],
            ['id' => '96', 'name' => 'Papua Pegunungan'],
        ];

        DB::table('reg_provinces')->insert($provinsis);

        // Data Kota/Kabupaten contoh (untuk Jawa Barat = 32)
        $regencies = [
            ['id' => '3201', 'province_id' => '32', 'name' => 'Kota Semarang'],
            ['id' => '3202', 'province_id' => '32', 'name' => 'Kabupaten Cilacap'],
            ['id' => '3203', 'province_id' => '32', 'name' => 'Kabupaten Banyumas'],
            ['id' => '3204', 'province_id' => '32', 'name' => 'Kabupaten Purbalingga'],
            ['id' => '3205', 'province_id' => '32', 'name' => 'Kabupaten Banjarnegara'],
        ];

        DB::table('reg_regencies')->insert($regencies);

        // Data Kecamatan contoh (untuk Kota Semarang = 3201)
        $districts = [
            ['id' => '320101', 'regency_id' => '3201', 'name' => 'Kecamatan Semarang Selatan'],
            ['id' => '320102', 'regency_id' => '3201', 'name' => 'Kecamatan Semarang Timur'],
            ['id' => '320103', 'regency_id' => '3201', 'name' => 'Kecamatan Semarang Utara'],
            ['id' => '320104', 'regency_id' => '3201', 'name' => 'Kecamatan Semarang Barat'],
            ['id' => '320105', 'regency_id' => '3201', 'name' => 'Kecamatan Semarang Tengah'],
        ];

        DB::table('reg_districts')->insert($districts);

        // Data Kelurahan contoh (untuk Kecamatan Semarang Selatan = 320101)
        $villages = [
            ['id' => '3201011001', 'district_id' => '320101', 'name' => 'Kelurahan Bongrejo'],
            ['id' => '3201011002', 'district_id' => '320101', 'name' => 'Kelurahan Mugassari'],
            ['id' => '3201011003', 'district_id' => '320101', 'name' => 'Kelurahan Tanjungsari'],
            ['id' => '3201011004', 'district_id' => '320101', 'name' => 'Kelurahan Randugarut'],
            ['id' => '3201011005', 'district_id' => '320101', 'name' => 'Kelurahan Kuningan'],
        ];

        DB::table('reg_villages')->insert($villages);
    }
}
