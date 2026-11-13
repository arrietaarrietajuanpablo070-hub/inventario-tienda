<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/productos');

// CRUD completo: GET, POST, PUT/PATCH y DELETE
Route::resource('productos', ProductoController::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

// Movimientos de inventario
Route::prefix('movimientos')->name('movimientos.')->controller(MovimientoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});