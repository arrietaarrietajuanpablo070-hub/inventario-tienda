<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MovimientoController extends Controller
{
    public function index(): View
    {
        return view('movimientos.index');
    }
}