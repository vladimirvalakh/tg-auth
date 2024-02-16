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
    public const IN_SEARCH_STATUS = 'В поиске';
    public const ON_RENT_STATUS = 'В аренде';
    public const ON_MODERATION_STATUS = 'На модерации';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'status',
        'start_rent_date',
        'finish_rent_date'
    ];

    public function sites()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
}
