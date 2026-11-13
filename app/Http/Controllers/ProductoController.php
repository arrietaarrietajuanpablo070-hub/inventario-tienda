<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
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
        return view('productos.create', [
            'producto' => null,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')
            ->with('ok', 'Producto creado correctamente.');
    }

    public function show(Producto $producto): View
    {
        $producto->load('categoria', 'proveedor');
        $movimientos = $producto->movimientos()->latest()->limit(10)->get();

        return view('productos.show', compact('producto', 'movimientos'));
    }

    public function edit(Producto $producto): View
    {
        return view('productos.edit', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('ok', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        // Los movimientos se borran en cascada: se protege el historial.
        if ($producto->movimientos()->exists()) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar: el producto tiene movimientos de inventario.');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('ok', 'Producto eliminado.');
    }
}