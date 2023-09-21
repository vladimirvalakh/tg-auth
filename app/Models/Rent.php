<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rent
 *
 * @package App\Models
 * @property int $id
 * @property string $site_id
 * @property string $status
 */
class Rent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'status',
    ];

    public function sites()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
}
