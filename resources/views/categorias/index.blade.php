<x-layouts.app titulo="Categorías">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Categorías</h1>
            <a href="{{ route('categorias.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nueva categoría</a>
        </div>

        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('categorias.index') }}" class="flex gap-3 mb-4">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar categoría..." class="flex-1 border rounded px-3 py-2">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Buscar</button>
        </form>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Nombre</th>
                    <th>Descripción</th>
                    <th class="text-right">Productos</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $c)
                    <tr class="border-b">
                        <td class="py-2">{{ $c->nombre }}</td>
                        <td class="text-gray-600">{{ $c->descripcion }}</td>
                        <td class="text-right">{{ number_format($c->productos_count, 0, ',', '.') }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('categorias.edit', $c) }}" class="text-blue-600 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('categorias.destroy', $c) }}" class="inline"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar esta categoría?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-3">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">No hay categorías.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $categorias->links() }}
        </div>
    </div>
</x-layouts.app>