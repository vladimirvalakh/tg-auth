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
    public const ARENDATOR_SLUG = 'arendator';
    public const MODERATOR_SLUG = 'moderator';
    public const MANAGER_SLUG = 'manager';
    public const OWNER_SLUG = 'owner';
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

    public static function getRoleName($role_id)
    {
        return Role::where('id', $role_id)->value('name');
    }
}
