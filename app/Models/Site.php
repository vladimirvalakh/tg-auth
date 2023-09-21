<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Site
 *
 * @package App\Models
 * @property int $id
 * @property string $url
 * @property integer $city_id
 * @property string $address
 * @property string $phone1,
 * @property string $phone2,
 * @property string $email
 * @property string $email2
 * @property string $koeff,
 * @property string $mail_domain,
 * @property string $YmetricaId
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
        'city_id',
        'cat_id',
        'address',
        'phone1',
        'phone2',
        'email',
        'email2',
        'koeff',
        'mail_domain',
        'YmetricaId',
        'VENYOOId',
        'tgchatid',
        'GMiframe1',
        'GMiframe2',
        'areas',
        'crm',
        'crm_pass',
        'crm_u',
        'prf'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'cat_id');
    }

    public function rent()
    {
        return $this->hasOne('App\Models\Rent', 'site_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }

    public static function urlsList(): Array
    {
        return DB::table('sites')->pluck('url', 'url')->toArray();
    }

    public static function prfList(): Array
    {
        return DB::table('sites')->pluck('prf', 'prf')->toArray();
    }
}
