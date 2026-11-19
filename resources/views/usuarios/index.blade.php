<x-layouts.app titulo="Usuarios">
    <div class="max-w-5xl mx-auto bg-white border rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Usuarios</h1>
            <a href="{{ route('usuarios.create') }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Nuevo usuario</a>
        </div>

        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('usuarios.index') }}" class="flex gap-3 mb-4">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre o correo" class="flex-1 border rounded px-3 py-2">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Buscar</button>
        </form>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 font-semibold">Nombre</th>
                    <th class="font-semibold">Correo</th>
                    <th class="font-semibold">Rol</th>
                    <th class="text-right font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $u)
                    <tr class="border-b">
                        <td class="py-2">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ ['admin' => 'Administrador', 'cajero' => 'Cajero', 'cliente' => 'Cliente'][$u->rol] ?? $u->rol }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('usuarios.edit', $u) }}" class="text-blue-600 hover:underline">Editar</a>
                            @if (!$u->is(auth()->user()))
                                <form method="POST" action="{{ route('usuarios.destroy', $u) }}" class="inline"
                                      onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-alerta hover:underline ml-3">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-500">No hay usuarios con esa búsqueda.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>
</x-layouts.app>