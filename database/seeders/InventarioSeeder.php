<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $archivo = fopen(database_path('seeders/data/inventario.csv'), 'r');
        fgetcsv($archivo); // saltar encabezado

        $categorias = [];
        $lote = [];
        $ahora = now();

        while (($f = fgetcsv($archivo)) !== false) {
            [$nombre, $categoria, $compra, $venta, $stock, $activo] = $f;

            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = Categoria::firstOrCreate(['nombre' => $categoria])->id;
            }

            $lote[] = [
                'nombre' => $nombre,
                'precio_compra' => $compra,
                'precio_venta' => $venta,
                'stock' => $stock,
                'stock_minimo' => 5,
                'activo' => (bool) $activo,
                'categoria_id' => $categorias[$categoria],
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }
        fclose($archivo);

        foreach (array_chunk($lote, 500) as $grupo) {
            Producto::insert($grupo);
        }
    }
}
