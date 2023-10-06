<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use App\Models\Site;
use App\Models\Rent;
use App\Models\City;
use App\Models\Category;
use App\Models\User;

class ModalController extends Controller
{
    public function showLast10orders(int $siteId)
    {
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
            ->where('sites.id', $siteId)
            ->limit(2)
            ->orderBy('orders.date', 'DESC');

        $dataProvider = new EloquentDataProvider($sites);

        $gridDataModal =  [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 10,
            'use_filters' => false,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',


            'columnFields' => [
                [
                    'label' => 'Дата',
                    'value' => function ($row) {
                        return ($row->order_date) ? Carbon::parse($row->order_date)->format('d.m.Y H:m') : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return  Str::mask($row->url, '*', 2, -4);
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Телефон',
                    'format' => 'html',
                    'value' => function ($row) {
                        return  Str::mask($row->order_phone, '*', 2, -4);
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Текст заявки',
                    'value' => function ($row) {
                        return $row->order_info;
                    },
                    'filter' => false,
                ],
            ],
        ];

        //$count = $sites->count();

        //var_dump($count);
        //die();

        return response()->json(grid_view($gridDataModal), Response::HTTP_OK);
    }

    public function get30daysOrders(int $siteId)
    {
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
            ->where('sites.id', $siteId)
            ->where('orders.date', '>=', Carbon::now()->subDays(30))
            ->orderBy('orders.date', 'DESC');

        $dataProvider = new EloquentDataProvider($sites);

        $gridDataModal =  [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => false,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',


            'columnFields' => [
                [
                    'label' => 'Дата',
                    'value' => function ($row) {
                        return ($row->order_date) ? Carbon::parse($row->order_date)->format('d.m.Y H:m') : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return  Str::mask($row->url, '*', 2, -4);
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Телефон',
                    'format' => 'html',
                    'value' => function ($row) {
                        return  Str::mask($row->order_phone, '*', 2, -4);
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Текст заявки',
                    'value' => function ($row) {
                        return $row->order_info;
                    },
                    'filter' => false,
                ],
            ],
        ];

        //$count = $sites->count();

        //var_dump($count);
        //die();

        return response()->json(grid_view($gridDataModal), Response::HTTP_OK);
    }
}
