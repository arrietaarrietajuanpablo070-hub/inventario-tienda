<x-layouts.app titulo="{{ $proveedor->nombre }}">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $proveedor->nombre }}</h1>
                <dl class="text-gray-700 mt-2 space-y-1">
                    <div><dt class="inline font-semibold">Teléfono:</dt> <dd class="inline">{{ $proveedor->telefono ?: 'No registrado' }}</dd></div>
                    <div><dt class="inline font-semibold">Correo:</dt> <dd class="inline">{{ $proveedor->email ?: 'No registrado' }}</dd></div>
                    <div><dt class="inline font-semibold">Dirección:</dt> <dd class="inline">{{ $proveedor->direccion ?: 'No registrada' }}</dd></div>
                </dl>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar</a>
                <a href="{{ route('proveedores.index') }}" class="px-4 py-2 rounded border">← Volver</a>
            </div>
        </div>

        <h2 class="text-xl font-bold mb-2">Productos de este proveedor ({{ number_format($productos->total(), 0, ',', '.') }})</h2>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Producto</th>
                    <th class="text-right">Precio compra</th>
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $p)
                    <tr class="border-b">
                        <td class="py-2">{{ $p->nombre }}</td>
                        <td class="text-right">${{ number_format($p->precio_compra, 0, ',', '.') }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-gray-500">Este proveedor aún no tiene productos asignados.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $productos->links() }}
        </div>
    </div>
</x-layouts.app>