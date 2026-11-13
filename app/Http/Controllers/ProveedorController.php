<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->string('buscar')->toString();

        $proveedores = Proveedor::withCount('productos')
            ->when($buscar, fn ($q) => $q->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%");
            }))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores', 'buscar'));
    }

    public function create(): View
    {
        return view('proveedores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate($this->reglas());

        Proveedor::create($datos);

        return redirect()->route('proveedores.index')
            ->with('ok', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $datos = $request->validate($this->reglas());

        $proveedor->update($datos);

        return redirect()->route('proveedores.index')
            ->with('ok', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        // En la migración, proveedor_id es nullOnDelete: los productos
        // no se borran, solo quedan sin proveedor.
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('ok', 'Proveedor eliminado. Sus productos quedaron sin proveedor.');
    }
        public function show(Proveedor $proveedor): View
    {
        $productos = $proveedor->productos()->orderBy('nombre')->paginate(10);

        return view('proveedores.show', compact('proveedor', 'productos'));
    }

    private function reglas(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ];
    }
}