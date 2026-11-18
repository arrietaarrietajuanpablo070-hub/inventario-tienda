<x-layouts.app titulo="Venta #{{ $venta->id }}">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">Venta #{{ $venta->id }}</h1>
                <p class="text-gray-600">{{ $venta->created_at->format('d/m/Y H:i') }} · {{ ucfirst($venta->metodo_pago) }} · {{ ucfirst($venta->canal) }}</p>
                <p class="text-sm text-gray-500">Atendió: {{ $venta->user?->name ?? 'Sin usuario registrado' }}</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded border">Imprimir</button>
                <a href="{{ route('ventas.index') }}" class="px-4 py-2 rounded border">← Volver</a>
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $d)
                    <tr class="border-b">
                        <td class="py-2">{{ $d->producto->nombre }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($d->cantidad, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                        <td class="text-right">${{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 ml-auto w-64 space-y-1">
            <div class="flex justify-between font-bold text-lg"><span>Total</span><span>${{ number_format($venta->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Pago</span><span>${{ number_format($venta->pago, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Cambio</span><span>${{ number_format($venta->cambio, 0, ',', '.') }}</span></div>
        </div>
    </div>
</x-layouts.app>