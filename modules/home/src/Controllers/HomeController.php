<?php

namespace SynApps\Modules\Home\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {

        return view('home::welcome');
    }

    public function dashboard()
    {
        synav()->setActiveMenu('home');

        return view('home::dashboard');
    }
}
