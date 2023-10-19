<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class City
 *
 * @package App\Models
 * @property int $id
 * @property string $city
 * @property string $locative
 * @property string $subject_rf
 * @property integer $distance
 * @property string $population
 * @property string $tax
 * @property integer $price_per_lead
 * @property integer $rental_price_per_month
 */
class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'city',
        'locative',
        'subject_rf',
        'distance',
        'population',
        'tax',
        'price_per_lead',
        'rental_price_per_month'
    ];

    public function sites()
    {
        return $this->hasMany('App\Site', 'city_id');
    }

    public static function citiesList(): ?array
    {
        return DB::table('cities')->orderBy('city')->pluck('city', 'id')->toArray();
    }

    public static function userSubjectRFList(): ?array
    {
        if (empty(json_decode(auth()->user()->cities))) {
            return [];
        }
        return DB::table('cities')->whereIn('id', json_decode(auth()->user()->cities))->orderBy('city')->pluck('subject_rf', 'subject_rf')->toArray();
    }

    public static function userCitiesList(): ?array
    {
        if (empty(json_decode(auth()->user()->cities))) {
            return [];
        }
        return DB::table('cities')->whereIn('id', json_decode(auth()->user()->cities))->orderBy('city')->pluck('city', 'id')->toArray();
    }

    public static function subjectsRfList(): ?array
    {
        return DB::table('cities')->orderBy('subject_rf')->pluck('subject_rf', 'subject_rf')->toArray();
    }

    public static function regionsList(): ?array
    {
        $list = DB::table('cities')
            ->orderBy('id')
            ->pluck('subject_rf', 'id')
            ->toArray();

        return array_unique($list);
    }

    public static function citiesfListByRegionByCityId(int $cityId): ?array
    {
        $region = DB::table('cities')->where('id', $cityId)
            ->select('subject_rf')
            ->first();

        return DB::table('cities')
            ->where('subject_rf', $region->subject_rf)
            ->orderBy('city')
            ->pluck('city', 'id')
            ->toArray();
    }
}
