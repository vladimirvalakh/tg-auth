<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Rent;

/**
 * Class Site
 *
 * @package App\Models
 * @property int $id
 * @property string $url
 * @property integer $city_id
 * @property int $last_month_orders_count
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
        'comment',
        'last_month_orders_count',
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

    public static function userUrlsList(): Array
    {
        //return DB::table('sites')->whereIn('id', json_decode(auth()->user()->cities))->orderBy('city')->pluck('city', 'id')->toArray();

        return DB::table('sites')->pluck('url', 'url')->toArray();
    }

    public static function urlsList(): Array
    {
        return DB::table('sites')->pluck('url', 'url')->toArray();
    }

    public static function getArendatorId($site_id)
    {
        return Rent::where('site_id', $site_id)->value('user_id');
    }

    public static function getCityId($site_id)
    {
        return Site::where('id', $site_id)->value('city_id');
    }

    public function getCityName()
    {
        return City::where('id', $this->city_id)->value('city');
    }

    public static function prfList(): Array
    {
        return DB::table('sites')->pluck('prf', 'prf')->toArray();
    }

    public function getRentalPeriodUpTo($siteId) {
        $order =  Site::select(
            'sites.id as site_id',
            'orders.rental_period_up_to',
            'orders.order_status',
            'orders.id as order_id',
        )
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->where('sites.id', $siteId)
//            ->where('order_status', Order::ON_MODERATION_STATUS)
            ->first();

        if (!$order) return  Carbon::now()->addMonth()->format('d.m.Y');

        return Carbon::create($order->rental_period_up_to)->format('d.m.Y');
    }

    public function getCountOrdersFor30days() {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.date as order_date',
            'orders.phone as order_phone',
            'orders.info as order_info',
            'cities.id as cities_id',
            'cities.price_per_lead as price_per_lead',
            'url',
        )
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id')
            ->where('sites.id', $this->id)
            ->where('orders.date', '>=', Carbon::now()->subDays(30))
            ->orderBy('orders.date', 'DESC');

        return $sites->count();
    }

    public function getCountOrdersFor91days() {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.date as order_date',
            'orders.phone as order_phone',
            'orders.info as order_info',
            'cities.id as cities_id',
            'cities.price_per_lead as price_per_lead',
            'url',
        )
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id')
            ->where('sites.id', $this->id)
            ->where('orders.date', '>=', Carbon::now()->subDays(91))
            ->orderBy('orders.date', 'DESC');

        return $sites->count();
    }
}
