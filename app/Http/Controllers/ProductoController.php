<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $resumen = [
            'productos' => Producto::count(),
            'categorias' => Categoria::count(),
            'proveedores' => Proveedor::count(),
            'valor' => (float) Producto::sum(DB::raw('precio_compra * stock')),
        ];

        return view('productos.index', compact('resumen'));
    }

    public function create(): View
    {
        return view('productos.crear');
    }

    public function edit(Producto $producto): View
    {
        return view('productos.editar', compact('producto'));
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado.');
    }
}