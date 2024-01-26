<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\City;

class LocationRepository extends CustomRepository
{
    public function getCity($cityId): ?City
    {
        return City::find($cityId);
    }

    public function getCityNameById($cityId): ?String
    {
        return City::find($cityId)->value('city');
    }

    public function getCityIdByName(string $cityName): ?int
    {
        return City::where('city', $cityName)->value('id');
    }
}
