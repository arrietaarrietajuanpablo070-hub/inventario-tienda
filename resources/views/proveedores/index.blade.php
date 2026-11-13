<x-layouts.app titulo="Proveedores">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Proveedores</h1>
            <a href="{{ route('proveedores.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nuevo proveedor</a>
        </div>

        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
        @endif

        <form method="GET" action="{{ route('proveedores.index') }}" class="flex gap-3 mb-4">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre o teléfono..." class="flex-1 border rounded px-3 py-2">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Buscar</button>
        </form>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th class="text-right">Productos</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proveedores as $p)
                    <tr class="border-b">
                        <td class="py-2"><a href="{{ route('proveedores.show', $p) }}" class="text-green-700 hover:underline">{{ $p->nombre }}</a></td>
                        <td>{{ $p->telefono }}</td>
                        <td>{{ $p->email }}</td>
                        <td class="text-right">{{ number_format($p->productos_count, 0, ',', '.') }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('proveedores.edit', $p) }}" class="text-blue-600 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('proveedores.destroy', $p) }}" class="inline"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar este proveedor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-3">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-500">Aún no hay proveedores. Crea el primero.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $proveedores->links() }}
        </div>
    </div>
</x-layouts.app>