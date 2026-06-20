<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengelolaController extends Controller
{
    public function dashboard()
    {
        return view('pengelola.dashboard');
    }

    public function kantinIndex()
    {
        return view('pengelola.kantin.index');
    }
}
