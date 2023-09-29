<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Itstructure\GridView\Actions\Button;
use Itstructure\GridView\Actions\CustomHtmlTag;
use Itstructure\GridView\Actions\Delete;
use Itstructure\GridView\Columns\ActionColumn;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Rent;
use App\Models\City;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Itstructure\GridView\Filters\DropdownFilter;

class HomeController extends Controller
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

    public function siteView(Site $site)
    {
        return view('site/view', [
            'site' => $site
        ]);
    }

    public function siteData(Site $site)
    {
        return [
            'site' => $site,
            'location' => $site->location,
            'period_date' => Carbon::now()->addMonth()->format('d.m.Y'),
        ];
    }

    public function siteEdit(Site $site)
    {
        $categories = DB::table('categories')->pluck('name', 'id')->toArray();
        $cities = DB::table('cities')->pluck('city', 'id')->toArray();

        return view('site/edit', [
            'site' => $site,
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }

    public function siteUpdate(Request $request, Site $site)
    {
        Site::find($site->id)->update([
            'cat_id' => $request->input('cat_id'),
            'url' => $request->input('url'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
            'phone1' => $request->input('phone1'),
            'phone2' => $request->input('phone2'),
            'email' => $request->input('email'),
            'email2' => $request->input('email2'),
            'koeff' => $request->input('koeff'),
            'mail_domain' => $request->input('mail_domain'),
            'YmetricaId' => $request->input('YmetricaId'),
            'VENYOOId' => $request->input('VENYOOId'),
            'tgchatid' => $request->input('tgchatid'),
            'GMiframe1' => $request->input('GMiframe1'),
            'GMiframe2' => $request->input('GMiframe2'),
            'areas' => $request->input('areas'),
            'crm' => $request->input('crm'),
            'crm_pass' => $request->input('crm_pass'),
            'crm_u' => $request->input('crm_u'),
            'prf' => $request->input('prf'),
        ]);

        return Redirect::route('sites')->with('status', 'site-updated');
    }

    public function siteDestroy(Site $site)
    {
        $site = Site::findOrFail($site->id);
        $site->delete();

        return Redirect::to('/');
    }

    public function sites()
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

        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }

    public function roles()
    {

        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role');
        }

        $dataProvider = new EloquentDataProvider(Role::query());

        $gridData = [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'name',
                    'label' => 'Роль',
                ],
            ],
        ];

        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }

    /**
     * ARENDATOR DASHBOARD
     */
    private function getDashboardForArendatorRole(): array
    {
        $sites =  Site::select(
            'sites.id as site_id',
            'city_id',
            'url',
            'cat_id',
            'rents.status as rent_status',
            'rents.p90 as rent_p90',
            'rents.p30 as rent_p30',
            'rents.period as rent_period',
        )->whereIn('sites.city_id', json_decode(auth()->user()->cities, true))
        ->join('rents', 'rents.site_id', '=', 'sites.id')
        ->where('rents.status', 'В поиске');

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
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'city_id', //for some reason works LIKE
                        'data' => City::userCitiesList(),
                    ],
                ],
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return  Str::mask($row->url, '*', 2, -4);
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Site::urlsList(),
                    ],
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
                    'htmlAttributes' => [
                        'width' => '170'
                    ],
                    'actionTypes' => [ // Required
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/site/' . $data->site_id . '/data';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-success rent-site-modal-button">Взять в аренду</button>',
                        ]
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
