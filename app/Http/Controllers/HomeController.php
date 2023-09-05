<?php

namespace App\Http\Controllers;

use Itstructure\GridView\DataProviders\EloquentDataProvider;
use App\Models\Site;
use Illuminate\Support\Facades\DB;


use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Exception;
use Itstructure\GridView\Filters\DropdownFilter;

class HomeController extends Controller
{
    public function list()
    {
        $dataProvider = new EloquentDataProvider(Site::query());

        $categories = DB::table('categories')->pluck('name', 'id')->toArray();

        $gridData = [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'useSendButtonAnyway' => true,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
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
                    'attribute' => 'city',
                    'label' => 'Город',
                ],
                [
                    'attribute' => 'city2',
                    'label' => 'Город локатив',
                ],
                [
                    'attribute' => 'prf',
                    'label' => 'Назначен',
                ],

//                [
//                    'label' => 'Actions', // Optional
//                    'class' => Itstructure\GridView\Columns\ActionColumn::class, // Required
//                    'actionTypes' => [ // Required
//                        'view',
//                        'delete',
////                        'edit' => function ($data) {
////                            return '/admin/pages/' . $data->id . '/edit';
////                        },
//                        [
//                            'class' => Itstructure\GridView\Actions\Delete::class, // Required
//                            'url' => function ($data) { // Optional
//                                return '/admin/pages/' . $data->id . '/delete';
//                            },
//                            'htmlAttributes' => [ // Optional
//                                'target' => '_blank',
//                                'style' => 'color: red; font-size: 16px;',
//                                'onclick' => 'return window.confirm("Вы уверены что хотите удалить?");'
//                            ],
//                        ],
//                    ],
//                ],
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
