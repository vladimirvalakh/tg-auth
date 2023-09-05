<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'city',
    ];

    public function sites() {
        return $this->hasMany('App\Site', 'cat_id');
    }
}
