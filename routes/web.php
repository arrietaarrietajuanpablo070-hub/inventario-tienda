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

// Categorías: CRUD completo (index, create, store, show, edit, update, destroy)
Route::resource('categorias', CategoriaController::class);

// Proveedores: CRUD completo
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

// Movimientos de inventario
Route::prefix('movimientos')->name('movimientos.')->controller(MovimientoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});