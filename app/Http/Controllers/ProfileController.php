<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Exception;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
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
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
//        $request->validateWithBag('userDeletion', [
//            'password' => ['required', 'current_password'],
//        ]);

        $user = $request->user();

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
