<x-layouts.app titulo="{{ $categoria->nombre }}">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $categoria->nombre }}</h1>
                <p class="text-gray-600">{{ $categoria->descripcion ?: 'Sin descripción.' }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ number_format($productos->total(), 0, ',', '.') }} productos en esta categoría</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('categorias.edit', $categoria) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar</a>
                <a href="{{ route('categorias.index') }}" class="px-4 py-2 rounded border">← Volver</a>
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Producto</th>
                    <th class="text-right">Precio venta</th>
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $p)
                    <tr class="border-b">
                        <td class="py-2">{{ $p->nombre }}</td>
                        <td class="text-right">${{ number_format($p->precio_venta, 0, ',', '.') }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-gray-500">Esta categoría aún no tiene productos.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $productos->links() }}
        </div>
    </div>
</x-layouts.app>