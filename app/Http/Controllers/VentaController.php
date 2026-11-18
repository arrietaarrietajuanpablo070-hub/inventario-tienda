<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'metodo' => ['nullable', 'in:efectivo,nequi,tarjeta'],
        ]);

        $desde = $request->input('desde', now()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());
        $metodo = $request->input('metodo');

        $query = Venta::query()
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->when($metodo, fn ($q) => $q->where('metodo_pago', $metodo));

        $resumen = [
            'ventas' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('total'),
        ];
        $resumen['promedio'] = $resumen['ventas'] ? $resumen['total'] / $resumen['ventas'] : 0;

        $ventas = $query->withCount('detalles')->latest()->paginate(15)->withQueryString();

        return view('ventas.index', compact('ventas', 'resumen', 'desde', 'hasta', 'metodo'));
    }

    public function show(Venta $venta): View
    {
        $venta->load('detalles.producto', 'user');

        return view('ventas.show', compact('venta'));
    }
}