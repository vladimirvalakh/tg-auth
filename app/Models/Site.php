<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Site
 *
 * @package App\Models
 * @property int $id
 * @property string $url
 * @property string $city
 */
class Site extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'city',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'cat_id');
    }
}
