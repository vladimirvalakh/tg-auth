<?php

namespace App\Http\Controllers;

class InfoController extends Controller
{
    public function showApiDetails()
    {
        return view('profile.info.api_details');
    }
}
