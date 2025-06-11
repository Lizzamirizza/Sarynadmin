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
        ])->get('https://api-sandbox.collaborator.komerce.id/rajaongkir/province');

        dd($response->status(), $response->body());


        foreach ($response['rajaongkir']['results'] as $prov) {
            DB::table('provinces')->insert([
                'id' => $prov['province_id'],
                'name' => $prov['province'],
            ]);
        }
    }
}
