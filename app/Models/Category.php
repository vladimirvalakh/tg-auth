<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Category
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $parent_id
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function sites() {
        return $this->hasMany('App\Site', 'cat_id');
    }

    public static function categoriesList(): Array
    {
        return DB::table('categories')->pluck('name', 'id')->toArray();
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id');
    }

    public static function namesList(): Array
    {
        return DB::table('categories')->pluck('name', 'name')->toArray();
    }
}
