<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $proveedores = collect([
                ['nombre' => 'Distribuidora El Paisa', 'telefono' => '3001112233', 'email' => 'pedidos@elpaisa.co', 'direccion' => 'Calle 10 # 5-20'],
                ['nombre' => 'Lácteos del Valle', 'telefono' => '3004445566', 'email' => 'ventas@lacteosdelvalle.co', 'direccion' => 'Carrera 8 # 12-45'],
                ['nombre' => 'Abarrotes La Economía', 'telefono' => '3007778899', 'email' => 'economia@abarrotes.co', 'direccion' => 'Avenida 3 # 7-10'],
            ])->map(fn ($d) => Proveedor::firstOrCreate(['nombre' => $d['nombre']], $d));

            // Productos de alta rotación y el proveedor que los surte.
            $patrones = [
                'COCA%' => 0, 'AGUA %' => 0, 'CERVEZA%' => 0, 'PAPAS%' => 0,
                'LECHE%' => 1, 'HUEVO%' => 1, 'PAN %' => 1,
                'ARROZ %' => 2, 'ACEITE %' => 2, 'GALLETA%' => 2, 'JABON %' => 2, 'PAPEL HIG%' => 2,
            ];

            $productos = collect();
            foreach ($patrones as $patron => $indice) {
                $p = Producto::where('activo', true)
                    ->where('precio_venta', '>', 0)
                    ->where('nombre', 'like', $patron)
                    ->orderBy('nombre')
                    ->first();

                if ($p) {
                    $p->update(['proveedor_id' => $proveedores[$indice]->id]);
                    $productos->push($p);
                }
            }

            // Entradas de mercancía de hace 8 días.
            $hace8 = now()->subDays(8)->setTime(9, 0);
            foreach ($productos as $p) {
                $p->refresh();
                $antes = (float) $p->stock;
                $nuevo = $antes + 40;
                $p->update(['stock' => $nuevo]);
                $this->movimiento($p, 'entrada', 40, $antes, $nuevo, 'Compra a proveedor', $hace8);
            }

            // Ventas de los últimos 7 días.
            $cajero = User::where('email', 'cajero@tienda.com')->first();
            $metodos = ['efectivo', 'efectivo', 'efectivo', 'nequi', 'tarjeta'];

            for ($i = 0; $i < 14; $i++) {
                $fecha = now()->subDays(mt_rand(0, 6))->setTime(mt_rand(8, 19), mt_rand(0, 59));

                $lineas = [];
                foreach ($productos->shuffle()->take(mt_rand(1, 4)) as $p) {
                    $p->refresh();
                    $cant = mt_rand(1, 3);
                    if ($cant <= (float) $p->stock) {
                        $lineas[] = [$p, $cant];
                    }
                }

                if (!$lineas) {
                    continue;
                }

                $total = collect($lineas)->sum(fn ($l) => $l[0]->precio_venta * $l[1]);
                $metodo = $metodos[array_rand($metodos)];
                $pago = $metodo === 'efectivo' ? ceil($total / 1000) * 1000 + (mt_rand(0, 1) * 5000) : $total;

                $venta = new Venta();
                $venta->forceFill([
                    'user_id' => $cajero?->id,
                    'total' => $total,
                    'pago' => $pago,
                    'cambio' => $pago - $total,
                    'metodo_pago' => $metodo,
                    'canal' => 'local',
                    'estado' => 'completada',
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ])->save();

                foreach ($lineas as [$p, $cant]) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $p->id,
                        'cantidad' => $cant,
                        'precio_unitario' => $p->precio_venta,
                        'subtotal' => $p->precio_venta * $cant,
                    ]);

                    $antes = (float) $p->stock;
                    $nuevo = $antes - $cant;
                    $p->update(['stock' => $nuevo]);
                    $this->movimiento($p, 'salida', $cant, $antes, $nuevo, 'Venta #' . $venta->id, $fecha, $cajero?->id);
                }
            }

            // Un producto con stock bajo para mostrar la alerta roja.
            $bajo = $productos->first(fn ($p) => str_starts_with($p->nombre, 'LECHE')) ?? $productos->last();
            if ($bajo) {
                $bajo->refresh();
                $antes = (float) $bajo->stock;
                $bajo->update(['stock' => 3]);
                $this->movimiento($bajo, 'ajuste', abs(3 - $antes), $antes, 3, 'Conteo físico', now()->subDay());
            }
        });
    }

    private function movimiento(Producto $p, string $tipo, float $cant, float $antes, float $nuevo, string $motivo, $fecha, ?int $userId = null): void
    {
        $m = new MovimientoInventario();
        $m->forceFill([
            'producto_id' => $p->id,
            'user_id' => $userId,
            'tipo' => $tipo,
            'cantidad' => $cant,
            'stock_anterior' => $antes,
            'stock_nuevo' => $nuevo,
            'motivo' => $motivo,
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ])->save();
    }
}
