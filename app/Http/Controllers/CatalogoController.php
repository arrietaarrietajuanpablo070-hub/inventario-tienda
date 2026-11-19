<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $categoriaId = $request->integer('categoria') ?: null;

        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->when($q, fn ($s) => $s->where('nombre', 'like', "%{$q}%"))
            ->when($categoriaId, fn ($s) => $s->where('categoria_id', $categoriaId))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $categorias = Categoria::orderBy('nombre')->get();

        return view('catalogo.index', compact('productos', 'categorias', 'q', 'categoriaId'));
    }
}