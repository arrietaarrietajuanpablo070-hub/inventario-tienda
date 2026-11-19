<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

// Solo para quien NO ha iniciado sesión
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/registro', [RegistroController::class, 'create'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'store']);
});

// Para cualquier usuario con sesión
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        return redirect()->route(auth()->user()->rol === 'cliente' ? 'catalogo.index' : 'pos.index');
    });

    // Todos los roles
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

    // Administrador y cajero
    Route::middleware('rol:admin,cajero')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    });

    // Solo administrador
    Route::middleware('rol:admin')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::resource('categorias', CategoriaController::class);
        Route::resource('proveedores', ProveedorController::class)
            ->parameters(['proveedores' => 'proveedor']);
        Route::resource('usuarios', UsuarioController::class)->except('show');

        Route::prefix('movimientos')->name('movimientos.')->controller(MovimientoController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    });
});