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
    public const IN_SEARCH_STATUS = 'В поиске';
    public const ON_RENT_STATUS = 'В аренде';
    public const ON_MODERATION_STATUS = 'На модерации';
    public const ORDER_STATUS_DECLINED = 'Отклонён';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'phone',
        'city_id',
        'tow',
        'site_id',
        'user_id',
        'status',
        'order_status',
        'comm_moderator',
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
