<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = $request->input('desde', now()->subDays(29)->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ventas = fn () => Venta::where('estado', 'completada')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta);

        $detalles = fn () => DetalleVenta::query()
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 'completada')
            ->whereDate('ventas.created_at', '>=', $desde)
            ->whereDate('ventas.created_at', '<=', $hasta);

        $resumen = [
            'ventas' => $ventas()->count(),
            'total' => (float) $ventas()->sum('total'),
            'hoy' => (float) Venta::where('estado', 'completada')->whereDate('created_at', now()->toDateString())->sum('total'),
            'ganancia' => (float) $detalles()
                ->selectRaw('SUM((detalle_ventas.precio_unitario - productos.precio_compra) * detalle_ventas.cantidad) as g')
                ->value('g'),
        ];

        $porDia = $ventas()
            ->selectRaw('DATE(created_at) as dia, COUNT(*) as ventas, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $masVendidos = $detalles()
            ->selectRaw('productos.id, productos.nombre, SUM(detalle_ventas.cantidad) as unidades, SUM(detalle_ventas.subtotal) as ingresos')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('unidades')
            ->limit(10)
            ->get();

        return view('reportes.index', compact('resumen', 'porDia', 'masVendidos', 'desde', 'hasta'));
    }
}