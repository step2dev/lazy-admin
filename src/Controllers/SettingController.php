<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('lazy::settings.index');
    }

    public function show(): View
    {
        return view('lazy::settings.show');
    }
}
