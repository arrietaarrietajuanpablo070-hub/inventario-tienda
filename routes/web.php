<?php

use App\Models\Producto;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/productos', 'productos.index');
Route::view('/productos/crear', 'productos.crear');

Route::get('/productos/{producto}/editar', function (Producto $producto) {
    return view('productos.editar', ['producto' => $producto]);
});

Route::view('/movimientos', 'movimientos.index');