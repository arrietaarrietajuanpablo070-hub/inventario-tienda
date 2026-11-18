<x-layouts.app titulo="Ventas">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Ventas</p>
            <p class="text-2xl font-bold">{{ number_format($resumen['ventas'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total vendido</p>
            <p class="text-2xl font-bold">${{ number_format($resumen['total'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Promedio por venta</p>
            <p class="text-2xl font-bold">${{ number_format($resumen['promedio'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Historial de ventas</h1>
            <a href="{{ route('pos.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nueva venta</a>
        </div>

        <form method="GET" action="{{ route('ventas.index') }}" class="flex flex-wrap gap-3 items-end mb-4">
            <div>
                <label class="block text-sm font-semibold">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold">Método de pago</label>
                <select name="metodo" class="border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach (['efectivo' => 'Efectivo', 'nequi' => 'Nequi', 'tarjeta' => 'Tarjeta'] as $valor => $texto)
                        <option value="{{ $valor }}" @selected($metodo === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Filtrar</button>
        </form>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">N.°</th>
                    <th>Fecha</th>
                    <th class="text-right">Productos</th>
                    <th>Método</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ventas as $v)
                    <tr class="border-b">
                        <td class="py-2">#{{ $v->id }}</td>
                        <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">{{ $v->detalles_count }}</td>
                        <td>{{ ucfirst($v->metodo_pago) }}</td>
                        <td class="text-right">${{ number_format($v->total, 0, ',', '.') }}</td>
                        <td class="text-right"><a href="{{ route('ventas.show', $v) }}" class="text-green-700 hover:underline">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay ventas en este rango de fechas.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $ventas->links() }}
        </div>
    </div>
</x-layouts.app>