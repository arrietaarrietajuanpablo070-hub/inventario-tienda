<x-layouts.app titulo="Catálogo">
    <div class="max-w-5xl mx-auto bg-white border rounded-lg p-6">
        <h1 class="text-xl font-semibold">Catálogo</h1>
        <p class="text-gray-500 mb-4">Productos disponibles hoy en la tienda.</p>

        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
        @endif

        <form method="GET" action="{{ route('catalogo.index') }}" class="flex flex-wrap gap-3 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="¿Qué producto buscas?" class="flex-1 min-w-48 border rounded px-3 py-2">
            <select name="categoria" class="border rounded px-3 py-2">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $c)
                    <option value="{{ $c->id }}" @selected($categoriaId === $c->id)>{{ $c->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Buscar</button>
        </form>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 font-semibold">Producto</th>
                    <th class="font-semibold">Categoría</th>
                    <th class="text-right font-semibold">Precio</th>
                    <th class="text-right font-semibold">Disponibilidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $p)
                    <tr class="border-b">
                        <td class="py-2">{{ $p->nombre }}</td>
                        <td>{{ $p->categoria->nombre }}</td>
                        <td class="text-right font-semibold">${{ number_format($p->precio_venta, 0, ',', '.') }}</td>
                        <td class="text-right {{ $p->stock <= $p->stock_minimo ? 'text-alerta' : 'text-green-700' }}">
                            {{ $p->stock <= $p->stock_minimo ? 'Quedan pocas' : 'Disponible' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-500">No encontramos productos con ese filtro. Prueba con otra palabra.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $productos->links() }}
        </div>
    </div>
</x-layouts.app>