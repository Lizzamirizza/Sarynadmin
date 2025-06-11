<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AddressController extends Controller
{
    private $apiKey;
    private $client;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
        $this->client = new Client([
            'base_uri' => 'https://api.rajaongkir.com/starter/', // ganti 'starter' ke 'pro' jika kamu pakai akun Pro
            'headers' => [
                'key' => $this->apiKey
            ]
        ]);
    }

    // ✅ Ambil semua provinsi
    public function getProvinsi()
    {
        $response = $this->client->get('province');
        return response()->json(json_decode($response->getBody(), true));
    }

    // ✅ Ambil kota berdasarkan province_id
    public function getKota(Request $request)
    {
        $provinceId = $request->query('province_id');
        $response = $this->client->get("city?province={$provinceId}");
        return response()->json(json_decode($response->getBody(), true));
    }

    // ✅ Ambil kecamatan berdasarkan city_id (KHUSUS RAJAONGKIR PRO)
    public function getKecamatan(Request $request)
    {
        $cityId = $request->query('city_id');
        $clientPro = new Client([
            'base_uri' => 'https://pro.rajaongkir.com/api/',
            'headers' => [
                'key' => $this->apiKey
            ]
        ]);
        $response = $clientPro->get("subdistrict?city={$cityId}");
        return response()->json(json_decode($response->getBody(), true));
    }

    // ✅ Cek ongkir
    public function cekOngkir(Request $request)
    {
        $origin = $request->input('origin'); // biasanya ID kota/kecamatan asal
        $destination = $request->input('destination'); // ID tujuan
        $weight = $request->input('weight'); // berat dalam gram
        $courier = $request->input('courier'); // jne, tiki, pos, dll

        $response = $this->client->post('cost', [
            'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }
}
