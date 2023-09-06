<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function users() {
        return $this->hasMany('App\User', 'role_id');
    }
}
