<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Site;
use App\Models\Order;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use App\Models\Rent;
use Itstructure\GridView\Actions\CustomHtmlTag;
use Itstructure\GridView\Columns\ActionColumn;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use Itstructure\GridView\Filters\DropdownFilter;

class OrderController extends Controller
{
    public function callAction($method, $parameters)
    {
        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role', [
                'user' => auth()->user(),
                'roles' => DB::table('roles')->pluck('name', 'id')->toArray(),
            ]);
        }

        if ($currentRole && $currentRole->slug == Role::ARENDATOR_SLUG && !auth()->user()->cities) {
            return view('set_city', [
                'user' => auth()->user(),
                'cities' => City::citiesList(),
            ]);
        }

        return parent::callAction($method, $parameters);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['source'] = 'Создано владельцем сайта';
        $data['order_status'] = Order::ON_MODERATION_STATUS;
        $data['info'] = '';

        Order::create($data);

        if (!empty($data['rent_id'])) {
            $rent = Rent::where('id', $data['rent_id'])->first();
            $rent['status'] = Rent::ON_MODERATION_STATUS;
            $rent->save();
        }


        return Redirect::route('sites')->with('success','Заявка успешно создана, перейдите в <a href="'. route('orders') . '">личный кабинет</a> для управления.');
    }

    public function list()
    {
        $currentRole = auth()->user()->role;
        $dataProvider = new EloquentDataProvider(Site::query());

        if ($currentRole->slug === Role::MODERATOR_SLUG) {
            $gridData = $this->getDashboardForModeratorRole();
        } elseif ($currentRole->slug === Role::ARENDATOR_SLUG) {
            $gridData = $this->getDashboardForArendatorRole();
        } elseif ($currentRole->slug === Role::MANAGER_SLUG) {
            $gridData = $this->getDashboardForManagerRole();
        } elseif ($currentRole->slug === Role::OWNER_SLUG) {
            $gridData = $this->getDashboardForOwnerRole();
        }else {
            $gridData = $this->getDashboardForModeratorRole();
        }

        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData,
        ]);
    }

    public function add()
    {
        $currentRole = auth()->user()->role;

        $updateCriteria = ($currentRole->slug == Role::OWNER_SLUG);

        if (!$updateCriteria) {
            abort(403);
        }

        return view('order/add', [
            'cities' => City::userCitiesList(),
        ]);
    }

    public function edit(Order $order)
    {
        $currentRole = auth()->user()->role;

        $updateCriteria = ($currentRole->slug == Role::ARENDATOR_SLUG && $order->user_id == Auth::id())
            || $currentRole->slug == Role::MODERATOR_SLUG;

        if (!$updateCriteria) {
            abort(403);
        }

        return view('order/edit', [
            'order' => $order,
            'cities' => City::userCitiesList(),
        ]);
    }

    public function approve(Order $order)
    {
        $currentRole = auth()->user()->role;

        if ($currentRole->slug != Role::MODERATOR_SLUG) {
            abort(403);
        }

        $order['order_status'] = Order::ON_RENT_STATUS;
        $order['rental_period_up_to'] = Carbon::create($order['date'])->addMonth();
        $order->save();

        $rent = Rent::where('site_id', $order['site_id'])->first();
        $rent['status'] = Rent::ON_RENT_STATUS;
        $rent['user_id'] = $order['user_id'];
        $rent['emails'] = $order['emails'];
        $rent->save();

        return Redirect::to('orders')->with('success','Заявка одобрена.');
    }

    public function destroy(Order $order)
    {
        $currentRole = auth()->user()->role;

        $deleteCriteria = ($currentRole->slug == Role::ARENDATOR_SLUG && $order->user_id == Auth::id())
            || $currentRole->slug == Role::MODERATOR_SLUG
            || ($currentRole->slug == Role::OWNER_SLUG && $order->site->rent->status != Rent::ON_RENT_STATUS);

        if (!$deleteCriteria) {
            abort(403);
        }

        $order = Order::findOrFail($order->id);
        $order->delete();

        $rent = Rent::where('site_id', $order['site_id'])->first();
        $rent['status'] = Rent::IN_SEARCH_STATUS;
        $rent->save();

        return Redirect::to('orders')->with('success','Заявка удалена.');
    }

    public function update(Request $request, Order $order)
    {
        $currentRole = auth()->user()->role;

        $updateCriteria = ($currentRole->slug == Role::ARENDATOR_SLUG && $order->user_id == Auth::id())
            || $currentRole->slug == Role::MODERATOR_SLUG;

        if (!$updateCriteria) {
            abort(403);
        }

        Order::find($order->id)->update([
            'phone' => $request->input('phone'),
            'viber' => $request->input('viber'),
            'emails' => $request->input('emails'),
        ]);

        return Redirect::route('orders')->with('success','Заявка успешно обновлена.');
    }

    /**
     * ARENDATOR DASHBOARD
     */
    private function getDashboardForArendatorRole(): array
    {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.source',
            'cities.id as cities_id',
            'url',
            'cat_id',
            'rents.status as rent_status',
            'rents.p90 as rent_p90',
            'rents.p30 as rent_p30',
            'rents.period as rent_period',
            'cities.rental_price_per_month as rental_price_per_month'
        )->whereIn('sites.city_id', json_decode(auth()->user()->cities, true))
            ->join('rents', 'rents.site_id', '=', 'sites.id')
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id')
            ->where('orders.user_id', auth()->user()->id);

        $dataProvider = new EloquentDataProvider($sites);

        return [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'subject_rf',
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'orders.city_id',
                        'data' => City::userCitiesList(),
                    ],
                ],
                [
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Site::urlsList(),
                    ],
                ],
                [
                    'attribute' => 'rent_status',
                    'label' => 'Статус аренды',
                    'format' => 'html',
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'rents.status',
                        'data' => DB::table('rents')->pluck('status', 'status')->toArray()
                    ],
                    'value' => function ($row) {
                        $class = ($row->rent_status == Rent::ON_RENT_STATUS) ? "text-success" : "";
                        return "<span class='". $class ."'>" . $row->rent_status . "</span>";
                    },
                ],
                [
                    'attribute' => 'rental_period_up_to',
                    'label' => 'Срок аренды до',
                    'filter' => false,
                ],
//                [
//                    'attribute' => 'source',
//                    'label' => 'Источник заявок',
//                    'filter' => false,
//                ],
                [
                    'attribute' => 'rent_p90',
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent_p90) ? $row->rent_p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'rent_p30',
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent_p30) ? $row->rent_p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'rental_price_per_month',
                    'label' => 'Цена аренды за месяц',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->rental_price_per_month : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды',
                    'value' => function ($row) {
                        return ($row->rent_period) ? $row->rent_period : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class, // Required
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/site/' . $data->site_id . '/data';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block  btn-success rent-site-modal-button mb-1">Продлить аренду</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/edit';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-warning mb-1">Обновить контакты</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/destroy';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-danger delete-order">Прекратить аренду</button>',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * MODERATOR DASHBOARD
     */
    private function getDashboardForModeratorRole(): array
    {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.source',
            'orders.order_status as order_status',
            'cities.id as cities_id',
            'cities.price_per_lead as price_per_lead',
            'url',
            'cat_id',
            'rents.status as rent_status',
            'rents.p90 as rent_p90',
            'rents.p30 as rent_p30',
            'rents.period as rent_period',
        )
            ->join('rents', 'rents.site_id', '=', 'sites.id')
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id')
            ->where('order_status', Order::ON_MODERATION_STATUS)
            ->orderBy('rents.status', 'DESC');

        $dataProvider = new EloquentDataProvider($sites);

        return [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',


            'columnFields' => [
                [
                    'attribute' => 'subject_rf',
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'orders.city_id', //for some reason works LIKE
                        'data' => City::citiesList(),
                    ],
                ],
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Site::urlsList(),
                    ],
                ],
                [
                    'attribute' => 'rent_status',
                    'label' => 'Статус аренды',
                    'htmlAttributes' => [
                        'width' => '150'
                    ],
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'rents.status',
                        'data' => DB::table('rents')->pluck('status', 'status')->toArray()
                    ],
                ],
                [
                    'attribute' => 'rental_period_up_to',
                    'label' => 'Срок аренды до',
                    'filter' => false,
                ],
//                [
//                    'attribute' => 'source',
//                    'label' => 'Источник заявок',
//                    'filter' => false,
//                ],
                [
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent_p90) ? $row->rent_p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent_p30) ? $row->rent_p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'price_per_lead',
                    'label' => 'Цена за лид',
                    'filter' => false,
                ],
                [
                    'label' => 'Цена аренды за месяц',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->rental_price_per_month : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды',
                    'value' => function ($row) {
                        return ($row->rent_period) ? $row->rent_period : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/approve';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-success mb-1">Одобрить</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/edit';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-warning mb-1">Редактировать</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/destroy';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-danger">Отклонить заявку</button>',
                        ],
                    ],
                ],
            ],
        ];
    }


    /**
     * MANAGER DASHBOARD
     */
    private function getDashboardForManagerRole(): array
    {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.source',
            'cities.id as cities_id',
            'url',
            'cat_id',
            'rents.status as rent_status',
            'rents.p90 as rent_p90',
            'rents.p30 as rent_p30',
            'rents.period as rent_period',
        )
            ->join('rents', 'rents.site_id', '=', 'sites.id')
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id');

        $dataProvider = new EloquentDataProvider($sites);

        return [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'subject_rf',
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'orders.city_id', //for some reason works LIKE
                        'data' => City::userSubjectRFList(),
                    ],
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'orders.city_id', //for some reason works LIKE
                        'data' => City::citiesList(),
                    ],
                ],
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Site::urlsList(),
                    ],
                ],
                [
                    'attribute' => 'rent_status',
                    'label' => 'Статус аренды',
                    'format' => 'html',
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'rents.status',
                        'data' => DB::table('rents')->pluck('status', 'status')->toArray()
                    ],
                    'value' => function ($row) {
                        $class = ($row->rent_status == Rent::ON_RENT_STATUS) ? "text-success" : "";
                        return "<span class='". $class ."'>" . $row->rent_status . "</span>";
                    },
                ],
                [
                    'attribute' => 'rental_period_up_to',
                    'label' => 'Срок аренды до',
                    'filter' => false,
                ],
//                [
//                    'attribute' => 'source',
//                    'label' => 'Источник заявок',
//                    'filter' => false,
//                ],
                [
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent_p90) ? $row->rent_p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent_p30) ? $row->rent_p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Цена аренды за месяц',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->rental_price_per_month : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды',
                    'value' => function ($row) {
                        return ($row->rent_period) ? $row->rent_period : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class, // Required
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/edit';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-warning mb-1">Редактировать</button>',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * OWNER DASHBOARD
     */
    private function getDashboardForOwnerRole(): array
    {
        $sites =  Site::select(
            'sites.id as site_id',
            'sites.city_id as sites_city_id',
            'orders.city_id as city_id',
            'orders.rental_period_up_to',
            'orders.id as order_id',
            'orders.source',
            'cities.id as cities_id',
            'url',
            'cat_id',
            'rents.status as rent_status',
            'rents.p90 as rent_p90',
            'rents.p30 as rent_p30',
            'rents.period as rent_period',
        )
            ->join('rents', 'rents.site_id', '=', 'sites.id')
            ->join('orders', 'orders.site_id', '=', 'sites.id')
            ->join('cities', 'orders.city_id', '=', 'cities.id');

        $dataProvider = new EloquentDataProvider($sites);

        return [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'strictFilters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'subject_rf',
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'orders.city_id', //for some reason works LIKE
                        'data' => City::citiesList(),
                    ],
                ],
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Site::urlsList(),
                    ],
                ],
                [
                    'attribute' => 'rent_status',
                    'label' => 'Статус аренды',
                    'format' => 'html',
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'rents.status',
                        'data' => DB::table('rents')->pluck('status', 'status')->toArray()
                    ],
                    'value' => function ($row) {
                        $class = ($row->rent_status == Rent::ON_RENT_STATUS) ? "text-success" : "";
                        return "<span class='". $class ."'>" . $row->rent_status . "</span>";
                    },
                ],
                [
                    'attribute' => 'rental_period_up_to',
                    'label' => 'Срок аренды до',
                    'filter' => false,
                ],
//                [
//                    'attribute' => 'source',
//                    'label' => 'Источник заявок',
//                    'filter' => false,
//                ],
                [
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent_p90) ? $row->rent_p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent_p30) ? $row->rent_p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Цена аренды за месяц',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->rental_price_per_month : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды',
                    'value' => function ($row) {
                        return ($row->rent_period) ? $row->rent_period : "";
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                if ($data->rent_status != Rent::ON_RENT_STATUS) {
                                    return '/order/' . $data->order_id . '/destroy';
                                } else {
                                    return 'forbidden';
                                }
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-danger">Удалить</button>',
                        ],
                    ],
                ],
            ],
        ];
    }
}
