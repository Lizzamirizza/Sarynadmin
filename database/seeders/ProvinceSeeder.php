<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get('https://sandbox.rajaongkir.com/starter/province');

        dd($response->json());

        foreach ($response['rajaongkir']['results'] as $prov) {
            DB::table('provinces')->insert([
                'id' => $prov['province_id'],
                'name' => $prov['province'],
            ]);
        }
    }
}
