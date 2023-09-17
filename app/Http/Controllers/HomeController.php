<?php

namespace App\Http\Controllers;

use http\Env\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Itstructure\GridView\Actions\Delete;
use Itstructure\GridView\Columns\ActionColumn;
use Itstructure\GridView\Columns\CheckboxColumn;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use App\Models\Site;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth;
use Illuminate\Support\Facades\DB;

use Itstructure\GridView\Filters\DropdownFilter;
use Itstructure\GridView\Formatters\UrlFormatter;

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

        return parent::callAction($method, $parameters);
    }

    public function siteView(Site $site)
    {
        return view('site/view', [
            'site' => $site
        ]);
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

    public function siteUpdate(Request $request)
    {
        $user = Site::findOrFail(Auth::id());
        $site->save();
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


        if ($currentRole->slug === 'moderator') {
            return view('moderator_dashboard');
        }


        $dataProvider = new EloquentDataProvider(Site::query());

        $categories = DB::table('categories')->pluck('name', 'id')->toArray();
        $cities = DB::table('cities')->pluck('city', 'id')->toArray();


        $gridData = [
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
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
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
                        'data' => $categories
                    ],
                    'htmlAttributes' => [
                        'width' => '15%'
                    ]
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
                        'data' => $cities
                    ],
                    'htmlAttributes' => [
                        'width' => '15%'
                    ]
                ],
                [
                    'attribute' => 'prf',
                    'label' => 'Партнёр',
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [ // Required
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
                        'htmlAttributes' => [ // Html attributes for <img> tag.
                            'width' => '350',
                        ]
                    ],
                ],
//                [
//                    'class' => CheckboxColumn::class,
//                    'field' => 'delete',
//                    'attribute' => 'id'
//                ]
//                [
//                    'class' => CheckboxColumn::class,
//                    'label' => 'Множ. удаление',
//                    'style' => 'font-size: 10px;',
//                    'field' => 'delete', // REQUIRED.
//                    'attribute' => 'id', // REQUIRED.
//                    'htmlAttributes' => [ // Html attributes for <img> tag.
//                        'width' => '50',
//                    ]
////                    'display' => function ($row) {
////                        return {...condition to return true for displaying...};
////                    }
//                ],
            ],
        ];


        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }

    public function categories()
    {

        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role');
        }

        if ($currentRole->slug === 'moderator') {
            return view('moderator_dashboard');
        }


        $dataProvider = new EloquentDataProvider(Category::query());

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
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                ],
                [
                    'attribute' => 'city',
                    'label' => 'Город',
                ],
                [
                    'attribute' => 'city2',
                    'label' => 'Город локатив',
                ],
            ],
        ];


        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }

    public function users()
    {

        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role');
        }

        if ($currentRole->slug === 'moderator') {
            return view('moderator_dashboard');
        }


        $dataProvider = new EloquentDataProvider(User::query());

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
                    'label' => 'Имя',
                    'htmlAttributes' => [
                        'width' => '30%'
                    ]
                ],
                [
                    'attribute' => 'role_id',
                    'label' => 'Роль',
                    'value' => function ($row) {
                        return ($row->role) ? $row->role->name : '';
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'role_id', // REQUIRED if 'attribute' is not defined for column.
                        'data' => DB::table('roles')->pluck('name', 'id')->toArray()
                    ],
                    'htmlAttributes' => [
                        'width' => '15%'
                    ]
                ],
            ],
        ];


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

        if ($currentRole->slug === 'moderator') {
            return view('moderator_dashboard');
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



//    /**
//     * Display the user's profile form.
//     */
//    public function edit(Request $request): View
//    {
//        return view('site.edit', [
//            'user' => $request->user(),
//        ]);
//    }



//    /**
//     * Update the user's profile information.
//     */
//    public function update(ProfileUpdateRequest $request): RedirectResponse
//    {
//        $request->user()->fill($request->validated());
//
//        if ($request->user()->isDirty('email')) {
//            $request->user()->email_verified_at = null;
//        }
//
//        $request->user()->save();
//
//        return Redirect::route('profile.edit')->with('status', 'profile-updated');
//    }

//    /**
//     * Delete the user's account.
//     */
//    public function destroy(Request $request): RedirectResponse
//    {
//        $request->validateWithBag('userDeletion', [
//            'password' => ['required', 'current_password'],
//        ]);
//
//        $user = $request->user();
//
//        Auth::logout();
//
//        $user->delete();
//
//        $request->session()->invalidate();
//        $request->session()->regenerateToken();
//
//        return Redirect::to('/');
//    }
//
//    private function checkTelegramAuthorization($auth_data) {
//        $check_hash = $auth_data['hash'];
//        unset($auth_data['hash']);
//        $data_check_arr = [];
//        foreach ($auth_data as $key => $value) {
//            $data_check_arr[] = $key . '=' . $value;
//        }
//        sort($data_check_arr);
//        $data_check_string = implode("\n", $data_check_arr);
//        $secret_key = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
//        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
//        if (strcmp($hash, $check_hash) !== 0) {
//            throw new Exception('Data is NOT from Telegram');
//        }
//        if ((time() - $auth_data['auth_date']) > 86400) {
//            throw new Exception('Data is outdated');
//        }
//        return $auth_data;
//    }
}
