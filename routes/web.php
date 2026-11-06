<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/productos');

// Productos
Route::prefix('productos')->name('productos.')->controller(ProductoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/crear', 'create')->name('create');
    Route::get('/{producto}/editar', 'edit')->name('edit');
    Route::delete('/{producto}', 'destroy')->name('destroy');
});

// Categorías (GET, POST, PUT/PATCH y DELETE en una sola línea)
Route::resource('categorias', CategoriaController::class)->except('show');

// Proveedores
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor'])
    ->except('show');

// Movimientos de inventario
Route::prefix('movimientos')->name('movimientos.')->controller(MovimientoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});