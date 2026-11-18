<x-layouts.app titulo="Reportes">
    <div class="max-w-5xl mx-auto">
        <form method="GET" action="{{ route('reportes.index') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-semibold">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="border rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">Ver reporte</button>
        </form>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Vendido hoy</p>
                <p class="text-2xl font-bold">${{ number_format($resumen['hoy'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Ventas en el rango</p>
                <p class="text-2xl font-bold">{{ number_format($resumen['ventas'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Total del rango</p>
                <p class="text-2xl font-bold">${{ number_format($resumen['total'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Ganancia estimada</p>
                <p class="text-2xl font-bold text-green-700">${{ number_format($resumen['ganancia'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-3">Ventas por día</h2>
                @php $max = $porDia->max('total') ?: 1; @endphp
                @forelse ($porDia as $f)
                    <div class="mb-3">
                        <div class="flex justify-between text-sm">
                            <span>{{ \Carbon\Carbon::parse($f->dia)->format('d/m/Y') }} · {{ $f->ventas }} ventas</span>
                            <span class="font-semibold">${{ number_format($f->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-gray-100 rounded h-3">
                            <div class="bg-green-600 h-3 rounded" style="width: {{ round($f->total / $max * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No hay ventas en este rango.</p>
                @endforelse
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-3">Productos más vendidos</h2>
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b font-semibold">
                            <th class="py-2">Producto</th>
                            <th class="text-right">Unidades</th>
                            <th class="text-right">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($masVendidos as $m)
                            <tr class="border-b">
                                <td class="py-2">{{ $m->nombre }}</td>
                                <td class="text-right">{{ rtrim(rtrim(number_format($m->unidades, 2, ',', '.'), '0'), ',') }}</td>
                                <td class="text-right">${{ number_format($m->ingresos, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">Aún no hay ventas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>