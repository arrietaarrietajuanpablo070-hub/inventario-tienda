<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pos');

// CRUD completo: GET, POST, PUT/PATCH y DELETE
Route::resource('productos', ProductoController::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

// Movimientos de inventario
Route::prefix('movimientos')->name('movimientos.')->controller(MovimientoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});

// Caja (POS)
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

// Ventas y reportes
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');