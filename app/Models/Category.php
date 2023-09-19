<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Category
 *
 * @package App\Models
 * @property int $id
 * @property string $url
 * @property string $city
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'city_id',
        'address',
        'phone1',
        'phone2',
        'email',
        'email2',
        'mail_domain',
        'YmetricaId',
        'VENYOOId',
        'GMiframe1',
        'GMiframe2',
        'crm',
        'crm_pass',
        'crm_u',
    ];

    public function sites() {
        return $this->hasMany('App\Site', 'cat_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }

    public static function categoriesList(): Array
    {
        return DB::table('categories')->pluck('name', 'id')->toArray();
    }

    public static function urlsList(): Array
    {
        return DB::table('categories')->pluck('url', 'url')->toArray();
    }

    public static function namesList(): Array
    {
        return DB::table('categories')->pluck('name', 'name')->toArray();
    }
}
