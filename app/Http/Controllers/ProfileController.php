<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\City;
use App\Models\Role;
use Exception;
use Itstructure\GridView\Actions\CustomHtmlTag;
use Itstructure\GridView\Columns\ActionColumn;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use Itstructure\GridView\Filters\DropdownFilter;

class ProfileController extends Controller
{

    public function list()
    {
        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role');
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
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'name',
                        'data' => DB::table('users')->pluck('name', 'name')->toArray()
                    ],
                ],
                [
                    'attribute' => 'role_id',
                    'label' => 'Роль',
                    'value' => function ($row) {
                        //return ($row->role) ? $row->role->name : '';
                        return ($row->role_id) ? Role::getRoleName($row->role_id) : '';
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'role_id', // REQUIRED if 'attribute' is not defined for column.
                        'data' => DB::table('roles')->pluck('name', 'id')->toArray()
                    ],
                ],
                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class, // Required
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($row) {
                                return '/user/' . $row->id . '/send-telegram-message';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-success send-telegram-message-modal-button mb-1">Отправить сообщение в Telegram</button>',
                        ],
                    ],
                ],
            ],
        ];


        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        if ($request->user()->id != Auth::id()) {
            abort(403);
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'roles' => DB::table('roles')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function telegram(Request $request)
    {
        if (isset($_GET['hash'])) {
            try {
                $auth_data = $this->checkTelegramAuthorization($_GET);

                $user = User::where('telegram_id', '=', $auth_data['id'])->first();

                if (!$user) {
                    $user = new \App\Models\User();
                    $user->telegram_id = $auth_data['id'];
                    $user->username = $auth_data['username'];
                    if ($auth_data['first_name']) {
                        $user->name = $auth_data['first_name'];
                    } else {
                        $user->name = $auth_data['username'];
                    }

                   // $user->name = $auth_data['first_name'] . ' ' . $auth_data['last_name'];
                    $user->save();
                }

                Auth::loginUsingId($user->id);

                return redirect()->route('sites');
            } catch (Exception $e) {
                die ($e->getMessage());
            }
        }
    }

    /**
     * Update the user's profile information.
     */
    public function roleUpdate(Request $request): RedirectResponse
    {
        $user = User::findOrFail(Auth::id());
        $user->role_id = $request->get('role_id');
        $user->save();

        return Redirect::route('sites')->with('status', 'role-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function firstScreenUpdate(Request $request): RedirectResponse
    {
        $user = User::findOrFail(Auth::id());
        $user->first_screen = $request->get('first_screen');
        $user->save();

        return Redirect::route('sites');
    }

    /**
     * Update the user's profile information.
     */
    public function cityUpdate(Request $request): RedirectResponse
    {
        $user = User::findOrFail(Auth::id());

        $user->cities = $request->get('cities');

        $user->save();

        return Redirect::route('sites')->with('status', 'city-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {

        $user = User::findOrFail(Auth::id());

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->full_name = $request->get('full_name');

        $bankCards = [];

        if (!empty($request->get('bank1') && !empty($request->get('card1')))) {
            $bankCards[] = [
                'bank' => $request->get('bank1'),
                'card_number' => $request->get('card1')
            ];
        }

        if (!empty($request->get('bank2') && !empty($request->get('card2')))) {
            $bankCards[] = [
                'bank' => $request->get('bank2'),
                'card_number' => $request->get('card2')
            ];
        }

        if (!empty($request->get('bank3') && !empty($request->get('card3')))) {
            $bankCards[] = [
                'bank' => $request->get('bank3'),
                'card_number' => $request->get('card3')
            ];
        }

        $user->full_name = $request->get('full_name');

        $user->phone = $request->get('phone');

        $user->bank_cards = json_encode($bankCards);

        $user->save();

        return Redirect::route('sites')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function settings(): View
    {
        return view('profile.settings', [
            'user' => auth()->user(),
            'cities' => City::citiesList(),
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function delete(): View
    {
        return view('profile.delete');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->user()->id != Auth::id()) {
            abort(403);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function checkTelegramAuthorization($auth_data) {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Data is NOT from Telegram');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }
        return $auth_data;
    }
}
