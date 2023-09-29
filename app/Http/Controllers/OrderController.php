<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Site;
use App\Models\Order;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['source'] = 'Заявка на сайте';
        $data['info'] = '';

        Order::create($data);

        return Redirect::route('sites');
    }
}
