<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function getRegionCities(int $cityId): JsonResponse
    {
        $cities =  City::citiesfListByRegionByCityId($cityId);

        return response()->json($cities, Response::HTTP_OK);
    }
}
