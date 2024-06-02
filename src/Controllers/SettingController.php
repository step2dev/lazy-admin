<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function index()
    {
        return view('lazy::settings.index');
    }
}
