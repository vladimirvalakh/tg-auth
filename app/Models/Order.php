<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

/**
 * Class Order
 *
 * @package App\Models
 * @property int $id
 * @property Carbon $date
 * @property string $phone
 * @property int $city_id
 * @property int $site_id
 * @property int $status
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'phone',
        'city_id',
        'site_id',
        'user_id',
        'status',
        'emails',
        'viber',
        'source',
        'info'
    ];

    public function users() {
        return $this->hasMany('App\User', 'user_id');
    }

    public function site() {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

}
