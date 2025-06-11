<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function searchProvinces(Request $request)
    {
        $search = $request->query('q');
        return Province::where('name', 'like', "%$search%")->get();
    }

    public function searchCities(Request $request)
    {
        $search = $request->query('q');
        $provinceId = $request->query('province_id');

        $query = City::query()->where('name', 'like', "%$search%");
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        return $query->get();
    }
}
