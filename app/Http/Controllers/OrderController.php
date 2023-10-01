<?php

namespace App\Http\Controllers;

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
use Itstructure\GridView\Actions\CustomHtmlTag;
use Itstructure\GridView\Actions\Delete;
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
        $data['source'] = 'Заявка на сайте';
        $data['info'] = '';

        Order::create($data);

        return Redirect::route('sites')->with('success','Заявка успешно создана, перейдите в личный кабинет для управления.');
    }

    public function list()
    {
        $currentRole = auth()->user()->role;
        $dataProvider = new EloquentDataProvider(Site::query());

        if ($currentRole->slug === Role::MODERATOR_SLUG) {
            $gridData = $this->getDashboardForModeratorRole();
        } elseif ($currentRole->slug === Role::ARENDATOR_SLUG) {
            $gridData = $this->getDashboardForArendatorRole();
        } else {
            $gridData = $this->getDefaultDashboard();
        }

        return view('orders', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }

    public function edit(Order $order)
    {
        return view('order/edit', [
            'order' => $order,
            'cities' => City::userCitiesList(),
        ]);
    }

    public function destroy(Order $order)
    {
        $order = Order::findOrFail($order->id);
        $order->delete();

        return Redirect::to('orders')->with('success','Заявка удалена.');;
    }

    public function update(Request $request, Order $order)
    {
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
                        'data' => City::userCitiesList(),
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
                    'attribute' => 'source',
                    'label' => 'Источник заявок',
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
                                return '/site/' . $data->site_id . '/data';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block  btn-success rent-site-modal-button mb-1">Продлить аренду</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/edit';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-warning mb-1">Обновить</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/order/' . $data->order_id . '/destroy';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-danger">Удалить</button>',
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
        $dataProvider = new EloquentDataProvider(Site::query());

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
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'city_id', //for some reason works LIKE
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
                    'attribute' => 'cat_id',
                    'label' => 'Категория',
                    'value' => function ($row) {
                        return $row->category->name;
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'cat_id', // REQUIRED if 'attribute' is not defined for column.
                        'data' => Category::categoriesList(),
                    ],
                ],
                [
                    'label' => 'Статус аренды',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->status : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды до',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->period : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Цена за лид',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->price_per_lead : "";
                    },
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [
                        'view' => function ($data) {
                            return '/site/' . $data->id . '/view';
                        },
                        'edit' => function ($data) {
                            return '/site/' . $data->id . '/edit';
                        },
                        [
                            'class' => Delete::class, // Required
                            'url' => function ($data) { // Optional
                                return '/site/' . $data->id . '/destroy';
                            },
                            'htmlAttributes' => [ // Optional
                                'onclick' => 'return window.confirm("Вы уверены, что хотите удалить?");'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * ARENDATOR DASHBOARD
     */
    private function getDefaultDashboard(): array
    {
        $dataProvider = new EloquentDataProvider(Site::query());

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
                    'label' => 'Субъект РФ',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->subject_rf : "";
                    },
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'city_id', //for some reason works LIKE
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
                    'attribute' => 'cat_id',
                    'label' => 'Категория',
                    'value' => function ($row) {
                        return $row->category->name;
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'cat_id', // REQUIRED if 'attribute' is not defined for column.
                        'data' => Category::categoriesList(),
                    ],
                ],
                [
                    'label' => 'Статус аренды',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->status : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Срок аренды до',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->period : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 3 мес',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->p90 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Заявок 30 дней',
                    'value' => function ($row) {
                        return ($row->rent) ? $row->rent->p30 : '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => 'Цена за лид',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->price_per_lead : "";
                    },
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [
                        'view' => function ($data) {
                            return '/site/' . $data->id . '/view';
                        },
                        'edit' => function ($data) {
                            return '/site/' . $data->id . '/edit';
                        },
                    ],
                ],
            ],
        ];
    }
}
