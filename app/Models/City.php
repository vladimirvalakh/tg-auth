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
    ];

    public function sites() {
        return $this->hasMany('App\Site', 'city_id');
    }

    public static function citiesList(): Array
    {
        return DB::table('cities')->orderBy('city')->pluck('city', 'id')->toArray();
    }

    public static function subjectsRfList(): Array
    {
        return DB::table('cities')->orderBy('subject_rf')->pluck('subject_rf', 'subject_rf')->toArray();
    }
}
