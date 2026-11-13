<x-layouts.app titulo="{{ $producto->nombre }}">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $producto->nombre }}</h1>
                <p class="text-gray-600">{{ $producto->descripcion ?: 'Sin descripción.' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('productos.edit', $producto) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar</a>
                <a href="{{ route('productos.index') }}" class="px-4 py-2 rounded border">← Volver</a>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-3 mb-6">
            <div><dt class="text-sm text-gray-500">Categoría</dt><dd>{{ $producto->categoria->nombre }}</dd></div>
            <div><dt class="text-sm text-gray-500">Proveedor</dt><dd>{{ $producto->proveedor?->nombre ?? 'Sin proveedor' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Código de barras</dt><dd>{{ $producto->codigo_barras ?? 'No registrado' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Estado</dt><dd>{{ $producto->activo ? 'Activo' : 'Inactivo' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Precio de compra</dt><dd>${{ number_format($producto->precio_compra, 0, ',', '.') }}</dd></div>
            <div><dt class="text-sm text-gray-500">Precio de venta</dt><dd>${{ number_format($producto->precio_venta, 0, ',', '.') }}</dd></div>
            <div><dt class="text-sm text-gray-500">Stock</dt><dd>{{ rtrim(rtrim(number_format($producto->stock, 2, ',', '.'), '0'), ',') }}</dd></div>
            <div><dt class="text-sm text-gray-500">Stock mínimo</dt><dd>{{ $producto->stock_minimo }}</dd></div>
        </dl>

        <h2 class="text-xl font-bold mb-2">Últimos movimientos</h2>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Fecha</th>
                    <th>Tipo</th>
                    <th class="text-right">Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $m)
                    <tr class="border-b">
                        <td class="py-2">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($m->tipo) }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($m->cantidad, 2, ',', '.'), '0'), ',') }}</td>
                        <td>{{ $m->motivo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">Este producto aún no tiene movimientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>